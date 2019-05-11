<?php
if (isset($_POST["submit"])) {

    $data = $_POST["url"];

    function cleanStr($str)
    {
        return html_entity_decode(strip_tags($str), ENT_QUOTES, 'UTF-8');
    }

    function hd_finallink($curl_content)
    {

        $regex = '/hd_src_no_ratelimit:"([^"]+)"/';
        if (preg_match($regex, $curl_content, $match)) {
            return $match[1];

        } else {return;}
    }

    function sd_finallink($curl_content)
    {

        $regex = '/sd_src_no_ratelimit:"([^"]+)"/';
        if (preg_match($regex, $curl_content, $match1)) {
            return $match1[1];

        } else {return;}
    }

    function thumb($curl_content)
    {
        $regex = '/video_id:"([^"]+)"/';
        if (preg_match($regex, $curl_content, $match2)) {

            $img_src = "https://graph.facebook.com/" . $match2[1] . "/picture";
            $link = "https://www.facebook.com/video.php?v=" . $match2[1];
            return array($img_src, $link);

        } else {return;}
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
<?php if ($sdlink) {?>
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
      <span style="text-align:center;display:block;">© <?php echo date('Y') ?><a href="https://www.kapadiya.net">Kapadiya.net</a></span>
  </div>
</div>
</div>

<a href="https://github.com/vikas5914/Facebook-Video-Downloader"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/e7bbb0521b397edbd5fe43e7f760759336b5e05f/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f677265656e5f3030373230302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png"></a>

<script type="text/javascript" src="js/jquery.min.js" ></script>
<script type="text/javascript" src="js/bootstrap.min.js" ></script>
</body>
</html>