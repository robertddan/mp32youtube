<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 */

//if (session_id() == ""){ session_start(); }
session_start();
//print_r($_GET['v']);
$get = htmlspecialchars($_GET['v']);
$sessid = array(35, 40, 48);
$error = 0;

if(!$get){
    $error++;
}

if(!in_array(strlen($get), $sessid)){
    $error++;
}

if($error != 0){
    //header('Location: ../index.php');
    //return;
}

$videoname = substr($get, -8).".flv";
$session_id = substr($get, 0, -8);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MP3 to YouTube converter - mp32youtube.com</title>
    <meta name="author" content="robert d. dan" />
    <meta name="keywords" content="mp3, mp3 to video, mp32youtube, mp32tube, tube, pictures, convertor, converter, videos, slideshow, slide show, slideshows, slide shows, clips, spotlight, footage, music, songs, animation, video, music video, trailers, film, YouTube, Facebook " />
    <meta name="description" content="Turn your photos and music into professional HD video in minutes. with mp32youtube.com, you can upload music to YouTube, fast, free and amazingly simple." />
    <link href="../css/css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="../js/swfobject.js"></script>
    <script type="text/javascript">
    function initPlayer(){
		var flashvars = {};
        <?php 
        $videopath = "http://". $_SERVER['SERVER_NAME'] ."/uploads/" . $session_id . "/video/" . $videoname;
        echo "var path = '".$videopath."';"; ?>
        <?php 
        $videoid = pathinfo($videopath, PATHINFO_FILENAME);
        echo "var image = 'http://". $_SERVER['SERVER_NAME'] ."/uploads/" . $session_id . "/video/" . $videoid .".jpg';"; 
        ?>
		flashvars.file = path;
		flashvars.image = image;
		flashvars.autostart = "false";
		flashvars.controlbar = "over";
        var params = {'allowfullscreen':true,'allowscriptaccess':'always','bgcolor':'#000000'};
        var attributes = {};
		swfobject.embedSWF("../swf/player.swf", "play_video", "585", "323", "9.0.0", false, flashvars, params, attributes);
    }
        
    $(document).ready(function(){
        initPlayer();
    });
    


            
	</script>

</head>

<body>
<div id="wrapper">
    <table class="wrapper" width="800" border="0">
      <tr>
        <td width="98" rowspan="7" class="reel">&nbsp;</td>
        <td width="342">
            	<a href="http://mp32youtube.com/"><div id="logo"></div> </a>
        </td>
        <td width="244">
            <div id="adsHeader">
    <?php
    if($_SERVER['SERVER_NAME'] != "localhost"){
    echo <<<GADS
        <script type="text/javascript"><!--
        google_ad_client = "pub-4108828129593596";
        /* 234x60, created 9/6/10 */
        google_ad_slot = "9582443908";
        google_ad_width = 234;
        google_ad_height = 60;
        //-->
        </script>
        <script type="text/javascript"
        src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>
GADS;
    }
    ?>
            </div>
        </td>
        <td width="98" rowspan="7" class="reel">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" valign="middle" >
        <div align="center" class="intre">
            <?php
    if($_SERVER['SERVER_NAME'] != "localhost"){
    echo <<<GADS
<script type="text/javascript"><!--
google_ad_client = "pub-4108828129593596";
/* 468x15, created 8/12/10 */
google_ad_slot = "5844715519";
google_ad_width = 468;
google_ad_height = 15;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
GADS;
    }
    ?>
        </div>
        </td>
      </tr>
      <tr>
        <td height="328" colspan="2" id="main" valign="top">
            <div id="play_video" ></div>
        </td>
      </tr>
      <tr>
        <td colspan="2" style="background-color: #666; height: 50px;"></td>
      </tr>
      <tr>
        <td colspan="2"><div id="footer"><p>&copy; 2010 <a href="http://mp32youtube.com">mp32youtube.com</a> | <a href="contact.php">contact us</a> |  <a style="text-decoration:underline">need help?</a></p></div></td>
      </tr>
    </table>
</div>

</body>
</html>