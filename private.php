<?php
if (isset($_POST["submit"])) {

	$data = $_POST["url"];

	function replace_unicode_escape_sequence($uni) {
		return mb_convert_encoding(pack('H*', $uni[1]), 'UTF-8', 'UCS-2BE');
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

	function thumb($curl_content) {
		$regex = "/fb:\/\/video\/(.+?)\"/";
		if (preg_match($regex, $curl_content, $match2)) {

			$img_src = "https://graph.facebook.com/" . $match2[1] . "/picture";
			$link = "https://www.facebook.com/video.php?v=" . $match2[1];
			return array($img_src, $link);

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
	$img = thumb($data);

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Facebook Private Video Downloader</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style type="text/css">
  .centered {
      text-align: center;
      width: 90%;
      margin: 0 auto;
    }</style>

</head>

<div class="container">
  <div class="row clearfix">
    <div class="col-md-12 column">
      <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#facebook-video-downloader">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">FBVD</a>
        </div>
        <div class="collapse navbar-collapse" id="facebook-video-downloader">
          <ul class="nav navbar-nav">
            <li><a href="./index.php">Facebook Video Downloader</a></li>
            <li class="active"><a href="./private.php">Private Video Downloader</a></li>
            <li><a href="./vine-video-downloader.php">Graph API Video Downloader</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="./privacy-policy.php">Privacy Policy</a></li>
            <li><a href="mailto:vikas@kapadiya.net">Contact</a></li>
          </ul>
        </div>
      </nav>
      <div class="well">
        <div class="centered">
            <h1>Facebook Private Video Downloader </h1>
            <h2>Download Facebook Video</h2>

            <form style="margin:5% 0 5% 0;" method="post">
              <div class="ol-mg-12">
                  <textarea rows="4" style="width:100%"  name="url" placeholder="Paste the Video Page Source here..." id="url"></textarea>
              </div>
              <div class="control-group col-mg-12">
                  <span class="input-group-btn"><button class="btn btn-primary btn-block btn-large" id="download" name="submit">Download!</button></span>
              </div>
            </form>
        </div>
      </div>
        <?php if ($title) {?>
      <div class="well" id="result" style="">
          <div id="downloadUrl" style="">
            <div class="row">
              <div class="col-md-4"><p class="text-center"><b>Video Picture</b></p><p class="text-center" id="img"><img class="img-thumbnail" src="<?php echo $img[0]; ?>"></p></div>

              <div class="col-md-4">
                  <p class="text-center"><b>Information</b></p>
                  <div class="col-sm-2">Title:</div>
                  <div class="col-sm-10" id="title"><?php echo $title; ?></div>
                  <div class="col-sm-2">Source:</div>
                  <div class="col-sm-10" id="src"><a href="<?php echo $img[1]; ?>"><?php echo $img[1]; ?></a></div>
              </div>
              <div class="col-md-4">
                <p class="text-center"><b>Download Link</b></p>
                <p class="text-center" id="sd"><a href="<?php echo $sdlink; ?>" download="sd.mp4"><b>MP4 SD</b></a></p>
                <p class="text-center" id="hd"><a href="<?php echo $hdlink; ?>" download="hd.mp4"><b>MP4 HD</b></a></p>
               </div>
            </div>
          </div>
        </div>
        <?php }?>
      </div>
    </div>
  <div class="well">
    <div class="centered">
      <span style="text-align:center;display:block;">© <?php echo date('Y') ?> <a href="https://hashtagsoftworks.com">Hashtagsoftworks.com</a> &amp; <a href="https://www.kapadiya.net">Kapadiya.net</a></span>
    </div>
  </div>
</div>

<script type="text/javascript" src="js/jquery.min.js" ></script>
<script type="text/javascript" src="js/bootstrap.min.js" ></script>
</body>
</html>