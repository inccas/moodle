copyright  2018 Dorel Manolescu
author     Dorel Manolescu manolescu.dorel@gmail.com
license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later


CHANGES

CHANGES
version 2018041344:

The first iteration of the plugin. Requires Moodle 3.5

ABOUT

This plugin for Moodle allows calling the privacy api user data deletion via a web-service call.

The plugin uses moodle web-service layer and the web-service call is the same as for any other moodle web-services.
(example: https://yoursite/webservice/rest/server.php?wstoken=opensesame&wsfunction=local_gdpr_deleteuserdata_single
&moodlewsrestformat=json&parameters[userid]=23)

The web-service call takes as parameter the  moodle userid. It requires : moodle/user:delete permission.
Advisable steps to set up the web-service:
1) create a blank new role at the system level;
2) add moodle/user:delete permission;
3) create a user that will be able to run this web-service;
4) add the user to the role created in step 1;
5) create a token for our new user/gdpr_deleteuserdata web-service.


In case there is a need to add the web-service as core web-service this line needs to be added:
'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
in services.php

SETTINGS

This local plugin allows you to configure: nothing for the moment.


INSTALLATION

Just place the gdpr_deleteuserdata directory inside your Moodle's local directory.
Install the plugin and browse to:

Site Administration->Plugins->Local plugins->GDPR delete userdata
After installation, the new service should be visible under: admin/settings.php?section=externalservices
