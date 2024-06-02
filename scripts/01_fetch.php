<?php
require __DIR__ . '/vendor/autoload.php';
$basePath = dirname(__DIR__);

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

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
$proj4 = new Proj4php();
$projTWD97 = new Proj('EPSG:3826', $proj4);
$projWGS84 = new Proj('EPSG:4326', $proj4);

foreach ($cities as $cityKey => $city) {
  $lnglat = [];
  $pagePath = $basePath . '/raw/pages/' . $city;
  if (!file_exists($pagePath)) {
    mkdir($pagePath, 0777, true);
  }
  $detailPath = $basePath . '/raw/detail/' . $city;
  if (!file_exists($detailPath)) {
    mkdir($detailPath, 0777, true);
  }
  $dataPath = $basePath . '/docs/data/' . $city;
  if (!file_exists($dataPath)) {
    mkdir($dataPath, 0777, true);
  }
  // $penaltyPath = $basePath . '/raw/penalty/' . $city;
  // if (!file_exists($penaltyPath)) {
  //   mkdir($penaltyPath, 0777, true);
  // }
  $pageTotal = 1;
  $pageTotalDone = false;
  for ($page = 0; $page < $pageTotal; $page++) {
    $pageFile = $pagePath . '/' . $page . '.html';
    if (file_exists($pageFile)) {
      $pageContent = file_get_contents($pageFile);
    } else {
      $form = $crawler->selectButton('查詢')->form();
      $crawler = $browser->submit($form, ['cityId' => $cityKey, 'locateType' => 1, 'page' => $page]);
      $pageContent = $browser->getResponse()->getContent();
      file_put_contents($pagePath . '/' . $page . '.html', $pageContent);
    }
    if (false === $pageTotalDone) {
      $pos = strpos($pageContent, '<div id="pagination"');
      $pos = strpos($pageContent, '<span', $pos);
      $posEnd = strpos($pageContent, '</span>', $pos);
      $pageTotal = ceil(intval(strip_tags(substr($pageContent, $pos, $posEnd - $pos))) / 15);
    }

    $pos = strpos($pageContent, 'function initMarkers');
    $posEnd = strpos($pageContent, 'setZoomStyle', $pos);
    $parts = explode('var i ', substr($pageContent, $pos, $posEnd - $pos));
    foreach ($parts as $part) {
      $pos = strpos($part, '/detail/');
      if (false === $pos) {
        continue;
      }
      $posEnd = strpos($part, '\'', $pos);
      $urlParts = explode('/', substr($part, $pos, $posEnd - $pos));
      $detailId = $urlParts[2];
      $codeParts = explode('\'', $part);
      $x = floatval($codeParts[3]);
      $y = floatval($codeParts[5]);
      if ($x > 0 && $y > 0) {
        $pointSrc = new Point($x, $y, $projTWD97);
        $pointDest = $proj4->transform($projTWD97, $projWGS84, $pointSrc);
        $lnglat[$detailId] = $pointDest->toArray();
      }
    }

    $pos = strpos($pageContent, '"/home/childcare-center/detail/');
    while (false !== $pos) {
      $posEnd = strpos($pageContent, '"', $pos + 1);
      $parts = explode('/', substr($pageContent, $pos + 1, $posEnd - $pos - 1));
      $detailId = $parts[4];
      $detailFile = $detailPath . '/' . $detailId . '.html';
      if (file_exists($detailFile)) {
        $detailContent = file_get_contents($detailFile);
      } else {
        echo "getting {$detailId}\n";
        $detailUrl = 'https://ncwisweb.sfaa.gov.tw' . implode('/', $parts);
        $crawler = $browser->request('GET', $detailUrl);
        $detailContent = $browser->getResponse()->getContent();
        file_put_contents($detailFile, $detailContent);
      }

      $detailPos = strpos($detailContent, '<div class="dataBlock w-0 main">');
      $detailPosEnd = strpos($detailContent, '</main>', $detailPos);
      $detailParts = explode('<div class="data-row', substr($detailContent, $detailPos, $detailPosEnd - $detailPos));
      array_shift($detailParts);
      $data = [];
      foreach ($detailParts as $detailPart) {
        $detailPos = strpos($detailPart, '>');
        $detailPart = substr($detailPart, $detailPos + 1);
        $cols = preg_split('/<[\\/]?span>/', $detailPart);
        $cols[0] = trim(strip_tags($cols[0]));
        switch ($cols[0]) {
          case '核備工作人員數':
            $data[$cols[0]] = [];
            $lines = explode('</div>', $cols[1]);
            foreach ($lines as $line) {
              $lineCols = explode(' ', trim(strip_tags($line)));
              if (isset($lineCols[1])) {
                $data[$cols[0]][trim($lineCols[0])] = $lineCols[1];
              }
            }
            break;
          case '收費情形':
            $data[$cols[0]] = [];
            $blocks = explode('<p class="h6">托育方式：', $cols[1]);
            foreach ($blocks as $block) {
              $blockLines = explode('</tr>', $block);
              if (!isset($blockLines[1])) {
                continue;
              }
              $blockPos = strpos($block, '</p>');
              $blockTitle = substr($block, 0, $blockPos);
              $data[$cols[0]][$blockTitle] = [];
              foreach ($blockLines as $blockLine) {
                $blockCols = explode('</td>', $blockLine);
                if (isset($blockCols[1])) {
                  foreach ($blockCols as $k => $v) {
                    $blockCols[$k] = trim(strip_tags($v));
                  }
                  $data[$cols[0]][$blockTitle][] = [
                    '費用名稱' => $blockCols[0],
                    '費用金額' => $blockCols[1],
                    '費用說明' => $blockCols[2],
                  ];
                }
              }
            }
            break;
          case '違反法令紀錄':
            $data[$cols[0]] = [];
            $lines = explode('</li>', $cols[1]);
            if (count($lines) > 1) {
              foreach ($lines as $line) {
                $lineCols = explode('"', $line);
                if (count($lineCols) == 9) {
                  $penaltyParts = explode('/', $lineCols[1]);
                  $penaltyUrl = 'https://ncwisweb.sfaa.gov.tw' . implode('/', $penaltyParts);
                  $data[$cols[0]][] = $penaltyUrl;
                  // $penaltyFile = $penaltyPath . '/' . $detailId . '_' . $penaltyParts[4] . '.html';
                  // if (!file_exists($penaltyFile)) {
                  //   $penaltyUrl = 'https://ncwisweb.sfaa.gov.tw' . implode('/', $penaltyParts);
                  //   $browser->request('GET', 'https://ncwisweb.sfaa.gov.tw/home/childcare-center/detail/' . $detailId);
                  //   $browser->request('GET', $penaltyUrl);
                  //   $penaltyContent = $browser->getResponse()->getContent();
                  //   file_put_contents($penaltyFile, $penaltyContent);
                  // }
                  // $penalty = file_get_contents($penaltyFile);
                  // $data[$cols[0]][] = $lineCols[1];
                }
              }
            }
            break;
          case '退費說明':
            $lines = explode('<br>', $cols[1]);
            foreach ($lines as $k => $v) {
              $lines[$k] = trim(strip_tags($v));
            }
            $data[$cols[0]] = implode("\n", $lines);
            break;
          default:
            $data[$cols[0]] = trim(strip_tags($cols[1]));
        }
      }
      $data['longitude'] = $data['latitude'] = '';
      if (isset($lnglat[$detailId])) {
        $data['longitude'] = $lnglat[$detailId][0];
        $data['latitude'] = $lnglat[$detailId][1];
      }
      file_put_contents($dataPath . '/' . $detailId . '.json', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

      $pos = strpos($pageContent, '"/home/childcare-center/detail/', $posEnd);
    }
  }
}
