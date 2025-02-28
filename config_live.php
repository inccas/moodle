<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodle';
$CFG->dbpass    = '7O*7uHK^X6Plmtg6kJnP';
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

$CFG->wwwroot   = 'https://moodle.daad.de';
$CFG->sslproxy  = true;
$CFG->dataroot  = '/var/www/moodle.daad.de/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

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
