<?php

// Loaded from configuration
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();

try {
    $variablekeys = $provider->getRequiredParameter('variablekeys');
} catch (Exception $e) {
    exit("Error: Required parameter 'variablekeys' not found.");
}

// Adding - if necessary - CORS headers
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

header('Content-Type: application/json');

echo $pluginBuilder->getConfiguration()->getJsonConfiguration($variablekeys);
