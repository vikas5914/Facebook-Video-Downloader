<?php

header('Content-Type: application/json');

$msg = [];

try {
    $url = $_POST['url'];

    if (empty($url)) {
        throw new Exception('Please provide the URL', 1);
    }

    $context = [
        'http' => [
            'method' => 'GET',
            'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36',
        ],
    ];
    $context = stream_context_create($context);
    $data = file_get_contents($url, false, $context);

    $msg['success'] = true;

    $msg['id'] = generateId($url);
    $msg['title'] = getTitle($data);

    if ($sdLink = getSDLink($data)) {
        $msg['links']['Download Low Quality'] = $sdLink;
    }

    if ($hdLink = getHDLink($data)) {
        $msg['links']['Download High Quality'] = $hdLink;
    }
} catch (Exception $e) {
    $msg['success'] = false;
    $msg['message'] = $e->getMessage();
}

echo json_encode($msg);

function generateId($url)
{
    $id = '';
    if (is_int($url)) {
        $id = $url;
    } elseif (preg_match('#(\d+)/?$#', $url, $matches)) {
        $id = $matches[1];
    }

    return $id;
}

function cleanStr($str)
{
    return html_entity_decode(strip_tags($str), ENT_QUOTES, 'UTF-8');
}

function getSDLink($curl_content)
{
    $regexRateLimit = '/sd_src_no_ratelimit:"([^"]+)"/';
    $regexSrc = '/sd_src:"([^"]+)"/';

    if (preg_match($regexRateLimit, $curl_content, $match)) {
        return $match[1];
    } elseif (preg_match($regexSrc, $curl_content, $match)) {
        return $match[1];
    } else {
        return false;
    }
}

function getHDLink($curl_content)
{
    $regexRateLimit = '/hd_src_no_ratelimit:"([^"]+)"/';
    $regexSrc = '/hd_src:"([^"]+)"/';

    if (preg_match($regexRateLimit, $curl_content, $match)) {
        return $match[1];
    } elseif (preg_match($regexSrc, $curl_content, $match)) {
        return $match[1];
    } else {
        return false;
    }
}

function getTitle($curl_content)
{
    $title = null;
    if (preg_match('/h2 class="uiHeaderTitle"?[^>]+>(.+?)<\/h2>/', $curl_content, $matches)) {
        $title = $matches[1];
    } elseif (preg_match('/title id="pageTitle">(.+?)<\/title>/', $curl_content, $matches)) {
        $title = $matches[1];
    }

    return cleanStr($title);
}

function getDescription($curl_content)
{
    if (preg_match('/span class="hasCaption">(.+?)<\/span>/', $curl_content, $matches)) {
        return cleanStr($matches[1]);
    }

    return false;
}
