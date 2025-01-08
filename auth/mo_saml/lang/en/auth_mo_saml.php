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
global $CFG;

require_once($CFG->dirroot.'/auth/mo_saml/utilities.php');

$string['auth_mo_samltitle'] = 'miniOrange SAML SSO for moodle';
$string['auth_mo_samldescription'] = 'miniOrange SAML 2.0 Single Sign On (SSO) Plugin enables seamless SSO login into your Moodle sites via authentication through any SAML 2.0 compliant Identity Provider.';

$string['mo_saml_account_info'] = 'Account Setup';
$string['mo_saml_account_info_desc'] = "Login with miniOrange credentials and enter the license key to activate the Plugin. ";
$string['mo_saml_customer_email'] = 'Email';
$string['mo_saml_customer_email_desc'] = "";
$string['mo_saml_customer_password'] = 'Password';
$string['mo_saml_customer_password_desc'] = "";
$string['mo_saml_license_key'] = 'License Key';
$string['mo_saml_license_key_desc'] = "<a href='https://login.xecurify.com/moas/admin/customer/viewlicensekeys' target='_blank'>Click here to view the license keys.</a>";

$string['mo_saml_service_provider_metadata'] = 'Service Provider Metadata';
$string['mo_saml_service_provider_metadata_desc'] = "For configuring Moodle on your IDP, you have three options:";
$string['mo_saml_spentityid_name'] = "SP Entity-ID";
$string['mo_saml_spentityid_desc'] = "If you have already shared the below URLs or Metadata with your IDP, do <b>NOT</b> change SP EntityID. It might break your existing login flow.";
$string['mo_saml_spmetadata_url'] = 'SP Metadata URL';
$mo_saml_sp_metadata_url = $CFG->wwwroot."/auth/mo_saml/serviceprovider/spmetadata.php";
$string['mo_saml_spmetadata_url_help'] = '<a href=\'{$a}\' target="_blank">'. $mo_saml_sp_metadata_url .'</a>
<p>You can provide the metadata URL to your Identity Provider.</p><p>----------------------------------------------- OR -----------------------------------------------</p>';
$string['mo_saml_spmetadata_download'] = 'Download SP Metadata';
$string['mo_saml_spmetadata_download_help'] = '<a href=\'{$a}?download=1\'>Download Service Provider Metadata</a>
<p>You can download the plugin XML metadata and upload it on your Identity Provider.</p><p>----------------------------------------------- OR -----------------------------------------------</p>';

$string['mo_saml_sp_entityid'] = 'SP Entity-ID'; 
$string['mo_saml_sp_entityid_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';
$string['mo_saml_acs_url'] = 'ACS URL';
$string['mo_saml_acs_url_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';
$string['mo_saml_audience_uri'] = 'Audience URI';
$string['mo_saml_audience_uri_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';
$string['mo_saml_nameid_format'] = 'NameID Format';
$string['mo_saml_nameid_format_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</p>';
$string['mo_saml_sp_certificate_download'] = 'Certificate(Optional)';
$mo_saml_cert_location = $CFG->dirroot."/auth/mo_saml/resources/sp-certificate.crt";
$mo_saml_cert_file = file_get_contents( $mo_saml_cert_location );
$mo_saml_valid_until_date = utilities::getValidDateFromCert($mo_saml_cert_file);

$string['mo_saml_sp_certificate_download_help'] = '<a href=\'{$a}?download=1\'>Download</a> <br/> <p> Certificate Expiry Date :  ' . $mo_saml_valid_until_date . '  </p> ';
$string['mo_saml_sp_logout_url'] = 'Single Logout URL';
$string['mo_saml_sp_logout_url_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';

$string['mo_saml_service_provider_setup'] = 'Service Provider Setup';
$string['mo_saml_service_provider_setup_desc'] = "To configure IDP metadata, you have two options: you can either fetch the metadata URL or XML directly in the IDP Metadata textbox, or manually configure the values.";
$string['mo_saml_idp_name'] = 'IDP Name';
$string['mo_saml_idp_name_desc'] = 'Identity Provider Name like Azure, Okta, Salesforce';
$string['mo_saml_radio_option_label'] = 'Select the Method';
$string['mo_saml_radio_option_desc'] = 'Select the option how you would like to save the IDP configuration';
$string['mo_saml_idp_config_option1'] = 'Metadata URL/XML';
$string['mo_saml_idp_config_option2'] = 'Manual Configuration';
$string['mo_saml_idp_metadata'] = 'IDP Metadata URL/XML';
$string['mo_saml_idp_metadata_desc'] = "----------------------------------------------- OR -----------------------------------------------";
$string['mo_saml_idp_entityid'] = 'IDP Entity-ID';
$string['mo_saml_idp_entityid_desc'] = 'Identity Provider Entity-ID or Issuer';
$string['mo_saml_request_signed'] = 'Sign SSO & SLO Requests';
$string['mo_saml_request_signed_desc'] = 'Change this option to send Signed SSO and SLO requests.';
$string['mo_saml_request_signed_option1'] = 'off';
$string['mo_saml_request_signed_option2'] = 'on';
$string['mo_saml_login_url'] = 'SAML Login URL';
$string['mo_saml_login_url_desc'] = 'Single Sign-On Service URL of your IDP';
$string['mo_saml_x509_certificate'] = 'X.509 certificate';
$string['mo_saml_x509_certificate_desc'] = '<tr>
<td></td>
<td><b>NOTE:</b> Format of the certificate:<br/>-----BEGIN CERTIFICATE-----<br/>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br/>-----END CERTIFICATE-----<br/></td>
</tr>';

$string['mo_saml_logout_url'] = 'Logout URL';
$string['mo_saml_logout_url_desc'] = 'Single Logout Service URL of your IDP';

$string['mo_saml_idp_nameid'] = 'NameID Format';
$string['mo_saml_idp_nameid_desc'] = '';
$string['mo_saml_idp_nameid_option1'] = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
$string['mo_saml_idp_nameid_option2'] = 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified';
$string['mo_saml_idp_nameid_option3'] = 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient';
$string['mo_saml_idp_nameid_option4'] = 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent';


$string['test_configuration'] = 'Test Configuration';
$string['test_configuration_desc'] = '<li><a href=\'{$a}\' target="_blank"> Click here</a> to go to <b>Manage authentication</b> page.</li>
<li>Enable the plugin by clicking on the <i class="fa fa-eye"></i> icon next to the plugins name under the Enable column.</li>
<li> Click on the <b>Test Settings</b> option';

$string['mo_saml_attribute_mapping'] = 'Attribute Mapping';
$string['mo_saml_attribute_mapping_desc'] = "The Attribute Mapping feature helps you to map the user attributes sent by the IDP to the Moodle user attributes.";
$string['mo_saml_username'] = 'Username';
$string['mo_saml_username_desc'] = '';
$string['mo_saml_email'] = 'Email';
$string['mo_saml_email_desc'] = '';
$string['mo_saml_firstname'] = 'Firstname';
$string['mo_saml_firstname_desc'] = '';
$string['mo_saml_lastname'] = 'Lastname';
$string['mo_saml_lastname_desc'] = '';
$string['mo_saml_phone'] = 'Phone';
$string['mo_saml_phone_desc'] = '';
$string['mo_saml_department'] = 'Department';
$string['mo_saml_department_desc'] = '';
$string['mo_saml_institution'] = 'Institution';
$string['mo_saml_institution_desc'] = '';
$string['mo_saml_address'] = 'Address';
$string['mo_saml_address_desc'] = '';
$string['mo_saml_id_number'] = 'ID Number';
$string['mo_saml_id_number_desc'] = '';
$string['mo_saml_custom_attribute_mapping'] = 'Custom Attribute Mapping';
$string['mo_saml_custom_attribute_mapping_desc'] = 'To create a custom attribute in Moodle, go to <b>Site Administration > Users > User Profile Fields</b>. Create a new Text Input field. Provide a short name and name.';

$string['mo_saml_role_mapping'] = 'Role Mapping';
$string['mo_saml_role_mapping_desc'] = "The Role Mapping allows you to provide user capabilities based on their IDP attribute group values.<br><b>Note:</b> Role Mapping is only applicable for roles defined in the System context.";
$string['mo_saml_default_role'] = 'Default Role';
$string['mo_saml_default_role_desc'] = 'You can assign a default role to the users.';
$string['mo_saml_default_role_option1']= 'Authenticated user';
$string['mo_saml_idp_group']= 'Group Attribute from IDP ';
$string['mo_saml_idp_group_desc']= '';
$string['mo_saml_individual_role_desc']= '';
$string['mo_saml_default_role_option2']= 'Manager';
$string['mo_saml_default_role_option3']= 'Course Creator';
$string['mo_saml_default_role_option4']= 'Teacher';
$string['mo_saml_default_role_option5']= 'Non-editing teacher';
$string['mo_saml_default_role_option6']= 'Student';

$string['mo_saml_enable_redirect'] = 'Redirection and SSO Links';
$string['mo_saml_enable_redirect_desc'] = "General settings";


$string['mo_saml_enable_login_redirect'] = 'Enable Auto-redirect to IDP from Moodle login page';
$string['mo_saml_enable_login_redirect_desc'] = 'Check this option to redirect to IDP Login Page from Moodle Login Page.';
$string['mo_saml_enable_login_redirect_option1'] = 'No';
$string['mo_saml_enable_login_redirect_option2'] = 'Yes';

$string['mo_saml_backdoor_url'] = 'Backdoor URL';
$string['mo_saml_backdoor_url_desc'] = '<p style="margin-top:-15px;margin-bottom:25px;">{$a}</p>';

$string['mo_saml_support_email'] = 'Contact us';
$string['mo_saml_support_email_desc'] = 'If you are facing any issues or would like to know about our other Products, you can reach out to us at <a href = "mailto: moodlesupport@xecurify.com">moodlesupport@xecurify.com</a>.';


$string['auth_mo_saml_create_or_update_warning'] = "When auto-provisioning or auto-update is enable,";
$string['auth_mo_saml_empty_required_value'] = "is a required attribute, provide a valid value";
$string['retriesexceeded'] = 'Maximum number of SAML connection retries exceeded  - there must be a problem with the Identity Service.<br />Please try again in a few minutes.';
$string['pluginauthfailed'] = 'The miniOrange SAML authentication plugin failed - user $a disallowed (no user auto-creation?) or dual login disabled.';
$string['pluginauthfailedusername'] = 'The miniOrange SAML authentication plugin failed - user $a disallowed due to invalid username format.';
$string['auth_mo_saml_username_email_error'] = 'The identity provider returned a set of data that does not contain the SAML username/email mapping field. Once of this field is required to login. <br />Please check your Username/Email Address Attribute Mapping configuration.';
$string['pluginname'] = 'miniOrange SAML 2.0 SSO';
