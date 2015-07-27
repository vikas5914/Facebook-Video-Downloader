<?php
$url = $_POST['url'];

$context = [
  'http' => [
    'method' => 'GET',
    'header' => "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13"
  ]
];
$context = stream_context_create($context);
$data = file_get_contents($url, false, $context);

function replace_unicode_escape_sequence($uni) {
	return mb_convert_encoding(pack('H*', $uni[1]), 'UTF-8', 'UCS-2BE');
}

function hd_finallink($curl_content) {


	$regex = "/hd_src\\\\(.+?)is_hds/";
	if(preg_match($regex,$curl_content,$match)){

	$str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $match[0]);

	$decodedStr= stripslashes(rawurldecode($str));
	$decodedStr= str_replace('\/', '/', $decodedStr);
	$decodedStr= str_replace('hd_src":', '', $decodedStr);
	$decodedStr= str_replace(',"is_hds', '', $decodedStr);
	$decodedStr= str_replace('"', '', $decodedStr);


	return $decodedStr ;

	} else{return;}
}

function sd_finallink($curl_content) {

	$regex = "/sd_src\\\\(.+?)https\\\\(.+?)video_id/";
	if(preg_match($regex,$curl_content,$match1)){
	
		$str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $match1[0]);
		$decodedStr= stripslashes(rawurldecode($str));
		$decodedStr= str_replace('\/', '/', $decodedStr);
		$decodedStr= str_replace('sd_src":"', '', $decodedStr);
		$decodedStr= str_replace('","video_id', '', $decodedStr);
		return $decodedStr ;

	}else{return;}
}

function gettitle($curl_content){
	$regex ="/title id=\"pageTitle\">(.+?)<\/title>/";
 	if(preg_match($regex,$curl_content,$title_match)){
 		return $title_match[1];
 	}else{return;}

}

$hdlink = hd_finallink($data);
$sdlink = sd_finallink($data);
$title = gettitle($data);

$message = array();


if ($sdlink !="") {
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

?>

