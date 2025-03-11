<?php

require __DIR__ . "/crest/crest.php";
require __DIR__ . "/crest/crestcurrent.php";
require __DIR__ . "/crest/settings.php";
require __DIR__ . "/utils/index.php";
require __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$type = $_GET['type'];
$id = $_GET['id'];

$response = CRest::call('crm.item.get', [
  "entityTypeId" => LISTINGS_ENTITY_TYPE_ID,
  "id" => $id
]);

$property = $response['result']['item'];

if (!$property) {
  die("Property not found.");
}

function sanitizeFileName($filename)
{
  $filename = trim($filename);
  $filename = str_replace(' ', '_', $filename);

  $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $filename);

  $filename = preg_replace('/_+/', '_', $filename);

  return $filename;
}


$file_name = !empty($property['ufCrm12ReferenceNumber']) ? sanitizeFileName($property['ufCrm12ReferenceNumber']) . ".pdf" : "Property_$id.pdf";
$title = $property['ufCrm12TitleEn'] ?? "No Title";
$price = $property['ufCrm12Price'] . ' AED' ?? "Not Available";
$location = $property['ufCrm12Location'] ?? "Unknown";
$description = $property['ufCrm12DescriptionEn'] ?? "No Description";
$mainImage = $property['ufCrm12PhotoLinks'][0] ?? '';
$images = $property['ufCrm12PhotoLinks'] ?? [];
$size = $property['ufCrm12Size'] ?? "0";
$bedrooms = $property['ufCrm12Bedroom'] ?? "0";
$bathrooms = $property['ufCrm12Bathroom'] ?? "0";
$propertyType = $property['ufCrm12PropertyType'] ?? "Unknown";
$availability = $property['ufCrm12Availability'] ?? "Unknown";

$companyName = "VSERVE REAL ESTATE";
$companyAddress = "1717, Park Lane Tower, Business Bay, Dubai, UAE";
$companyWebsite = "https://vserverealestate.com/";

if ($type === 'agent') {
  $agentName = $property['ufCrm12AgentName'] ?? "Agent Name";
  $agentEmail = $property['ufCrm12AgentEmail'] ?? "agent@example.com";
  $agentPhone = $property['ufCrm12AgentPhone'] ?? "+971 4 357 5939";
} elseif ($type === 'owner') {
  $agentName = $property['ufCrm12ListingOwner'] ?? "Owner Name";
  $userResponse = CRest::call("user.get", [
    "filter" => [
      "NAME" => $property['ufCrm12ListingOwner']
    ]
  ]);
  $owner = $userResponse['result'][0];
  $agentEmail = $owner["EMAIL"] ?? "owner@example.com";
  $agentPhone = $owner["PERSONAL_MOBILE"] ?? "+971 4 357 5939";
} else {
  $currentUserResponse = CRestCurrent::call('user.current');
  $user = $currentUserResponse['result'];
  $agentName = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
  $agentEmail = $user['EMAIL'];
  $agentPhone = $user['PERSONAL_MOBILE'] ?? "+971 4 357 5939";
}

$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('isBase64Enabled', true);
$options->set('isRemoteEnabled', true);

$pdf = new Dompdf($options);
$pdf->setPaper('A4', 'portrait');

$html = "
    <div style='text-align:center;'>
        <img src='{$mainImage}' style='width:100%; height:auto;' />
    </div>
    <h2>{$title}</h2>
    <p><strong>Location:</strong> {$location}</p>
    <p><strong>Price:</strong> {$price}</p>
    <p><strong>Agent:</strong> {$agentName} - {$agentEmail} - {$agentPhone}</p>";

if (count($images) > 1) {
  $html .= "
    <div style='display:flex; justify-content:space-between;'>
        <img src='{$images[1]}' style='width:48%; height:auto;' />
        <img src='{$images[2]}' style='width:48%; height:auto;' />
    </div>";
}

$html .= "<hr><div style='page-break-before: always;'></div>";

$html .= "<h2>Property Features</h2>
    <p><strong>Size:</strong> {$size} sqft  <strong>Bedrooms:</strong> {$bedrooms}  <strong>Bathrooms:</strong> {$bathrooms}  <strong>Type:</strong> {$propertyType} <strong>Availability:</strong> {$availability}</p>
    <p><strong>Description:</strong><br>{$description}</p>";

$html .= "<h3>Image Gallery</h3>";
$html .= "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";

foreach ($images as $index => $image) {
  if ($index > 0) {
    $html .= "<img src='{$image}' style='width:100%; height:auto;'/>";
  }
}

$html .= "</div>";

$html .= "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
if ($property['ufCrm12Amenities'] && count($property['ufCrm12Amenities']) > 0) {
  $html .= "<h3>Private Amenities</h3><div>";

  $amenities = $property['ufCrm12Amenities'] ?? [];
  foreach ($amenities as $amenity) {
    $html .= "<div style='display: inline-block; padding: 5px 10px; background-color: #f1f1f1; border-radius: 20px; margin: 10px;'>" . getFullAmenityName($amenity) . "</div>";
  }
}
$html .= "</div>";
$html .= "</div>";
$html .= "<hr><div style='page-break-before: always;'></div>";

$html .= "
    <h3>For viewing and more information, please contact our property specialist:</h3>
    <div style='border: 1px solid #ddd; padding: 20px; text-align:center;'>
        <p><strong>{$agentName}</strong></p>
        <p>{$agentEmail}</p>
        <p>{$agentPhone}</p>
    </div>";

$pdf->loadHtml($html);
$pdf->render();
$pdf->stream($file_name, ["Attachment" => 0]);
