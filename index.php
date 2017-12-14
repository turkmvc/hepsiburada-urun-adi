<?php

require_once "vendor/autoload.php";

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

function sendResponse($code, $responseArray) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($responseArray);
    exit();
};

$hepsiburadaSku = $_GET['sku'];

if (!$hepsiburadaSku) {
    sendResponse(422, [
        'message' => 'sku Parametresi ile hepsiburada ürün Sku (stok kodu) gönderilmeli. ?sku=HRSENS-70201 gibi'
    ]);
}

$httpClient = new Client([
    'exceptions' => false
]);

$response   = $httpClient->get('http://www.hepsiburada.com/as-p-' . $hepsiburadaSku);
$crawler = new Crawler((string) $response->getBody());

if ($response->getStatusCode() == 200) {
    $name = $crawler->filter('h1.product-name')->text();
    
    sendResponse(200, [
        'name'  =>  trim($name)
    ]);

} elseif ($response->getStatusCode() == 404) {
    sendResponse(404, [
        'message' => 'Ürün bulunamadı'
    ]);
} else {
    sendResponse(500, [
        'message' => 'Bilinmeyen bir hata oluştu'
    ]);
}
