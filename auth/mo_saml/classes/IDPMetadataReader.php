<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   auth_mo_saml
 * @copyright   2020  miniOrange
 * @category    document
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 */

 namespace auth_mo_saml;

 use DomDocument;
 use DOMElement;
use DOMNodeList;
use DOMXPath;


global $CFG;

 require_once($CFG->dirroot.'/auth/mo_saml/utilities.php');

 class IDPMetadataReader{

	private $identityProviders;
	private $serviceProviders;

	public function __construct(DOMDocument $xml = NULL){

		$this->identityProviders = array();
		$this->serviceProviders = array();

		$entityDescriptors = $this->xpQuery($xml, './saml_metadata:EntityDescriptor');

		//print_r($entityDescriptors);exit;
        
		foreach ($entityDescriptors as $entityDescriptor) {
			$idpSSODescriptor = $this->xpQuery($entityDescriptor, './saml_metadata:IDPSSODescriptor');
			
			if(isset($idpSSODescriptor) && !empty($idpSSODescriptor)){
				array_push($this->identityProviders,new IdentityProviders($entityDescriptor));
			}
		}
	}

    public function xpquery($node, $query) {
        $xpcache = null;

        if ($node instanceof DOMDocument) {
            $doc = $node;
        } else {
            $doc = $node->ownerDocument;
        }

        if ($xpcache === null || !$xpcache->document->isSameNode($doc)) {
            $xpcache = new DOMXPath($doc);
            $xpcache->registerNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpcache->registerNamespace('saml_protocol', 'urn:oasis:names:tc:SAML:2.0:protocol');
            $xpcache->registerNamespace('saml_assertion', 'urn:oasis:names:tc:SAML:2.0:assertion');
            $xpcache->registerNamespace('saml_metadata', 'urn:oasis:names:tc:SAML:2.0:metadata');
            $xpcache->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $xpcache->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');
        }

        $results = $xpcache->query($query, $node);
        $ret = array();
        for ($i = 0; $i < $results->length; $i++) {
            $ret[$i] = $results->item($i);
        }

        return $ret;
    }


	public function getIdentityProviders(){
		return $this->identityProviders;
	}

	public function getServiceProviders(){
		return $this->serviceProviders;
	}

}

class IdentityProviders{

	private $idpName;
	private $entityID;
	private $loginDetails;
	private $logoutDetails;
	private $signingCertificate;
	private $encryptionCertificate;
	private $signedRequest;

	public function __construct(DOMElement $xml = NULL){

		$this->idpName = '';
		$this->loginDetails = array();
		$this->logoutDetails = array();
		$this->signingCertificate = array();
		$this->encryptionCertificate = array();

		if ($xml->hasAttribute('entityID')) {
            $this->entityID = $xml->getAttribute('entityID');
        }

        if($xml->hasAttribute('WantAuthnRequestsSigned')){
        	$this->signedRequest = $xml->getAttribute('WantAuthnRequestsSigned');
        }

        $idpSSODescriptor = $this->xpQuery($xml, './saml_metadata:IDPSSODescriptor');

        if (count($idpSSODescriptor) > 1) {
            throw new Exception('More than one <IDPSSODescriptor> in <EntityDescriptor>.');
        } elseif (empty($idpSSODescriptor)) {
            throw new Exception('Missing required <IDPSSODescriptor> in <EntityDescriptor>.');
        }

        $idpSSODescriptorEL = $idpSSODescriptor[0];

        $info = $this->xpQuery($xml, './saml_metadata:Extensions');
        
        if($info)
        	$this->parseInfo($idpSSODescriptorEL);
        $this->parseSSOService($idpSSODescriptorEL);
        $this->parseSLOService($idpSSODescriptorEL);
        $this->parsex509Certificate($idpSSODescriptorEL);

	}

	private function parseInfo($xml){
		$displayNames = $this->xpQuery($xml, './mdui:UIInfo/mdui:DisplayName');
		foreach ($displayNames as $name) {
			if($name->hasAttribute('xml:lang') && $name->getAttribute('xml:lang')=="en"){
				$this->idpName = $name->textContent;
			}
		}
	}

	private function parseSSOService($xml){
		$ssoServices = $this->xpQuery($xml, './saml_metadata:SingleSignOnService');
		foreach ($ssoServices as $ssoService) {
			$binding = str_replace("urn:oasis:names:tc:SAML:2.0:bindings:","",$ssoService->getAttribute('Binding'));
	        $this->loginDetails = array_merge( 
	        	$this->loginDetails, 
	        	array($binding => $ssoService->getAttribute('Location')) 
	        );
	    }
	}

	private function parseSLOService($xml){
		$sloServices = $this->xpQuery($xml, './saml_metadata:SingleLogoutService');
		if(!empty($sloServices)){
			foreach ($sloServices as $sloService) {
				$binding = str_replace("urn:oasis:names:tc:SAML:2.0:bindings:","",$sloService->getAttribute('Binding'));
	    	    $this->logoutDetails = array_merge( 
	        		$this->logoutDetails, 
	        		array($binding => $sloService->getAttribute('Location')) 
	        	);
			}
		} else {
			$this->logoutDetails = array('HTTP-Redirect' => '');
		}
	}

	private function parsex509Certificate($xml){
		foreach ( $this->xpQuery($xml, './saml_metadata:KeyDescriptor') as $KeyDescriptorNode ) {
			if($KeyDescriptorNode->hasAttribute('use')){
				if($KeyDescriptorNode->getAttribute('use')=='encryption'){
					$this->parseEncryptionCertificate($KeyDescriptorNode);
				}else{
					$this->parseSigningCertificate($KeyDescriptorNode);
				}
			}else{
				$this->parseSigningCertificate($KeyDescriptorNode);
			}
		}
	}

	private function parseSigningCertificate($xml){
		$certNode = $this->xpQuery($xml, './ds:KeyInfo/ds:X509Data/ds:X509Certificate');
		$certData = trim($certNode[0]->textContent);
		$certData = str_replace(array ( "\r", "\n", "\t", ' '), '', $certData);
		if(!empty($certNode))
			array_push($this->signingCertificate, $this->sanitize_certificate( $certData ));
	}

    public function sanitize_certificate( $certificate ) {
        $certificate = preg_replace("/[\r\n]+/", '', $certificate);
        $certificate = str_replace( "-", '', $certificate );
        $certificate = str_replace( "BEGIN CERTIFICATE", '', $certificate );
        $certificate = str_replace( "END CERTIFICATE", '', $certificate );
        $certificate = str_replace( " ", '', $certificate );
        $certificate = chunk_split($certificate, 64, "\r\n");
        $certificate = "-----BEGIN CERTIFICATE-----\r\n" . $certificate . "-----END CERTIFICATE-----";
        return $certificate;
    }


	private function parseEncryptionCertificate($xml){
		$certNode = $this->xpQuery($xml, './ds:KeyInfo/ds:X509Data/ds:X509Certificate');
		$certData = trim($certNode[0]->textContent);
		$certData = str_replace(array ( "\r", "\n", "\t", ' '), '', $certData);
		if(!empty($certNode))
			array_push($this->encryptionCertificate, $certData);
	}

    public function xpquery($node, $query) {
        $xpcache = null;

        if ($node instanceof DOMDocument) {
            $doc = $node;
        } else {
            $doc = $node->ownerDocument;
        }

        if ($xpcache === null || !$xpcache->document->isSameNode($doc)) {
            $xpcache = new DOMXPath($doc);
            $xpcache->registerNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpcache->registerNamespace('saml_protocol', 'urn:oasis:names:tc:SAML:2.0:protocol');
            $xpcache->registerNamespace('saml_assertion', 'urn:oasis:names:tc:SAML:2.0:assertion');
            $xpcache->registerNamespace('saml_metadata', 'urn:oasis:names:tc:SAML:2.0:metadata');
            $xpcache->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $xpcache->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');
        }

        $results = $xpcache->query($query, $node);
        $ret = array();
        for ($i = 0; $i < $results->length; $i++) {
            $ret[$i] = $results->item($i);
        }

        return $ret;
    }


	public function getIdpName(){
		return $this->idpName;
	}

	public function getEntityID(){
		return $this->entityID;
	}

	public function getLoginURL($binding){
		return $this->loginDetails[$binding];
	}

	public function getLogoutURL($binding){
		return $this->logoutDetails[$binding];
	}

	public function getLoginDetails(){
		return $this->loginDetails;
	}

	public function getLogoutDetails(){
		return $this->logoutDetails;
	}

	public function getSigningCertificate(){
		return $this->signingCertificate;
	}

	public function getEncryptionCertificate(){
		return $this->encryptionCertificate[0];
	}

	public function isRequestSigned(){
		return $this->signedRequest;
	}

}

class ServiceProviders{
	//TODO
}
