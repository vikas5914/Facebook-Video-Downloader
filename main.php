<?php
$url = $_POST['url'];

$context = [
	'http' => [
		'method' => 'GET',
		'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36",
	],
];
$context = stream_context_create($context);
$data = file_get_contents($url, false, $context);

function replace_unicode_escape_sequence($uni) {
	return mb_convert_encoding(pack('H*', $uni[1]), 'UTF-8', 'UCS-2');
}

function hd_finallink($curl_content) {

	$regex = '/"hd_src_no_ratelimit":"([^"]+)"/';
	if (preg_match($regex, $curl_content, $match)) {
		$str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $match[1]);
		$decodedStr = str_replace('\/', '/', $str);
		return $decodedStr;

	} else {return;}
}

function sd_finallink($curl_content) {

	$regex = '/"sd_src_no_ratelimit":"([^"]+)"/';
	if (preg_match($regex, $curl_content, $match1)) {

		$str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $match1[1]);
		$decodedStr = str_replace('\/', '/', $str);
		return $decodedStr;

	} else {return;}
}

function gettitle($curl_content) {
	$regex = "/title id=\"pageTitle\">(.+?)<\/title>/";
	if (preg_match($regex, $curl_content, $title_match)) {
		$title_match = explode("|", $title_match[1]);
		return $title_match[0];
	} else {return;}

}

$hdlink = hd_finallink($data);
$sdlink = sd_finallink($data);
$title = gettitle($data);

$message = array();

if ($sdlink != "") {
	$message = array(
		'type' => 'success',
		'title' => $title,
		'hd_download_url' => $hdlink,
		'sd_download_url' => $sdlink,

	);
} else {
	$message = array(
		'type' => 'failure',
		'message' => 'Error retrieving the download link for the url. Please try again later',
	);
}
echo json_encode($message);
