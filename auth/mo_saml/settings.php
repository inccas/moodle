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

 use auth_mo_saml\admin\setting_button;
 use auth_mo_saml\admin\setting_textonly;
 use auth_mo_saml\admin\setting_idp_metadata;
 use auth_mo_saml\admin\setting_fetch_values;
 use auth_mo_saml\admin\setting_role_mapping;

defined('MOODLE_INTERNAL') || die();


if($hassiteconfig) {

    global $CFG,$OUTPUT;

    $config = get_config('auth_mo_saml');

	if ( empty( $config->apikey ) ) {
		$id     = uniqid();
		$apikey = md5( $id );
		set_config( 'apikey', $apikey, 'auth_mo_saml' );
	}

    require_once($CFG->dirroot.'/auth/mo_saml/locallib.php');

    $settings->add(
        new admin_setting_heading(
            'auth_mo_saml/pluginname', '',
            new lang_string('auth_mo_samldescription', 'auth_mo_saml')
        )
    );

    if ( empty( $config->license_verified ) ) {
		// Login with miniOrange Credentials.

		if ( $data = data_submitted() ) {

			if ( isset( $data->s_auth_mo_saml_adminemail ) ) {
				$config->adminemail = $data->s_auth_mo_saml_adminemail;
			}
			if ( isset( $data->s_auth_mo_saml_password ) ) {
				$config->password = $data->s_auth_mo_saml_password;
			}
			if ( isset( $data->s_auth_mo_saml_license_key ) ) {
				$config->license_key = $data->s_auth_mo_saml_license_key;
			}
			$url      = 'https://login.xecurify.com/moas/rest/customer/key';
			$username = isset( $data->s_auth_mo_saml_adminemail ) ? $config->adminemail : '';
			$password = isset( $data->s_auth_mo_saml_password ) ? $config->password : '';
			$code     = isset( $data->s_auth_mo_saml_license_key ) ? $config->license_key : '';

			$fields = array(
				'email'    => $username,
				'password' => $password,
			);

			$field_string = json_encode( $fields );

			$headers = array(
				'Content-Type: application/json',
				'Authorization: Basic',
			);

			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_URL, $url );

			curl_setopt( $ch, CURLOPT_POST, 1 );

			curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string );

			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

			$response = curl_exec( $ch );
			$response = json_decode( $response );
		//	error_log( 'API CALL - ' . print_r( $response, true ) );
			curl_close( $ch );
			if ( ! isset( $response->status ) ) {
				// $message = get_string( 'mo_api_invalid_customer', 'auth_mo_saml' );
				// echo $OUTPUT->notification( $message );
			} else {
				$content = (array) $response;
				//error_log( 'inside another api call' );
				//error_log( print_r( is_array( $content ), true ) );
				if ( is_array( $content ) ) {

					set_config( 'moapikey', $content['apiKey'], 'auth_mo_saml' );
					set_config( 'moid', $content['id'], 'auth_mo_saml' );
					set_config( 'motoken', $content['token'], 'auth_mo_saml' );


					$valid_license_url = 'https://login.xecurify.com/moas/api/backupcode/verify';

					$currentTimeInMillis = round( microtime( true ) * 1000 );

					$stringToHash = $content['id'] . number_format( $currentTimeInMillis, 0, '', '' ) . $content['apiKey'];
					$hashValue    = hash( 'sha512', $stringToHash );

					$currentTimeInMillis = number_format( $currentTimeInMillis, 0, '', '' );

					$site_url = $CFG->wwwroot;

					$fields = array(
						'code'             => $code,
						'customerKey'      => $content['id'],
						'licenseType'      => 'MOODLE_SAML_SP_PLUGIN',
						'additionalFields' => array(
							'field1' => $site_url,
						),
					);

					$field_string = json_encode( $fields );

					$headers = array(
						'Content-Type: application/json',
						'Customer-Key: ' . $content['id'],
						'Timestamp: ' . $currentTimeInMillis,
						'Authorization: ' . $hashValue,
					);

					$ch = curl_init();

					curl_setopt( $ch, CURLOPT_URL, $valid_license_url );

					curl_setopt( $ch, CURLOPT_POST, 1 );

					curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string );

					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

					curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

					$response = curl_exec( $ch );
					//error_log( 'Another API Response - ' . print_r( $response, true ) );

					if ( curl_errno( $ch ) ) {

						$error = curl_error( $ch );
					} else {
						$response = json_decode( $response );
						if ( $response !== null && strcasecmp( $response->status, 'SUCCESS' ) == 0 ) {
							set_config( 'license_verified', true, 'auth_mo_saml' );
						}
					}
					curl_close( $ch );
				}
			}
		}

        $settings->add(
            new admin_setting_heading(
                'auth_mo_saml/account_info_tab',
                new lang_string('mo_saml_account_info', 'auth_mo_saml'), 
                new lang_string('mo_saml_account_info_desc', 'auth_mo_saml')
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/adminemail',
                get_string('mo_saml_customer_email', 'auth_mo_saml'),
                get_string('mo_saml_customer_email_desc', 'auth_mo_saml'),'', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(
            new admin_setting_configpasswordunmask(
                'auth_mo_saml/password',
                get_string('mo_saml_customer_password', 'auth_mo_saml'),
                get_string('mo_saml_customer_password_desc', 'auth_mo_saml'),'', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/license_key',
                get_string('mo_saml_license_key', 'auth_mo_saml'),
                get_string('mo_saml_license_key_desc', 'auth_mo_saml'),'', PARAM_RAW_TRIMMED
            )
        );

	} 
    else {
        // Service Provider Metadata Tab

        $settings->add(
            new admin_setting_heading(
                'auth_mo_saml/service_provider_metadata',
                new lang_string('mo_saml_service_provider_metadata', 'auth_mo_saml'), new lang_string('mo_saml_service_provider_metadata_desc', 'auth_mo_saml')
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/spentityid',
                get_string('mo_saml_spentityid_name', 'auth_mo_saml'),
                get_string('mo_saml_spentityid_desc', 'auth_mo_saml'), $CFG->wwwroot, PARAM_RAW_TRIMMED
            )
        );

    $settings->add(new setting_textonly(
        'auth_mo_saml/spm_url',
        get_string('mo_saml_spmetadata_url', 'auth_mo_saml'),
        get_string('mo_saml_spmetadata_url_help', 'auth_mo_saml', $CFG->wwwroot . '/auth/mo_saml/serviceprovider/spmetadata.php')
        ));

    $settings->add(new setting_textonly(
        'auth_mo_saml/spm_xml',
        get_string('mo_saml_spmetadata_download', 'auth_mo_saml'),
        get_string('mo_saml_spmetadata_download_help', 'auth_mo_saml', $CFG->wwwroot . '/auth/mo_saml/serviceprovider/spmetadata.php')
    ));

    if(isset($config->spentityid) && !empty($config->spentityid)){
        $spentityid = $config->spentityid;
    }
    else{
        $spentityid = $CFG->wwwroot;
    }

    $settings->add(new setting_textonly(
        'auth_mo_saml/spm_entityid',
        get_string('mo_saml_sp_entityid', 'auth_mo_saml'),
        get_string('mo_saml_sp_entityid_desc', 'auth_mo_saml', $spentityid)
    ));
 
    $settings->add(new setting_textonly(
        'auth_mo_saml/spm_acsurl',
        get_string('mo_saml_acs_url', 'auth_mo_saml'),
        get_string('mo_saml_acs_url_desc', 'auth_mo_saml', $CFG->wwwroot . '/auth/mo_saml/index.php')
    ));

    $settings->add(new setting_textonly(
        'auth_mo_saml/spm_audienceuri',
        get_string('mo_saml_audience_uri', 'auth_mo_saml'),
        get_string('mo_saml_audience_uri_desc', 'auth_mo_saml', $CFG->wwwroot)
    ));

    $settings->add(new setting_textonly(
        'auth_mo_saml/spm_nameidformat',
        get_string('mo_saml_nameid_format', 'auth_mo_saml'),
        get_string('mo_saml_nameid_format_desc', 'auth_mo_saml', $CFG->wwwroot)
    ));

        $settings->add(new setting_textonly(
            'auth_mo_saml/spm_certificatedownload',
            get_string('mo_saml_sp_certificate_download', 'auth_mo_saml'),
            get_string('mo_saml_sp_certificate_download_help', 'auth_mo_saml', $CFG->wwwroot."/auth/mo_saml/resources/sp-certificate.crt")
        ));

        $settings->add(new setting_textonly(
            'auth_mo_saml/spm_logouturl',
            get_string('mo_saml_sp_logout_url', 'auth_mo_saml'),
            get_string('mo_saml_sp_logout_url_desc', 'auth_mo_saml', $CFG->wwwroot . '/auth/mo_saml/logout.php')
        ));


        // Service Provider Setup Tab

        $settings->add(
            new admin_setting_heading(
                'auth_mo_saml/service_provider_setup',
                new lang_string('mo_saml_service_provider_setup', 'auth_mo_saml'), 
                new lang_string('mo_saml_service_provider_setup_desc', 'auth_mo_saml')
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/identityname',
                get_string('mo_saml_idp_name', 'auth_mo_saml'),
                get_string('mo_saml_idp_name_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(new admin_setting_configselect(
            'auth_mo_saml/idpconfigoption',
            get_string('mo_saml_radio_option_label', 'auth_mo_saml'),
            get_string('mo_saml_radio_option_desc', 'auth_mo_saml'),
            'mo_saml_idp_config_option2',
            array(
                'Manual Configuration' => get_string('mo_saml_idp_config_option2', 'auth_mo_saml'),
                'Metadata URL' => get_string('mo_saml_idp_config_option1', 'auth_mo_saml'),
            )
        ));

        $settings->add(
            new setting_idp_metadata(
                'auth_mo_saml/idpmetadata',
                get_string('mo_saml_idp_metadata', 'auth_mo_saml'),
                get_string('mo_saml_idp_metadata_desc', 'auth_mo_saml'), '', PARAM_RAW,80,5
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/samlissuer',
                get_string('mo_saml_idp_entityid', 'auth_mo_saml'),
                get_string('mo_saml_idp_entityid_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(
            new admin_setting_configselect(
                'auth_mo_saml/saml_request_signed',
                get_string('mo_saml_request_signed', 'auth_mo_saml'),
                get_string('mo_saml_request_signed_desc', 'auth_mo_saml'), 'mo_saml_request_signed_option1',
                array(
                    'off' => get_string('mo_saml_request_signed_option1', 'auth_mo_saml'),
                    'on' => get_string('mo_saml_request_signed_option2', 'auth_mo_saml'),
                )
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/loginurl',
                get_string('mo_saml_login_url', 'auth_mo_saml'),
                get_string('mo_saml_login_url_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/logouturl',
                get_string('mo_saml_logout_url', 'auth_mo_saml'),
                get_string('mo_saml_logout_url_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(new admin_setting_configselect(
            'auth_mo_saml/nameidformat', 
            get_string('mo_saml_idp_nameid', 'auth_mo_saml'),
            get_string('mo_saml_idp_nameid_desc', 'auth_mo_saml'),
            'mo_saml_idp_nameid_option1',
            array(
                'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' => get_string('mo_saml_idp_nameid_option1', 'auth_mo_saml'),
                'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified' => get_string('mo_saml_idp_nameid_option2', 'auth_mo_saml'),
                'urn:oasis:names:tc:SAML:2.0:nameid-format:transient' => get_string('mo_saml_idp_nameid_option3', 'auth_mo_saml'),
                'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent' => get_string('mo_saml_idp_nameid_option4', 'auth_mo_saml'),
            )
        ));

        $settings->add(
            new admin_setting_configtextarea(
                'auth_mo_saml/samlxcertificate',
                get_string('mo_saml_x509_certificate', 'auth_mo_saml'),
                get_string('mo_saml_x509_certificate_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
            )
        );

    // $settings->add(new setting_fetch_values(
    //     'auth_mo_saml/certificate_note',
    //     get_string('mo_saml_certificate_note', 'auth_mo_saml'),
    //     get_string('mo_saml_certificate_note_desc', 'auth_mo_saml')
    //     )
    // );

    $settings->add(new setting_fetch_values(
        'auth_mo_saml/testconfiguration',
        get_string('test_configuration', 'auth_mo_saml'),
        get_string('test_configuration_desc', 'auth_mo_saml', $CFG->wwwroot . '/admin/settings.php?section=manageauths')
        )
    );

        // Attribute Mapping

        $settings->add(
            new admin_setting_heading(
                'auth_mo_saml/attribute_mapping',
                new lang_string('mo_saml_attribute_mapping', 'auth_mo_saml'), new lang_string('mo_saml_attribute_mapping_desc', 'auth_mo_saml')
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/usernamemap',
                get_string('mo_saml_username', 'auth_mo_saml'),
                get_string('mo_saml_username_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(
            new admin_setting_configtext(
                'auth_mo_saml/emailmap',
                get_string('mo_saml_email', 'auth_mo_saml'),
                get_string('mo_saml_email_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
            )
        );

        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/firstnamemap',
            get_string('mo_saml_firstname', 'auth_mo_saml'),
            get_string('mo_saml_firstname_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/lastnamemap',
            get_string('mo_saml_lastname', 'auth_mo_saml'),
            get_string('mo_saml_lastname_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/institutionmap',
            get_string('mo_saml_institution', 'auth_mo_saml'),
            get_string('mo_saml_institution_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/deptmap',
            get_string('mo_saml_department', 'auth_mo_saml'),
            get_string('mo_saml_department_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/phonemap',
            get_string('mo_saml_phone', 'auth_mo_saml'),
            get_string('mo_saml_phone_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/addressmap',
            get_string('mo_saml_address', 'auth_mo_saml'),
            get_string('mo_saml_address_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/idnumbermap',
            get_string('mo_saml_id_number', 'auth_mo_saml'),
            get_string('mo_saml_id_number_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        $settings->add(
            new admin_setting_heading(
                'auth_mo_saml/custom_attribute_mapping',
                new lang_string('mo_saml_custom_attribute_mapping', 'auth_mo_saml'), 
                new lang_string('mo_saml_custom_attribute_mapping_desc', 'auth_mo_saml')
            )
        );


        $custom_attributes = $DB->get_records( 'user_info_field' );
		foreach ( $custom_attributes as $attribute ) {
			$shortname = $attribute->shortname;
            $longname = $attribute->name;
			$settings->add(
				new admin_setting_configtext(
					'auth_mo_saml/' . $shortname.'map',
					$longname,
					'',
					'',
					PARAM_RAW_TRIMMED
				)
			);
		}

        // Role Mapping

        $settings->add(
            new admin_setting_heading(
                'auth_mo_saml/role_mapping',
                new lang_string('mo_saml_role_mapping', 'auth_mo_saml'), 
                new lang_string('mo_saml_role_mapping_desc', 'auth_mo_saml')
            )
        );

        $roles = get_all_roles();

        $syscontext = context_system::instance();
        list($sysconxroles, $assigncounts, $nameswithcounts) = get_assignable_roles($syscontext, ROLENAME_BOTH, true);

        $default_role_array = array(
            'user' => 'Authenticated User',
        );

        foreach ( $roles as $role ){
            if(isset($sysconxroles[$role->id])) {
                $default_role_array[$role->shortname] = $sysconxroles[$role->id];
            }
        }

        $default_role = new admin_setting_configselect(
            'auth_mo_saml/defaultrolemap',
            get_string('mo_saml_default_role', 'auth_mo_saml'),
            get_string('mo_saml_default_role_desc', 'auth_mo_saml'),
            'Authenticated User',
            $default_role_array
        );
        $settings->add($default_role);
        
        $settings->add(new admin_setting_configtext(
            'auth_mo_saml/roleattribute',
            get_string('mo_saml_idp_group', 'auth_mo_saml'),
            get_string('mo_saml_idp_group_desc', 'auth_mo_saml'), '', PARAM_RAW_TRIMMED
        ));

        set_config('sysconxroles', json_encode($sysconxroles), 'auth_mo_saml');

        foreach ( $roles as $role ) {
            if(isset($sysconxroles[$role->id])){
                $shortname = $role->shortname;
                $settings->add(
                    new admin_setting_configtext(
                       'auth_mo_saml/' . $shortname.'map',
                       $sysconxroles[$role->id],
                        '',
                        '',
                        PARAM_RAW_TRIMMED
                    )
                );
            }
        }

        $settings->add(
            new admin_setting_heading(
                'auth_mo_saml/enable_redirect',
                new lang_string('mo_saml_enable_redirect', 'auth_mo_saml'), 
                new lang_string('mo_saml_enable_redirect_desc', 'auth_mo_saml')
            )
        );


        $settings->add(
            new admin_setting_configselect(
                'auth_mo_saml/enableloginredirect',
                get_string('mo_saml_enable_login_redirect', 'auth_mo_saml'),
                get_string('mo_saml_enable_login_redirect_desc', 'auth_mo_saml'), 'mo_saml_request_signed_option1',
                array(
                    'No' => get_string('mo_saml_enable_login_redirect_option1', 'auth_mo_saml'),
                    'Yes' => get_string('mo_saml_enable_login_redirect_option2', 'auth_mo_saml'),
                )
            )
        );

        $settings->add(new setting_textonly(
            'auth_mo_saml/backdoor_url',
            get_string('mo_saml_backdoor_url', 'auth_mo_saml'),
            get_string('mo_saml_backdoor_url_desc', 'auth_mo_saml', $CFG->wwwroot . '/login/index.php?saml_sso=false')
        ));
	
    $settings->add(new setting_role_mapping(
        'auth_mo_saml/support',
        get_string('mo_saml_support_email', 'auth_mo_saml'),
        get_string('mo_saml_support_email_desc', 'auth_mo_saml')
        )
    );

    }

}