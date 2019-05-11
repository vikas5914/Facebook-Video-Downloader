<?php
// $url = $_POST['url'];

$url = "https://www.facebook.com/5min.crafts/videos/1040621729467977";

$context = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36",
    ],
];
$context = stream_context_create($context);
$data = file_get_contents($url, false, $context);

$regex = '/FBQualityLabel=\/"(.+?)">/';

preg_match($regex, $data, $matches);

var_dump($matches);

FBQualityLabel = \\"(.+?)" > \\x3CBaseURL > (. +?) \/ BaseURL
