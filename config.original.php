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
$CFG->maintenance_enabled = 0;
require_once(__DIR__ . '/lib/setup.php');

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
