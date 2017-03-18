<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */


session_start();
//session_destroy();

function getcururl(){
    $da = basename(dirname(__FILE__)) == "public_html" || basename(dirname(__FILE__)) == "autovi"? null : "/".basename(dirname(__FILE__));
    echo "http://".$_SERVER['SERVER_NAME'] . $da;
}

function arrayToJSObject($array, $varname, $sub = false ) {
    $jsarray = $sub ? $varname . "{" : $varname . " = {\n";
    $varname = "\t$varname";
    reset ($array);

    // Loop through each element of the array
    while (list($key, $value) = each($array)) {
        $jskey = "'" . $key . "' : ";
       
        if (is_array($value)) {
            // Multi Dimensional Array
            $temp[] = arrayToJSObject($value, $jskey, true);
        } else {
            if (is_numeric($value)) {
                $jskey .= "$value";
            } elseif (is_bool($value)) {
                $jskey .= ($value ? 'true' : 'false') . "";
            } elseif ($value === NULL) {
                $jskey .= "null";
            } else {
                static $pattern = array("\\", "'", "\r", "\n");
                static $replace = array('\\', '\\\'', '\r', '\n');
                $jskey .= "'" . str_replace($pattern, $replace, $value) . "'";
            }
            $temp[] = $jskey;
        }
    }
    $jsarray .= implode(', ', $temp);

    $jsarray .= "}\n";
    return $jsarray;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>MP3 to YouTube converter - mp32youtube.com</title>
    <meta name="author" content="robert d. dan" />
    <meta name="keywords" content="mp3, mp3 to video, mp32youtube, mp32tube, tube, pictures, convertor, converter, videos, slideshow, slide show, slideshows, slide shows, clips, spotlight, footage, music, songs, animation, video, music video, trailers, film, YouTube, Facebook " />
    <meta name="description" content="Turn your photos and music into professional HD video in minutes. with mp32youtube.com, you can upload music to YouTube, fast, free and amazingly simple." />
    
    <link href="css/css.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="js/swfupload.js"></script>
    <script type="text/javascript" src="js/jquery.swfupload.js"></script>
<script type="text/javascript">

start = document.cookie.indexOf("PHPSESSID=");
var end = document.cookie.indexOf(";", start); // First ; after start
if (end == -1) end = document.cookie.length; // failed indexOf = -1
cookie = document.cookie.substring(start+10, end);

       
function addCommas(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function btokb(b) {
    var kb = b / 1024;
    return addCommas(Math.round(kb)) + " kb";
}

function thumblink(img){
    return 'uploads/' + cookie + '/thumbs/' + img + '?'+ (new Date()).getTime();
}

function majax(op, id, fun){
    return $.ajax({url:'<?php getcururl();  ?>/inc/',type:'POST',data:{'op':op, 'id':id},async:true,success:fun,dataType:'json'});
}

function handleUploadedImg(obj){
    //obj = eval('(' + unescape(obj) + ')');
    $imgList = $('#imgList');
    $thumb = $('<div></div>').addClass('thumb');
    $img = $('<img />');
    $img.attr({'src':thumblink(obj.id+"."+obj.ext), 'id':obj.id});
    $imgList.append($thumb.append($img.hide().load(function() {$(this).fadeIn(900)})));
    $('#upImgStatus').html('visuals: '+obj.cate);
    $('newVideo').show();
}

function addimage(object){
    $('#imgList').show();
    $.each(object, function(id, n) {
        $imgList = $('#imgList');
        $thumb = $('<div></div>').addClass('thumb').attr({'id':'wrap' + n.id});
        $img = $('<img />');
        $img.attr({'src':thumblink(n.id+"."+n.ext), 'id':n.id, 'alt':n.nameimg});
        $imgList.append($thumb.append($img.hide().load(function() {$(this).fadeIn(900)})));
                         
    });
}
    
function mlistimages(){
<?php 
    if(is_array($_SESSION['img'])){
        echo @arrayToJSObject($_SESSION['img'], object); 
        echo "addimage(object);";
    }
?>
} 



$(function(){
	$('#browseMusic').swfupload({
		upload_url: "upload/",
        post_params: {"what":"mp3", "PHPSESSID": cookie},
		file_size_limit : "15360", 
		file_types : "*.mp3",
		file_types_description : "mp3",
		file_upload_limit : "0",
        file_queue_limit : "0",
		flash_url : "swf/swfupload.swf",
		button_image_url : 'img/upmusic.png',
		button_width : 120,
		button_height : 26,
		button_placeholder : $('#buttonm')[0],
		//debug: true
	})
		.bind('fileQueued', function(event, file){
			//$('#upMp3Status').html('<li>File queued - '+file.name+'</li>');
			// start the upload since it's queued
            $(this).swfupload('setButtonDisabled', true);
			$(this).swfupload('startUpload');
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
			$('#upMp3Status').html('error: '+ message);
		})
        .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
            //$('#cancelUpMp3').show();
        })
		.bind('uploadStart', function(event, file){
		    $('#cancelMp3Upload').show();
            $('#wrapProgressBarMp3').show();
			//$('#upMp3Status').html('Upload start - '+file.name+'');
		})
		.bind('uploadProgress', function(event, file, bytesLoaded, bytesTotal){
			$('#upMp3Status').html('<img src="img/uploader.gif" /> '+file.name + ' ('+ btokb(file.size) +')');
            var percent = Math.ceil((bytesLoaded / file.size) * 100);
            
            $('#progressBarMp3').css('width',percent+'%');
            
		})
		.bind('uploadSuccess', function(event, file, serverData){
            obj = eval('(' + unescape(serverData) + ')');

            if(obj[0] == "error"){
                $('#upMp3Status').html('error: '+ obj[2] + " " + file.name);
            } else {
                $('#upMp3Status').html('soundtrack: '+ file.name);
                $('newVideo').show();
            }
			//$('#upMp3Status').html('Upload success - '+file.name);
		})
		.bind('uploadComplete', function(event, file){
			//$('#upMp3Status').html('Upload complete - '+file.name);
			// upload has completed, lets try the next one in the queue
			//$(this).swfupload('startUpload');
            $(this).swfupload('setButtonDisabled', false);
            $('#newVideo:hidden').show();
            $('#cancelMp3Upload').hide();
            $('#wrapProgressBarMp3').hide();
            
		})
		.bind('uploadError', function(event, file, errorCode, message){
			$('#upMp3Status').html('error: '+message);
		});
	
});


$(function(){
    var da = 0;
	$('#browseImages').swfupload({
		upload_url: "upload/",
        post_params: {"what":"image", "PHPSESSID": cookie},
		file_size_limit : "1024",
		file_types : "*.jpg;*.jpeg",
		file_types_description : "Image Files",
		flash_url : "swf/swfupload.swf",
		button_image_url : 'img/upimages.png',
		button_width : 120,
		button_height : 26,
		button_placeholder : $('#buttoni')[0],

		//debug: true
	})
    	.bind('fileQueued', function(event, file){
    		//$('#upImgStatus').append('File queued - '+file.name);
    		// start the upload since it's queued
    		$(this).swfupload('startUpload');
    	})
		.bind('fileQueueError', function(event, file, errorCode, message){
			$('#upImgStatus').html('error: '+message);
		})
        .bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
            
        })
		.bind('uploadStart', function(event, file){
		    $(this).swfupload('setButtonDisabled', true);
		    $('#cancelImgUpload').show();
            $('#wrapProgressBarImg').show();
			//$('#upImgStatus').html('Upload start - '+file.name);
		})
		.bind('uploadProgress', function(event, file, bytesLoaded){
            var percent = Math.ceil((bytesLoaded / file.size) * 100);
			$('#upImgStatus').html('<img src="img/uploader.gif" /> '+file.name + ' ('+ btokb(file.size) +')');
            $('#progressBarImg').css('width',percent+'%');
		})
		.bind('uploadSuccess', function(event, file, serverData){
			//$('#upImgStatus').html('Upload success - '+file.name);
            obj = eval('(' + unescape(serverData) + ')');
            if(obj[0] == "error"){
                $('#upImgStatus').html('error: '+ obj[2] + " " + file.name);
            } else {
                handleUploadedImg(obj);
            }
		})
		.bind('uploadComplete', function(event, file){
			//$('#upImgStatus').html('Upload complete - '+file.name);
			// upload has completed, lets try the next one in the queue
            $(this).swfupload('setButtonDisabled', false);
			$(this).swfupload('startUpload');
            $('#imgList:hidden').show();
            $('#newVideo:hidden').show();
            $('#cancelImgUpload').hide();
            $('#wrapProgressBarImg').hide();
		})
		.bind('uploadError', function(event, file, errorCode, message){
			$('#upImgStatus').html('error: '+message);
		});
	
});
// This event comes from the Queue Plugin
function queueComplete(numFilesUploaded) {
	var status = document.getElementById("intree");
	status.innerHTML = numFilesUploaded + " file" + (numFilesUploaded === 1 ? "" : "s") + " uploaded.";
}

$(function(){
    
$("#imgList").sortable({ cursor: 'move', update: function() {
    var a = [];
    $(this).children().each(function(i) {
        a[i]=$(this).children('img').attr("id"); 
    });
    majax('sh', a);										 
    }								  
});
    
$('#newVideo').click(function(){
    if(confirm('This will delete all your uploaded photos and music.')){
        $('#browseImages').swfupload('cancelUpload');
        $('#browseMusic').swfupload('cancelUpload');
        $('#imgList').hide().children().remove();
        $('#upImgStatus').text('visuals: 0');
        $('#upMp3Status').text('sountrack: none yet  ');
        $('#newVideo').hide();
        majax("new","","");
    }
});

$('#createVideo').click(function(){
    if($('.progressbar').is(':visible')) {
        if(confirm("Warning: You appear to have uploads in progress.  Click 'ok' to cancel your uploads and proceed.  Otherwise, click 'cancel' and try again after your uploads are finished.")){
            $('#browseImages').swfupload('cancelUpload');
            $('#browseMusic').swfupload('cancelUpload');
        } else {
            return;
        }
    } 
    
    $(window.location).attr('href', 'processing.php');
    
});

$('#cancelImgUpload').click(function(){
    $('#browseImages').swfupload('cancelUpload');
    //$('#logi').html('<liUpload canceled</li>');
});

$('#cancelMp3Upload').click(function(){
    $('#browseMusic').swfupload('cancelUpload');
    //$('#logm').html('<li>Upload canceled</li>');
});
    
$("#imgList").disableSelection();
$("#imgList").selectable();

<?php
if(is_array($_SESSION['img']) || is_array($_SESSION['img'])){
	echo "$('#newVideo').show();";
}
?>



mlistimages();
});

</script>

</head>

<body>
<div id="wrapper">
    <table class="wrapper" width="800" border="0">
      <tr>
        <td width="98" rowspan="10" class="reel">&nbsp;</td>
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
        <td width="98" rowspan="10" class="reel">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><div id="intree" class="intre"></div></td>
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
      	<span class="menuLink"><strong><u>mp32youtube</u></strong></span>
      	<br />
      	<a class="menuLinkc" href="http://mp32youtube.com/"><u>Create Video</u></a>
      	<br />
      	<a class="menuLinkc" href="http://mp32youtube.com/contact.php"><u>Contact Us</u></a>
      	<br />
      	<a class="menuLinkc" href="http://mp32youtube.com/help.php"><u>Need Help?</u></a>
        <div id="adsMenu">
    <?php
    if($_SERVER['SERVER_NAME'] == "localhost"){
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
      <td height="auto" valign="bottom">

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

  <table style="float:right;" width="445" height="330" border="0">
    <tr >
      <td height="26" colspan="5" ><p class="bgBlu">Upload mp3 to YouTube online</p></td>
    </tr>

    <tr>
      <td height="55" colspan="5" valign="center">
        <p>Turn your photos and music into professional HD video in minutes.</p>
        <p >With mp32youtube.com, you can upload music to YouTube, fast, free and amazingly simple.</p>
        </td>
    </tr>
    <tr class="bgUpButton"  height="48" >
      <td width="64" height="20" ><div class="steps">Step 1:</div></td>
      <td width="127">
        <div id="browseMusic">
          <input type="button" id="buttonm" class="button" value="Upload music"/>
          </div>
        </td>
      <td height="10" colspan="2" valign="middle" >
      	<div id="upMp3Status"><?php echo is_array($_SESSION['mp3']) ? "sountrack: ".$_SESSION['mp3']['filename'] : "sountrack: none yet";?></div>
        <div class="progressbar" id="wrapProgressBarMp3">
        	<div id="progressBarMp3"></div>
        </div>
      </td>
      <td width="60"><p id="cancelMp3Upload" class="linkHover" style="display: none;">Cancel</p></td>
    </tr>
    <tr class="bgUpButton"  >
      <td height="5" colspan="5" ><div id="soundtrack"></div> </td>
    </tr>

    <tr class="bgUpButton" >
      <td width="64" height="48"><div class="steps">Step 2:</div></td>
            <td width="127">
                <div id="browseImages">
                    <input type="button" class="button" id="buttoni" value="Upload images"/>
                </div>
      </td>
      <td colspan="2" valign="middle" >
            <div id="upImgStatus">
                <?php echo count($_SESSION['img'])!= 0 ? "visuals: ".count($_SESSION['img']) : "visuals: 0";?>
            </div>
        <div class="progressbar" id="wrapProgressBarImg">
            <div id="progressBarImg"></div>
        </div>
      </td>
      <td><p id="cancelImgUpload" class="linkHover" style="display: none;">Cancel</p></td>
      </tr>
      
	<div id="wrapImgList" style="display:none;">
        <tr class="bgUpButton wrapImgList" >
          <td height="75" colspan="5" valign="top" ><div id="imgList" style="display:none;"></div></td>
        </tr>
	</div>
        
    <tr class="bgUpButton" style="border-top: 5px solid #404040">
      <td width="64" height="50" ><div class="steps">Step 3:</div></td>
      <td><input type="button" class="button" id="createVideo" value="Create video"/></td>
      <td width="79"></td>
      <td width="93"><input style="display:none;" type="button" class="button" id="newVideo" value="New video"/></td>
      <td>&nbsp;</td>
    </tr>
  </table>
        </td>
      </tr>
      
<?php
    echo <<<LATEST
      <tr>
        <td colspan="2" style="background-color: #666; height: 50px;"></td>
      </tr>
      <tr>
        <td colspan="2" style="padding-top: 5px; padding-left: 5px;"> <p class="bgBlu" style="margin-right:5px;">Latest videos</p> </td>
      </tr>
      <tr>
        <td colspan="2" style="padding-top: 5px; padding-left: 5px;">
LATEST;

include "latest.php";

    echo <<<LATEST
    
    
        </td>
      </tr>
LATEST;
?>
      
      <tr>
        <td colspan="2" style="background-color: #666; height: 50px;"></td>
      </tr>
      <tr>
        <td colspan="2"><div id="footer"><p>&copy; 2010 <a href="http://mp32youtube.com">mp32youtube.com</a> | <a href="contact.php">contact us</a> |  <a style="text-decoration:underline">need help?</a></p></div></td>
      </tr>
    </table>
</div>
        
<!--
<table id="contactForm" width="100%" height="238" border="0">
  <tr>
    <td height="20" colspan="2">Contact us</td>
    <td width="50%" rowspan="7">advertisement</td>
  </tr>
  <tr>
    <td height="10" colspan="2"></td>
  </tr>
  <tr>
    <td height="20" colspan="2"><label>name</label><br /><input name="name" type="text" value="" /></td>
  </tr>
  <tr>
    <td height="10" colspan="2"><label>e-mail</label><br /><input name="email" type="text" value="" /></td>
  </tr>
  <tr>
    <td height="10" colspan="2"><label>message</label><br /><textarea name="textarea" id="textarea" ></textarea></td>
  </tr>
  <tr>
    <td height="20" colspan="2"></td>
  </tr>
  <tr>
    <td height="18" colspan="2">submit button</td>
  </tr>
</table>
-->
<pre>
<?php 
print_r($_SESSION);
?>
</pre>
</body>
</html>