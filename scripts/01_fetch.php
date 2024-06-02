<?php
require __DIR__ . '/vendor/autoload.php';
$basePath = dirname(__DIR__);

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

$browser = new HttpBrowser(HttpClient::create());

$cities = [
  '4bc1e2f27af6e832017af6eeff7a0172' => '新北市',
  '4bc1e2f27af6e832017af6eeff750170' => '臺北市',
  '4bc1e2f27af6e832017af6eeff7d0173' => '桃園市',
  '4bc1e2f27af6e832017af6eeff860176' => '臺中市',
  '4bc1e2f27af6e832017af6eeff4a0162' => '臺南市',
  '4bc1e2f27af6e832017af6eeff8c0178' => '高雄市',
  '4bc1e2f27af6e832017af6eeff580166' => '宜蘭縣',
  '4bc1e2f27af6e832017af6eeff890177' => '新竹縣',
  '4bc1e2f27af6e832017af6eeff72016f' => '苗栗縣',
  '4bc1e2f27af6e832017af6eeff5d0168' => '彰化縣',
  '4bc1e2f27af6e832017af6eeff830175' => '南投縣',
  '4bc1e2f27af6e832017af6eeff6f016e' => '雲林縣',
  '4bc1e2f27af6e832017af6eeff63016a' => '嘉義縣',
  '4bc1e2f27af6e832017af6eeff3e015d' => '屏東縣',
  '4bc1e2f27af6e832017af6eeff67016b' => '臺東縣',
  '4bc1e2f27af6e832017af6eeff600169' => '花蓮縣',
  '4bc1e2f27af6e832017af6eeff770171' => '澎湖縣',
  '4bc1e2f27af6e832017af6eeff460160' => '基隆市',
  '4bc1e2f27af6e832017af6eeff480161' => '新竹市',
  '4bc1e2f27af6e832017af6eeff43015f' => '嘉義市',
  '4bc1e2f27af6e832017af6eeff6c016d' => '連江縣',
  '4bc1e2f27af6e832017af6eeff38015c' => '金門縣',
];

$crawler = $browser->request('GET', 'https://ncwisweb.sfaa.gov.tw/home/childcare-center');

foreach ($cities as $cityKey => $city) {
  $pagePath = $basePath . '/raw/pages/' . $city;
  if (!file_exists($pagePath)) {
    mkdir($pagePath, 0777, true);
  }
  $detailPath = $basePath . '/raw/detail/' . $city;
  if (!file_exists($detailPath)) {
    mkdir($detailPath, 0777, true);
  }
  $pageTotal = 1;
  $pageTotalDone = false;
  for ($page = 0; $page < $pageTotal; $page++) {
    $pageFile = $pagePath . '/' . $page . '.html';
    $form = $crawler->selectButton('查詢')->form();
    $crawler = $browser->submit($form, ['cityId' => $cityKey, 'locateType' => 1, 'page' => $page]);
    $pageContent = $browser->getResponse()->getContent();
    file_put_contents($pagePath . '/' . $page . '.html', $pageContent);
    if (false === $pageTotalDone) {
      $pos = strpos($pageContent, '<div id="pagination"');
      $pos = strpos($pageContent, '<span', $pos);
      $posEnd = strpos($pageContent, '</span>', $pos);
      $pageTotal = ceil(intval(strip_tags(substr($pageContent, $pos, $posEnd - $pos))) / 15);
    }
    $pos = strpos($pageContent, '"/home/childcare-center/detail/');
    while (false !== $pos) {
      $posEnd = strpos($pageContent, '"', $pos + 1);
      $parts = explode('/', substr($pageContent, $pos + 1, $posEnd - $pos - 1));
      $detailId = $parts[4];
      $detailFile = $detailPath . '/' . $detailId . '.html';
      $detailUrl = 'https://ncwisweb.sfaa.gov.tw' . implode('/', $parts);
      $crawler = $browser->request('GET', $detailUrl);
      $detailContent = $browser->getResponse()->getContent();
      file_put_contents($detailFile, $detailContent);

      $pos = strpos($pageContent, '"/home/childcare-center/detail/', $posEnd);
    }
  }
}
