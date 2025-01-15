<?php
// This file is part of miniOrange moodle plugin - http://moodle.org/
//
// This Plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This Program is distributed in the hope that it will be useful,
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

 require_once('utilities.php');

 function mo_saml_miniorange_generate_metadata($download=false) {
    
    global $CFG;
    $config = get_config('auth_mo_saml');
    if(isset($config->spentityid) && !empty($config->spentityid)){
      $entity_id = $config->spentityid;
    }
    else{
      $entity_id = $CFG->wwwroot;
    }
    $acs_url = $CFG->wwwroot."/auth/mo_saml/index.php";
    $slo_url = $CFG->wwwroot."/auth/mo_saml/logout.php";
    if (!isset($config->saml_request_signed)) {
      $config->saml_request_signed = '';
    }

    $public_certificate = get_config('auth_mo_saml', 'public_certificate');
    $validUntilDate     = utilities::getValidUntilDateFromCert($public_certificate);
    $certificate        = utilities::desanitize_certificate($public_certificate);

    if (!(isset($config->nameidformat))) {
      $nameidformat="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress";
    } else {
      $nameidformat =$config->nameidformat;
    }

    if(ob_get_contents())
        ob_clean();
    header( 'Content-Type: text/xml' );
    if($download){
            header('Content-Disposition: attachment; filename="Metadata.xml"');
    }
    echo '<?xml version="1.0"?>
<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" validUntil="'. $validUntilDate .'" cacheDuration="PT1446808792S" entityID="' . $entity_id . '">
  <md:SPSSODescriptor AuthnRequestsSigned="';
  if ($config->saml_request_signed == 'on') {
      echo 'true';
  }
  else
  {
      echo 'false';
  }
echo '" WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <md:KeyDescriptor use="signing">
            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                <ds:X509Data>
                    <ds:X509Certificate>' . $certificate . '</ds:X509Certificate>
                </ds:X509Data>
            </ds:KeyInfo>
        </md:KeyDescriptor>
        <md:KeyDescriptor use="encryption">
            <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                <ds:X509Data>
                    <ds:X509Certificate>' . $certificate . '</ds:X509Certificate>
                </ds:X509Data>
            </ds:KeyInfo>
        </md:KeyDescriptor>
        <md:NameIDFormat>'.$nameidformat.'</md:NameIDFormat>
        <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . $slo_url . '"/>
        <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="' . $slo_url . '"/>
        <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . $acs_url . '" index="1"/>
  </md:SPSSODescriptor>
  <md:Organization>
    <md:OrganizationName xml:lang="en-US">miniOrange</md:OrganizationName>
    <md:OrganizationDisplayName xml:lang="en-US">miniOrange</md:OrganizationDisplayName>
    <md:OrganizationURL xml:lang="en-US">https://www.miniorange.com</md:OrganizationURL>
  </md:Organization>
  <md:ContactPerson contactType="technical">
    <md:GivenName>miniOrange</md:GivenName>
    <md:EmailAddress>info@xecurify.com</md:EmailAddress>
  </md:ContactPerson>
  <md:ContactPerson contactType="support">
    <md:GivenName>miniOrange</md:GivenName>
    <md:EmailAddress>info@xecurify.com</md:EmailAddress>
  </md:ContactPerson>
</md:EntityDescriptor>';
    exit;

}
