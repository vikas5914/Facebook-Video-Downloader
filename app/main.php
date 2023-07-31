<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpClient\HttpClient;

header('Content-Type: application/json');

$msg = [];

try {
    $url          = trim($_REQUEST['url']);
    $curl_command = trim($_REQUEST['curl_command']);

    $redirectUrl = null;

    if (empty($url)) {
        throw new Exception('Please provide the URL', 1);
    }

    if (empty($curl_command)) {
        throw new Exception('Please provide curl command', 1);
    }

    // Break the curl_command in different variables and assign them
    foreach (explode("-H", $curl_command) as $entry) {
        $value = explode(": ", $entry)[1];
        if (preg_match("/Accept-Language:/", $entry)) {
            $accept_language = $value;
        }
        else if (preg_match("/User-Agent:/", $entry)) {
            $user_agent = $value;
        }
        elseif (preg_match("/Cookie:/", $entry)) {
            $cookie = $value;
        }
    }

    $headers = [
        'sec-fetch-user'            => '?1',
        'sec-fetch-site'            => 'none',
        'sec-fetch-dest'            => 'document',
        'sec-fetch-mode'            => 'navigate',
        'cache-control'             => 'max-age=0',
        'authority'                 => 'www.facebook.com',
        'upgrade-insecure-requests' => '1',
        'accept-language'           => $accept_language,
        'user-agent'                => $user_agent,
        'accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
        'cookie'                    => $cookie,
    ];

    $redirectCheckClient = HttpClient::create([
        'headers' => $headers,
    ]);

    $client = HttpClient::create([
        'headers' => $headers,
    ]);

    $response = $client->request('GET', $url);

    $data = $response->getContent();

    $msg['success'] = true;

    $msg['id'] = generateId($url);
    $msg['title'] = getTitle($data);

    if ($sdLink = getSDLink($data)) {
        $msg['links']['Download Low Quality'] = $sdLink . '&dl=1';
    }

    if ($hdLink = getHDLink($data)) {
        $msg['links']['Download High Quality'] = $hdLink . '&dl=1';
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
    $tmpStr = "{\"text\": \"{$str}\"}";

    return json_decode($tmpStr)->text;
}

function getSDLink($curl_content)
{
    $regexRateLimit = '/browser_native_sd_url":"([^"]+)"/';

    if (preg_match($regexRateLimit, $curl_content, $match)) {
        return cleanStr($match[1]);
    } else {
        return false;
    }
}

function getHDLink($curl_content)
{
    $regexRateLimit = '/browser_native_hd_url":"([^"]+)"/';

    if (preg_match($regexRateLimit, $curl_content, $match)) {
        return cleanStr($match[1]);
    } else {
        return false;
    }
}

function getTitle($curl_content)
{
    $title = null;
    if (preg_match('/<title>(.*?)<\/title>/', $curl_content, $matches)) {
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
