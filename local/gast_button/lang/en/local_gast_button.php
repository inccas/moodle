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

$string['pluginname'] = 'g.a.s.t. button';
$string['idp'] = 'BTU IDP URL';
$string['idp_desc'] = 'BTU IDP URL for preselection in DFN AAI.

live: https://www.b-tu.de/idp/shibboleth

test: https://www.b-tu.de/idp-dev/shibboleth';
$string['saml'] = 'g.a.s.t. SAML URL';
$string['saml_desc'] = 'DFN AAI g.a.s.t. service url.

live: https://api.gast.de/auth/saml/login

test: https://apprex.gast.de/gast-service-auth/saml/login';
$string['targetUrl'] = 'target URL';
$string['targetUrl_desc'] = 'target url for sending to g.a.s.t.';
$string['secret'] = 'secret';
$string['secret_desc'] = 'secret for JWT signature';
$string['salt'] = 'salt';
$string['salt_desc'] = 'salt for obfuscating internal user ids, when sending them to g.a.s.t. Should not be changed after initial configuration!';
$string['shortcode_gast_button'] = 'create a button that redirects users to the g.a.s.t. platforms';
$string['shortcode_gast_button_help'] = '
The following attributes may be used:

- `productId` (optional) specifies the g.a.s.t. products to redirect to. Defaults to 4 (DUO)

Example:

[gastbutton]Button text[/gastbutton]
[gastbutton productId="4"]Button text[/gastbutton]
';
