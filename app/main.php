<?php

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\HttpClient;

header('Content-Type: application/json');

$msg = [];

try {
    $url = $_REQUEST['url'];
    $redirectUrl = null;

    if (empty($url)) {
        throw new Exception('Please provide the URL', 1);
    }

    /*
        When there is shortlink, (example fb.watch), Directly getting content using follow redirect gives error.
        So we need to follow redirect and get the final url. thene make a another request to get the content.
    */

    $redirectCheckClient = HttpClient::create();

    try {
        $redirectCheckClient->request('GET', $url, ['max_redirects' => 0])->getContent();
        // not redirected
    } catch (RedirectionException $e) {
        $redirectUrl = $e->getResponse()->getInfo()['redirect_url'];
    }

    if ($redirectUrl) {
        $url = $redirectUrl;
    }

    $client = HttpClient::create([
        'headers' => [
            'accept'     => 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36',
        ],
    ]);

    $response = $client->request('GET', $url);

    $data = $response->getContent();

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
    $regexRateLimit = '/playable_url":"([^"]+)"/';

    if (preg_match($regexRateLimit, $curl_content, $match)) {
        return stripslashes($match[1]);
    } else {
        return false;
    }
}

function getHDLink($curl_content)
{
    $regexRateLimit = '/playable_url_quality_hd":"([^"]+)"/';

    if (preg_match($regexRateLimit, $curl_content, $match)) {
        return stripslashes($match[1]);
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
