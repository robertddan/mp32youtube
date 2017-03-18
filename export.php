<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

session_start();

if($_SESSION['video']['up'] == "none"){
    header("Location: http://".$_SERVER['SERVER_NAME']);
    exit();
}

function getcururl(){
    $da = basename(dirname(__FILE__)) == "public_html" || basename(dirname(__FILE__)) == "autovi"? null : "/".basename(dirname(__FILE__));
    echo "http://".$_SERVER['SERVER_NAME'] . $da;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Export video to YouTube - mp32youtube.com</title>
    <meta name="author" content="robert d. dan" />
    <meta name="keywords" content="mp3, mp3 to video, mp32youtube, mp32tube, tube, pictures, convertor, converter, videos, slideshow, slide show, slideshows, slide shows, clips, spotlight, footage, music, songs, animation, video, music video, trailers, film, YouTube, Facebook " />
    <meta name="description" content="Turn your photos and music into professional HD video in minutes. with mp32youtube.com, you can upload music to YouTube, fast, free and amazingly simple." />
    
    <link href="css/css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">

$(function(){
    
function export_to_youtube(op, fun){
    return $.ajax({url:"<?php getcururl();  ?>/youtube/",type:'GET',data:{'op':op},async:true,success:fun,error:errorHandler,dataType:'json'});
} 
    
function majax(op, id, fun){
    return $.ajax({url:'<?php getcururl();  ?>/inc/',type:'POST',data:{'op':op, 'id':id},async:true,success:fun,dataType:'json'});
}

function checkUpload(callback){
    if(callback[1] == "processing"){
        $("#exportVideo").hide();
        $("#exportVideoResponse").show();
        $("#exportVideoResponseM").html('<p>Your video has been exported!</p><br /><p>Your video should now be on YouTube, <br/>so head over there to <a style="text-decoration: underline;" target="_blank" href="http://www.youtube.com/watch?v=' + callback[0] + '">play it</a> or <a style="text-decoration: underline;" target="_blank" href="http://www.youtube.com/my_videos_edit?ns=1&amp;video_id=' + callback[0] + '">edit it</a>.</p>');
        $('#gotoyoutube').attr('href','http://www.youtube.com/watch?v='+callback[0]);
    }
        
    if(callback[0] == "error"){
        errorHandler();
    }
}

function errorHandler(){
    $("#exportVideo").hide();
    $("#exportVideoResponse").show();
    $("#exportVideoResponseM").show().html('<p>Error</p><br /><p>Sorry, there seems to be an error on the server. Please try again later.</p>');
}

function goBack(){
    window.location = "<?php echo "http://".$_SERVER['SERVER_NAME'] ?>";  
}
<?php
if(isset($_SESSION['video']['up']) && $_SESSION['video']['up'] == "ok"){
echo <<<IFEXPORT
export_to_youtube("viup", checkUpload);
IFEXPORT;
}
?>

$('#newVideo').click(function(){
    if(confirm('This will delete all your uploaded photos and music.')){
        majax("new","",goBack);
    }
});

});
</script>

</head>

<body>
<div id="wrapper">
    <table class="wrapper" width="800" border="0">
      <tr>
        <td width="98" rowspan="7" class="reel">&nbsp;</td>
        <td width="342">
            	<a href="index.php"><div id="logo"></div> </a>
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
        <td colspan="2"><div class="intre"></div></td>
      </tr>
      <tr>
        <td height="147" colspan="2" id="main" valign="top">


<div class="bgUpButton" style="float:left; margin-right: 5px;">
  <table width="135" height="330" border="0">
    <tr>
      <td height="10">
      
      </td>
      </tr>
    <tr>
      <td height="90">
        <div id="adsMenu">
    <?php
    if($_SERVER['SERVER_NAME'] != "localhost"){
    echo <<<GADS
            <script type="text/javascript"><!--
            google_ad_client = "pub-4108828129593596";
            /* 120x90, created 9/7/10 */
            google_ad_slot = "4798579175";
            google_ad_width = 120;
            google_ad_height = 90;
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
      <td height="85" valign="bottom">

      </td>
    </tr>
    <tr>
      <td height="130">
<div id="adsButton">
    <?php
    if($_SERVER['SERVER_NAME'] != "localhost"){
    echo <<<GADS
                
                    <script type="text/javascript"><!--
                    google_ad_client = "pub-4108828129593596";
                    /* 125x125, created 9/7/10 */
                    google_ad_slot = "5680232952";
                    google_ad_width = 125;
                    google_ad_height = 125;
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
  </table>
  
  
</div>

<div id="exportVideo" style="display:block;">
  <table style="float:right;" width="445" height="264" border="0">
    <tr >
      <td height="26" ><p class="bgBlu">Export video</p></td>
    </tr>
    <tr >
      <td height="10">&nbsp;</td>
    </tr>
    <tr  >
		<td height="204" valign="top" style="padding:5px;">
			<div id="exportVideoM" >
<p>Your video is being uploaded to YouTube.</p> <br/> <p> This process may take several minutes</p><p>Remember that YouTube will take some time to process it as well, so it may not be viewable for up to an hour.</p>
            </div>   
		</td>
    </tr>
    <tr  >
		<td style="padding:5px;" valign="top"></td>
    </tr>
  </table>
</div>

<div id="exportVideoResponse" style="display:none;">
  <table style="float:right;" width="445" height="328" border="0">
    <tr >
      <td height="26" colspan="4" ><p class="bgBlu">Export video</p></td>
    </tr>
    <tr >
      <td height="10" colspan="4">&nbsp;</td>
    </tr>
    <tr  >
		<td height="107" colspan="4" valign="top" style="padding:5px;">
			<div id="exportVideoResponseM" ></div>   
		</td>
    </tr>
    <tr  >
		<td width="40" height="169" valign="top" style="padding:5px;"></td>
		<td width="142" valign="top" style="padding:5px;"><p> <a id="gotoyoutube" class="button" href="" target="_blank">Go to YouTube</a> </p></td>
		<td width="115" valign="top" style="padding:5px;"><p> <a class="button" href="http://mp32youtube.com">edit video</a> </p></td>
		<td width="130" valign="top" style="padding:5px;"><p> <a class="button"  id="newVideo">new video</a> </p></td>
    </tr>
  </table>
</div>
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

<pre>
<?php 
//print_r($_SESSION);
?>
</pre>
</body>
</html>