<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Facebook Video Downloader</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <style type="text/css">
  .centered {
      text-align: center;
      width: 90%;
      margin: 0 auto;
    }
  #vid_url{
    word-break: break-all;
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
          <a class="navbar-brand" href="#">FBVD</a>
        </div>
        <div class="collapse navbar-collapse" id="facebook-video-downloader">
          <ul class="nav navbar-nav">
            <li class="active"><a href="./index.php">Facebook Video Downloader</a></li>
            <li><a href="./private.php">Private Video Downloader</a></li>
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
            <h1>Facebook Video Downloader </h1>
            <h2>Download Facebook Video</h2>

              <div class="input-group col-mg-12">
                  <input type="text" name="url" class="form-control" placeholder="Facebook Video URL" id="url">
                  <span class="input-group-btn"><a class="btn btn-primary" onclick="getDownloadLink();" id="download">Download!</a></span>
              </div>
        </div>
      </div>
      <div class="well" id="result" style="display:none;">
          <div id="bar"><p class="text-center"><img src="img/ajax.gif"></p></div>
          <div id="downloadUrl" style="display:none;">
            <div class="row">
              <div class="col-md-4"><p class="text-center"><b>Video Picture</b></p><p class="text-center" id="img"></p></div>
              <div class="col-md-4">
                  <p class="text-center"><b>Information</b></p>
                  <div class="col-sm-2">Title:</div>
                  <div class="col-sm-10" id="title"></div>
                  <div class="col-sm-2">Source:</div>
                  <div class="col-sm-10" id="src"></div>
              </div>
              <div class="col-md-4">
                <p class="text-center"><b>Download Link</b></p>
                <p class="text-center" id="sd"></p>
                <p class="text-center" id="hd"></p>
               </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <div class="well">
    <div class="centered">
      <span style="text-align:center;display:block;">© <?php echo date('Y') ?> <a href="https://www.kapadiya.net">Kapadiya.net</a></span>
    </div>
  </div>
</div>

<a href="https://github.com/vikas5914/Facebook-Video-Downloader"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/e7bbb0521b397edbd5fe43e7f760759336b5e05f/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f677265656e5f3030373230302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png"></a>

<script type="text/javascript" src="js/jquery.min.js" ></script>
<script type="text/javascript" src="js/bootstrap.min.js" ></script>
<script type="text/javascript">


function getDownloadLink(){
  var vid_url = $("#url").val();

  $("#download").html("Grabbing Link ...");
  $("#download").attr("disabled","disabled");
  $("#result").css("display","block");
  $("#downloadUrl").css("display","none");
  $("#bar").css("display","block");
  $("#hd").html('');
  $("#sd").html('');
  $.ajax({
    type:"POST",
    dataType:'json',
    url:'main.php',
    data:{url:vid_url},
    success:function(data){
      console.log(data);
      $("#bar").css("display","none");
      $("#downloadUrl").css("display","block");
      if(data.type=="success") {

        var img_link = $("#url").val().split("/")[5];
        $("#title").html(data.title);
        $("#img").html('<img class="img-thumbnail" src="https://graph.facebook.com/'+img_link+'/picture">');
        $("#src").html('<a id="vid_url" href="'+vid_url+'">'+vid_url+'</a>');
        $("#sd").html('<a href="'+data.sd_download_url+'" download="sd.mp4"><b>MP4 SD</b></a>');

        if(data.hd_download_url){
        $("#hd").html('<a href="'+data.hd_download_url+'" download="hd.mp4"><b>MP4 HD</b></a>');
        }
      }

      if(data.type=="failure"){
        $("#downloadUrl").html('<h3>'+data.message+'</h3>');
      }

      $("#download").html("Download!");
      $("#download").removeAttr("disabled");
    }
  })
}

</script>
</body>
</html>