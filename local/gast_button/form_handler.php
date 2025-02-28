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

require_once('../../config.php');
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/vendor/autoload.php');

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Spatie\Url\Url;

throw_moodle_exception_if_request_method_is_not_post();
if (is_any_required_parameter_empty()) {
    if (is_at_least_return_url_set()) {
        throw new moodle_exception("parameters_invalid", "local_gast_button", $_POST['returnUrl']);
    }
    throw new moodle_exception("parameters_invalid", "local_gast_button");
}
validate_post_parameters();
list($secret, $targetUrl) = load_configuration();
perform_jwt_auth($secret, $targetUrl);
exit(0);

function perform_jwt_auth($secret, $targetUrl) {
    $token = create_json_web_token($secret);
    $curlhandle = create_and_initialize_curl_session($targetUrl, $token);
    list($response, $httpstatuscode) = execute_curl_request($curlhandle);
    $redirecturl = process_response($response, $httpstatuscode);
    header("Location: $redirecturl");
}

function send_to_sso($secret, $gastsaml, $btuidp) {
    $targeturl = $_POST['targetUrl'];
    $token = create_json_web_token($secret);

    $url = Url::fromString($targeturl);
    $url = $url->withQueryParameter('token', $token);

    $redirecturl = Url::fromString($gastsaml);
    $redirecturl = $redirecturl->withQueryParameter('RelayState', $url);
    $redirecturl = $redirecturl->withQueryParameter('idp', $btuidp);

    header("Location: $redirecturl");
}

function check_auth_method(): bool {
    global $USER;

    return in_array($USER->auth, ['ldap', 'cas']);
}

/**
 * @throws moodle_exception
 */
function throw_moodle_exception_if_request_method_is_not_post() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new moodle_exception("form_handler.php called with wrong method. Method: " .
            $_SERVER['REQUEST_METHOD'], 'local_gast_button');
    }
}

function is_any_required_parameter_empty(): bool {
    return empty($_POST['productId'])
        || empty($_POST['clientId'])
        || empty($_POST['userId'])
        || empty($_POST['returnUrl'])
        || empty($_POST['targetUrl']);
}

function is_at_least_return_url_set(): bool {
    return !empty($_POST['returnUrl']);
}

/**
 * @throws moodle_exception
 */
function validate_post_parameters() {
    global $CFG;
    if (is_url_valid($_POST['returnUrl']) === false) {
        throw new moodle_exception("form_handler.php called with invalid parameters (1)", 'local_gast_button');
    }
    if (intval($_POST['productId']) !== $CFG->gast_button['productId']) {
        throw new moodle_exception("form_handler.php called with invalid parameters (2)", 'local_gast_button', $_POST['returnUrl']);
    }
    if (intval($_POST['clientId']) !== $CFG->gast_button['clientId']) {
        throw new moodle_exception("form_handler.php called with invalid parameters (3)", 'local_gast_button', $_POST['returnUrl']);
    }
    if (is_url_valid($_POST['targetUrl']) === false) {
        throw new moodle_exception("form_handler.php called with invalid parameters (4)", 'local_gast_button', $_POST['returnUrl']);
    }
}

function is_url_valid($url): bool {
    $urlarray = parse_url($url);
    return $urlarray !== false;
}

/**
 * @throws moodle_exception
 */
function load_configuration(): array {
    global $CFG;
    try {
//    $secretbase64 = get_config('local_gast_button', 'secret');
//    $secret = base64_decode($secretbase64);
//    $targeturl = get_config('local_gast_button', 'targetUrl');
//    $gastsaml = get_config('local_gast_button', 'saml');
//    $btuidp = get_config('local_gast_button', 'idp');
//    return [$secret, $targeturl, $gastsaml, $btuidp];

        $activeEnv = $CFG->gast_button['activeenv'] ?? 'test';
//        $secret = $CFG->gast_button['env'][$activeEnv]['secretkey'];
        $secretBase64 = $CFG->gast_button['env'][$activeEnv]['secretkey'];
        $secret = base64_decode($secretBase64);
        $targetUrl = $CFG->gast_button['env'][$activeEnv]['url'];
        return [$secret, $targetUrl];
    } catch (dml_exception $e) {
        throw new moodle_exception("configuration_invalid. Message: " . $e->getMessage(), 'local_gast_button', $_POST['returnUrl']);
    }
}

function create_json_web_token($secret): Token {
    $now = new DateTimeImmutable();
    return (new Builder())
        // ->setIssuer('https://www.b-tu.de')
        // ->setAudience($targeturl)
        ->setIssuedAt($now->getTimestamp())
        // ->setExpiration($now->modify('+10 minutes')->getTimestamp())
        ->set('productId', $_POST['productId'])
        ->set('clientId', $_POST['clientId'])
        ->set('userId', $_POST['userId'])
        ->sign(new Sha256(), $secret)
        ->getToken();
}

function create_and_initialize_curl_session($targeturl, $token) {
    $curlhandle = curl_init();
    curl_setopt($curlhandle, CURLOPT_URL, $targeturl);
    curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curlhandle, CURLOPT_POST, true);
    curl_setopt($curlhandle, CURLOPT_POSTFIELDS, []);

    $headers = [
        "Authorization: Bearer $token",
    ];
    curl_setopt($curlhandle, CURLOPT_HTTPHEADER, $headers);

    return $curlhandle;
}

function execute_curl_request($curlhandle): array {
    $response = curl_exec($curlhandle);
    $httpstatuscode = curl_getinfo($curlhandle, CURLINFO_RESPONSE_CODE);
    curl_close($curlhandle);
    return array($response, $httpstatuscode);
}

/**
 * @throws moodle_exception
 */
function process_response($response, $httpstatuscode) {
    if ($httpstatuscode === 0) {
        throw new moodle_exception("Der Partnerserver antwortet nicht.", 'local_gast_button', $_POST['returnUrl']);
    }
    if ($httpstatuscode !== 200) {
        throw new moodle_exception(
            "Der Partnerserver hat mit einem Fehler geantwortet: " . $httpstatuscode,
            'local_gast_button',
            $_POST['returnUrl']
        );
    }
    if ($response === false) {
        throw new moodle_exception(
            "Der Partnerserver hat eine ungültige Antwort geschickt (1).",
            'local_gast_button',
            $_POST['returnUrl']
        );
    }
    if (empty($response)) {
        throw new moodle_exception(
            "Der Partnerserver hat eine ungültige Antwort geschickt (2).",
            'local_gast_button',
            $_POST['returnUrl']
        );
    }
    $response = json_decode($response);
    if ($response === null) {
        throw new moodle_exception(
            "Der Partnerserver hat eine ungültige Antwort geschickt (3).",
            'local_gast_button',
            $_POST['returnUrl']
        );
    }
    if (empty($response->redirectUrl)) {
        throw new moodle_exception(
            "Der Partnerserver hat eine ungültige Antwort geschickt (4).",
            'local_gast_button',
            $_POST['returnUrl']
        );
    }
    return $response->redirectUrl;
}

