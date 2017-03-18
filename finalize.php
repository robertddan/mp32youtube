<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

session_start();

if (!isset($_SESSION['video']) && !is_array($_SESSION['video']) && $_SESSION['video']['status'] != 1) {
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
    <title>Preview video - mp32youtube.com</title>
    <meta name="author" content="robert d. dan" />
    <meta name="keywords" content="mp3, mp3 to video, mp32youtube, mp32tube, tube, pictures, convertor, converter, videos, slideshow, slide show, slideshows, slide shows, clips, spotlight, footage, music, songs, animation, video, music video, trailers, film, YouTube, Facebook " />
    <meta name="description" content="Turn your photos and music into professional HD video in minutes. with mp32youtube.com, you can upload music to YouTube, fast, free and amazingly simple." />
    
    <link href="css/css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript">

$(function(){
    
function majax(op, id, fun){
    return $.ajax({url:'<?php getcururl();  ?>/inc/',type:'POST',data:{'op':op, 'id':id},async:true,success:fun,dataType:'json'});
}

function redirect(){
    window.location = "<?php echo "http://".$_SERVER['SERVER_NAME'] ?>";  
}

function videoplayer(id, path, image){
    var flashvars = {'file':path, 'image':image, 'autostart':false, 'controlbar':'over'};
    var params = {'allowfullscreen':true,'allowscriptaccess':'always','bgcolor':'#000000'};
    var attributes = {};
    swfobject.embedSWF("swf/player.swf", "videoplayer", "445", "251", "9.0.24", false, flashvars, params, attributes);
}

$('#videoExport').click(function(){

<?php 

//if(isset($_SESSION['sessionToken'])){
//	$_SESSION['video']['up']  = "ok";
//	echo "window.location = 'http://".$_SERVER['SERVER_NAME']."/export.php';";
	
//} else {
	echo "window.location = 'https://www.google.com/accounts/AuthSubRequest?next=http%3A%2F%2Fmp32youtube.com%2Fyoutube%2F&scope=http://gdata.youtube.com&secure=&session=1';";
//}


?>
    
}); 

$('#videoDownload').click(function(e) {
    e.preventDefault();  //stop the browser from following
    window.location.href = '<?php echo $_SESSION['video']['path'].$_SESSION['video']['name']; ?>';
});

$('#editVideo').click(function(){
    window.location = "<?php getcururl(); ?>";
});

$('#newVideo').click(function(){
    if(confirm('This will delete all your uploaded photos and music.')){
        majax("new","",redirect);
    }
});

videoplayer("videoplayer", "<?php echo $_SESSION['video']['path'].$_SESSION['video']['name']; ?>", "<?php echo $_SESSION['video']['image']; ?>");

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
    if($_SERVER['SERVER_NAME'] == "localhost"){
    echo <<<GADS
            <script type="text/javascript">
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


<div style="display:block;">
  <table style="float:right;" width="445" height="330" border="0">
      <tr>
        <td height="26" colspan="4" ><p class="bgBlu">Upload mp3 to YouTube online</p></td>
      </tr>
      <tr>
      <td height="256" colspan="4" valign="top" >
        
        
        
        <div id="preview_video" style="display: block;">
          <div id="videoplayer" align="center" ></div>
        <!-- end #preview_video --></div>
        
        
        
        </td>
    </tr>
      <tr valign="center" style="padding:5px;"> 
        <td width="40" >&nbsp;</td>
        <td width="158"><input type="button" class="button" id="videoExport" value="Upload to YouTube"/></td>
        <td width="106"><input type="button" class="button" id="videoDownload" value="Download video"/></td>
        <td width="127"><input type="button" class="button" id="newVideo" value="New video"/></td>
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