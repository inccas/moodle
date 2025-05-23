<?php
// ${license.statement}
require_once ('pluginbuilder.php');

$provider = $pluginBuilder->getCustomParamsProvider();

try {
    $image = $provider->getRequiredParameter('image');
} catch (Exception $e) {
    exit("Error: Required parameter 'image' not found.");
}

// Adding - if necessary - CORS headers.
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : "";
$res = new com_wiris_system_service_HttpResponse();
$pluginBuilder->addCorsHeaders($res, $origin);

$cas = $pluginBuilder->newCas();
$outp = array();
echo $cas->createCasImage($image);
