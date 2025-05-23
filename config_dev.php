<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodle';
$CFG->dbpass    = '0gmVFnxfkj3@!vxLjsgB';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => false,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
#  'dbssl_ca' => '/etc/ssl/certs/Baltimore_CyberTrust_Root.pem',
#  'dbsslca' => '/etc/ssl/certs/Baltimore_CyberTrust_Root.pem',
#  'ssl_ca' => '/etc/ssl/certs/Baltimore_CyberTrust_Root.pem',
#  'sslca' => '/etc/ssl/certs/Baltimore_CyberTrust_Root.pem',
);

$CFG->wwwroot   = 'https://moodle-daad-de.daad.com';
$CFG->sslproxy  = true;
$CFG->dataroot  = '/var/www/moodle-daad-de.daad.com/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;
# Fallback solution if there is something broken. But better to set this via backend
# $CFG->maintenance_enabled = 0;
require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!

//=========================================================================
// 6. CONTENT SETTINGS FOR LERNLANDKARTE !!!
//=========================================================================
//
$CFG->lernlandkarte = [
    'imagesrc' => '/theme/boost_union_vorsprung/pix/static/Campus/lernlandkarte-alles-1440px-72dpi.png',
    'x' => 50,
    'y' => 50,
    'zoom' => 100,
];

// Debug modus if nessessary
//=========================================================================
// 7. SETTINGS FOR DEVELOPMENT SERVERS - not intended for production use!!!
//=========================================================================
//
// Force a debugging mode regardless the settings in the site administration
// @error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
// @ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
// $CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
// $CFG->debugdisplay = 1;              // NOT FOR PRODUCTION SERVERS!
//
// You can specify a comma separated list of user ids that that always see
// debug messages, this overrides the debug flag in $CFG->debug and $CFG->debugdisplay
// for these users only.
$CFG->debugusers = '31761,50,51';


// DUO-gast-button Plugin
$CFG->gast_button = [
  'clientId' => 5,
  'productId' => 4, // DUO
  'salt' => '37zLmV9fnoYsMddEG4SA',
  'env' => [
      'test' => [
          'url'=>'https://apprex.gast.de/gast-service-auth/api/public/client/entry',
          'secretkey'=>'WdNJp5D85aydbia2myBPhrtiXBVBvDZlSkrcYDQv3gk=',
      ],
      'prod' => [
          'url'=>'https://api.gast.de/auth/api/public/client/entry',
          'secretkey'=>'94oOkDr1MiN2VQ13hfvvYpzDGK4TbSRrXmEPLogM5AQ=',
      ]
  ],
  'activeenv' => 'prod',
];

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
