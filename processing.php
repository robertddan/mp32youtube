<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

session_start();
//if ($_SESSION['allow'] != true) {
//    header('Location: index.php');
//    exit();
//}


function getcururl(){
    $da = basename(dirname(__FILE__)) == "public_html" || basename(dirname(__FILE__)) == "autovi"? null : "/".basename(dirname(__FILE__));
    echo "http://".$_SERVER['SERVER_NAME'] . $da;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Processing video - mp32youtube.com</title>
    <meta name="author" content="robert d. dan" />
    <meta name="keywords" content="mp3, mp3 to video, mp32youtube, mp32tube, tube, pictures, convertor, converter, videos, slideshow, slide show, slideshows, slide shows, clips, spotlight, footage, music, songs, animation, video, music video, trailers, film, YouTube, Facebook " />
    <meta name="description" content="Turn your photos and music into professional HD video in minutes. with mp32youtube.com, you can upload music to YouTube, fast, free and amazingly simple." />
    
    <link href="css/css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
function s1(callback){
    if(callback=="success"){
        //alert(callback);
        majax('s2', '', s4);
    }
    
	if(callback.error == "mp3"){
        jQuery('#errorProcessing').show();
        jQuery('#processing').hide();                            
		jQuery('#errorProcessingM').html("<p>This project has no soundtrack; render cannot be made.</p><p>Go back and upload mp3 file.</p>");
		
	}
    
	if(callback.error == "img"){
        jQuery('#processing').hide();   
        jQuery('#errorProcessing').show();    
		jQuery('#errorProcessingM').html("<p>This project has no visuals; render cannot be made.</p><p>Go back and upload one or more images.</p>");
		//jQuery('#error').show("slide", { direction: "down" }, 500);                           
	}

	if(callback.error == "exist"){
        window.location = "finalize.php";  
	}

	if(callback.error == "ocupat"){
	  //alert("ocupat");
      window.location = "index.php";  
        //update();
	}
}

function majax(op, id, fun){
    return $.ajax({url:'<?php getcururl();  ?>/inc/',type:'POST',data:{'op':op, 'id':id},async:true,success:fun,error:majaxError,dataType:'json'});
}

function majaxError(XMLHttpRequest, textStatus, errorThrown){
    jQuery('#errorProcessing').show();
    jQuery('#processing').hide();                            
    jQuery('#errorProcessingM').html("<p>There was en error in processing your request.</p><p>Go back and try again.</p>");

}

function s4(callback){
    //jQuery('#processing').hide();   


    if(callback.status==1) {
        window.location = "finalize.php";  
    } else {
        $('#processing').hide();
        $('#errorProcessing').show();    
		$('#errorProcessingM').html("<p>There was en error in processing your request.</p><p>Go back and try again.</p>");
    } 
    
    return true;
}

function goBack(){
    window.location = "http://<?php echo $_SERVER['SERVER_NAME']; ?>";  
}

$(function(){
    majax("s1","",s1);
});
</script>

</head>

<body>
<div id="wrapper">
    <table class="wrapper" width="800" border="0">
      <tr>
        <td width="98" rowspan="5" class="reel">&nbsp;</td>
        <td width="342">
            <a href="index.php"><div id="logo"></div></a>
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
        <td width="98" rowspan="5" class="reel">&nbsp;</td>
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


<div id="processing" style="display:block;">
  <table style="float:right;" width="445" height="330" border="0">
      <tr>
        <td height="26" ><p class="bgBlu">Processing video</p></td>
        </tr>
      <tr>
        <td height="26" style="padding:5px;">&nbsp;</td>
      </tr>
      <tr>
        <td style="padding:5px;"><p>Please wait... </p><p>mp32youtube.com is producing your video</p></td>
      </tr>
      <tr>
        <td height="119" style="padding:5px;">
            <div align="center">
                <img src="img/71.gif" />   
            </div>
		</td>
      </tr>
      <tr>
        <td height="106" style="padding:5px;" valign="top">
          <p>It will take us a few minutes to prepare your video creation.</p>
          </td>
        </tr>
  </table>
</div>

<div id="errorProcessing" style="display:none;">
  <table style="float:right;" width="445" height="293" border="0">
    <tr >
      <td height="26" ><p class="bgBlu">Error</p></td>
    </tr>
    <tr >
      <td height="10">&nbsp;</td>
    </tr>
    <tr  >
		<td height="74" valign="top" style="padding:5px;">
			<div id="errorProcessingM" ></div>   
		</td>
    </tr>
    <tr  >
		<td style="padding:5px;" valign="top">
			<p><input type="button" id="goBack" value="go back" onclick="javascript: goBack();" class="button" /></p>   
		</td>
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