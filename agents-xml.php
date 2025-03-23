<?php
require 'utils/index.php';
require_once __DIR__ . "/crest/settings.php";

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = WEB_HOOK_URL;
$entityTypeId = AGENTS_ENTITY_TYPE_ID;
$fields = [
    'id',
    'ufCrm12AgentName',
    'ufCrm12AgentEmail',
    'ufCrm12AgentMobile',
    'ufCrm12AgentLicense',
    'ufCrm12AgentPhoto'
];

$agents = fetchAllAgents($baseUrl, $entityTypeId, $fields);

if (count($agents) > 0) {
    $xml = generateAgentsXml($agents);
    echo $xml;
} else {
    echo '<?xml version="1.0" encoding="UTF-8"?><agents></agents>';
}
