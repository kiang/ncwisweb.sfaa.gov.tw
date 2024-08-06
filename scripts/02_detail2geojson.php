<?php
$basePath = dirname(__DIR__);
$activeList = [];
$activeListFile = $basePath . '/raw/active_list.json';
if (file_exists($activeListFile)) {
    $activeList = json_decode(file_get_contents($activeListFile), true);
}

$fc = [
    'type' => 'FeatureCollection',
    'features' => [],
];

foreach (glob($basePath . '/docs/data/*/*.json') as $jsonFile) {
    $data = json_decode(file_get_contents($jsonFile), true);
    if (!empty($data['longitude'])) {
        $fc['features'][$data['id']] = [
            'type' => 'Feature',
            'properties' => [
                'id' => $data['id'],
                'name' => $data['機構名稱'],
                'address' => $data['所在地'],
                'phone' => $data['聯絡電話'],
                'capacity' => $data['核定收托'],
                'status' => $data['實際收托'],
                'is_active' => isset($activeList[$data['id']]) ? true : false,
            ],
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [
                    $data['longitude'],
                    $data['latitude'],
                ],
            ],
        ];
    }
}

ksort($fc['features']);
$fc['features'] = array_values($fc['features']);

file_put_contents($basePath . '/docs/babycare.json', json_encode($fc, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
