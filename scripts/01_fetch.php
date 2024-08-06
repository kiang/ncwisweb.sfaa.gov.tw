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
  //'4bc1e2f27af6e832017af6eeff7a0172' => '新北市',
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
  '4bc1e2f27af6e832017af6ef0256025b_4bc1e2f27af6e832017af6eeff7a0172' => '新北市板橋區',
  '4bc1e2f27af6e832017af6ef032502a2_4bc1e2f27af6e832017af6eeff7a0172' => '新北市三重區',
  '4bc1e2f27af6e832017af6ef03e702e3_4bc1e2f27af6e832017af6eeff7a0172' => '新北市中和區',
  '4bc1e2f27af6e832017af6ef000b019f_4bc1e2f27af6e832017af6eeff7a0172' => '新北市永和區',
  '4bc1e2f27af6e832017af6ef03c702d6_4bc1e2f27af6e832017af6eeff7a0172' => '新北市新莊區',
  '4bc1e2f27af6e832017af6ef026c0260_4bc1e2f27af6e832017af6eeff7a0172' => '新北市新店區',
  '4bc1e2f27af6e832017af6ef04950327_4bc1e2f27af6e832017af6eeff7a0172' => '新北市樹林區',
  '4bc1e2f27af6e832017af6ef03e502e2_4bc1e2f27af6e832017af6eeff7a0172' => '新北市鶯歌區',
  '4bc1e2f27af6e832017af6ef017e0222_4bc1e2f27af6e832017af6eeff7a0172' => '新北市三峽區',
  '4bc1e2f27af6e832017af6ef027b0264_4bc1e2f27af6e832017af6eeff7a0172' => '新北市淡水區',
  '4bc1e2f27af6e832017af6ef01ad0230_4bc1e2f27af6e832017af6eeff7a0172' => '新北市汐止區',
  '4bc1e2f27af6e832017af6ef00d801ef_4bc1e2f27af6e832017af6eeff7a0172' => '新北市瑞芳區',
  '4bc1e2f27af6e832017af6ef00e301f3_4bc1e2f27af6e832017af6eeff7a0172' => '新北市土城區',
  '4bc1e2f27af6e832017af6ef0579036f_4bc1e2f27af6e832017af6eeff7a0172' => '新北市蘆洲區',
  '4bc1e2f27af6e832017af6ef02a10271_4bc1e2f27af6e832017af6eeff7a0172' => '新北市五股區',
  '4bc1e2f27af6e832017af6ef01bf0236_4bc1e2f27af6e832017af6eeff7a0172' => '新北市泰山區',
  '4bc1e2f27af6e832017af6ef04a2032b_4bc1e2f27af6e832017af6eeff7a0172' => '新北市林口區',
  '4bc1e2f27af6e832017af6ef001401a3_4bc1e2f27af6e832017af6eeff7a0172' => '新北市深坑區',
  '4bc1e2f27af6e832017af6ef034702a9_4bc1e2f27af6e832017af6eeff7a0172' => '新北市石碇區',
  '4bc1e2f27af6e832017af6ef04b20331_4bc1e2f27af6e832017af6eeff7a0172' => '新北市坪林區',
  '4bc1e2f27af6e832017af6ef04b70332_4bc1e2f27af6e832017af6eeff7a0172' => '新北市三芝區',
  '4bc1e2f27af6e832017af6ef04ba0333_4bc1e2f27af6e832017af6eeff7a0172' => '新北市石門區',
  '4bc1e2f27af6e832017af6ef029e0270_4bc1e2f27af6e832017af6eeff7a0172' => '新北市八里區',
  '4bc1e2f27af6e832017af6ef00cf01ec_4bc1e2f27af6e832017af6eeff7a0172' => '新北市平溪區',
  '4bc1e2f27af6e832017af6ef03ff02ec_4bc1e2f27af6e832017af6eeff7a0172' => '新北市雙溪區',
  '4bc1e2f27af6e832017af6ef00de01f2_4bc1e2f27af6e832017af6eeff7a0172' => '新北市貢寮區',
  '4bc1e2f27af6e832017af6ef056e036b_4bc1e2f27af6e832017af6eeff7a0172' => '新北市金山區',
  '4bc1e2f27af6e832017af6ef02a30272_4bc1e2f27af6e832017af6eeff7a0172' => '新北市萬里區',
  '4bc1e2f27af6e832017af6ef0577036e_4bc1e2f27af6e832017af6eeff7a0172' => '新北市烏來區',
];

$crawler = $browser->request('GET', 'https://ncwisweb.sfaa.gov.tw/home/childcare-center');
$proj4 = new Proj4php();
$projTWD97 = new Proj('EPSG:3826', $proj4);
$projWGS84 = new Proj('EPSG:4326', $proj4);

$addressPath = $basePath . '/raw/address';
if (!file_exists($addressPath)) {
  mkdir($addressPath, 0777, true);
}
$activeList = [];
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

    try {
      $form = $crawler->selectButton('查詢')->form();
    } catch (Exception $e) {
      // do nothing
    }
    $cityKeyParts = explode('_', $cityKey);
    if (count($cityKeyParts) == 1) {
      $crawler = $browser->submit($form, ['cityId' => $cityKey, 'locateType' => 1, 'page' => $page]);
    } else {
      $crawler = $browser->submit($form, ['cityId' => $cityKeyParts[1], 'townId' => $cityKeyParts[0], 'locateType' => 1, 'page' => $page]);
    }

    $pageContent = $browser->getResponse()->getContent();
    //file_put_contents($pagePath . '/' . $page . '.html', $pageContent);

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
      echo "getting {$detailId}\n";
      $detailUrl = 'https://ncwisweb.sfaa.gov.tw' . implode('/', $parts);
      $crawler = $browser->request('GET', $detailUrl);
      $detailContent = $browser->getResponse()->getContent();
      if (false !== strpos($detailContent, '<title>網頁無法顯示</title>')) {
        $pos = strpos($pageContent, '"/home/childcare-center/detail/', $posEnd);
        continue;
      }
      //file_put_contents($detailFile, $detailContent);

      $detailPos = strpos($detailContent, '<div class="dataBlock w-0 main">');
      $detailPosEnd = strpos($detailContent, '</main>', $detailPos);
      $detailParts = explode('<div class="data-row', substr($detailContent, $detailPos, $detailPosEnd - $detailPos));
      array_shift($detailParts);
      $data = [
        'id' => $detailId,
      ];
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
      } else {
        $address = $data['所在地'];
        $addressPos = strpos($address, '號');
        if (false !== $addressPos) {
          $address = substr($address, 0, $addressPos) . '號';
        }
        $addressFile = $addressPath . '/' . $address . '.json';
        if (!file_exists($addressFile)) {
          $command = <<<EOD
curl 'https://api.nlsc.gov.tw/MapSearch/ContentSearch?word=___KEYWORD___&mode=AutoComplete&count=1&feedback=XML' \
-H 'Accept: application/xml, text/xml, */*; q=0.01' \
-H 'Accept-Language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7' \
-H 'Connection: keep-alive' \
-H 'Origin: https://maps.nlsc.gov.tw' \
-H 'Referer: https://maps.nlsc.gov.tw/' \
-H 'Sec-Fetch-Dest: empty' \
-H 'Sec-Fetch-Mode: cors' \
-H 'Sec-Fetch-Site: same-site' \
-H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36' \
-H 'sec-ch-ua: "Google Chrome";v="123", "Not:A-Brand";v="8", "Chromium";v="123"' \
-H 'sec-ch-ua-mobile: ?0' \
-H 'sec-ch-ua-platform: "Linux"'
EOD;
          $result = shell_exec(strtr($command, [
            '___KEYWORD___' => urlencode($address),
          ]));
          $cleanKeyword = trim(strip_tags($result));
          if (!empty($cleanKeyword)) {
            $command = <<<EOD
              curl 'https://api.nlsc.gov.tw/MapSearch/QuerySearch' \
                -H 'Accept: application/xml, text/xml, */*; q=0.01' \
                -H 'Accept-Language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7' \
                -H 'Connection: keep-alive' \
                -H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' \
                -H 'Origin: https://maps.nlsc.gov.tw' \
                -H 'Referer: https://maps.nlsc.gov.tw/' \
                -H 'Sec-Fetch-Dest: empty' \
                -H 'Sec-Fetch-Mode: cors' \
                -H 'Sec-Fetch-Site: same-site' \
                -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36' \
                -H 'sec-ch-ua: "Google Chrome";v="123", "Not:A-Brand";v="8", "Chromium";v="123"' \
                -H 'sec-ch-ua-mobile: ?0' \
                -H 'sec-ch-ua-platform: "Linux"' \
                --data-raw 'word=___KEYWORD___&feedback=XML&center=120.218280%2C23.007292'
              EOD;
            $result = shell_exec(strtr($command, [
              '___KEYWORD___' => urlencode(urlencode($cleanKeyword)),
            ]));
            $json = json_decode(json_encode(simplexml_load_string($result)), true);
            if (!empty($json['ITEM']['LOCATION'])) {
              $parts = explode(',', $json['ITEM']['LOCATION']);
              if (count($parts) === 2) {
                file_put_contents($addressFile, json_encode([
                  'AddressList' => [
                    [
                      'X' => $parts[0],
                      'Y' => $parts[1],
                    ],
                  ],
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
              }
            }
          }
        }
        if (file_exists($addressFile)) {
          $addressData = json_decode(file_get_contents($addressFile), true);
          if (isset($addressData['AddressList'][0])) {
            $data['longitude'] = floatval($addressData['AddressList'][0]['X']);
            $data['latitude'] = floatval($addressData['AddressList'][0]['Y']);
          }
        }
      }
      $data['核定收托'] = intval($data['核定收托']);
      $data['實際收托'] = intval($data['實際收托']);
      file_put_contents($dataPath . '/' . $detailId . '.json', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
      $activeList[$data['id']] = true;

      $pos = strpos($pageContent, '"/home/childcare-center/detail/', $posEnd);
    }
  }
}

ksort($activeList);
file_put_contents($basePath . '/raw/active_list.json', json_encode($activeList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
