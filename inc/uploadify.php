<?php

if (session_id() == ""){ session_start(); }
if (session_id() != $_POST['PHPSESSID']){
    session_id($_POST['PHPSESSID']);
    session_start();
}


require('class_resizeimage.php');

define("PATH", substr(getcwd(), 0, -3)); //return directory where index.php is, with "/" (if this file is in folder "inc")
define("UPFOLDER", PATH . "uploads/" . session_id());
define("SESID", session_id());
define("IMG", UPFOLDER . '/images/');
define("TMP", UPFOLDER . '/tmp/');
define("MP3", UPFOLDER . '/audio/');
define("VID", UPFOLDER . '/video/');
define("THB", UPFOLDER . '/thumbs/');

define("MAX_SIZE_IMG", "5242880");
define("MAX_SIZE_MP3", "15728640");


if (!file_exists(IMG) || !file_exists(MP3)){ 
    rmkdir(rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", IMG), "/"));
    rmkdir(rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", THB), "/"));
    rmkdir(rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", MP3), "/"));
    rmkdir(rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", TMP), "/"));
    rmkdir(rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", VID), "/"));
}

//if ($_SESSION['mydata'] != md5('whatever')) {
//        header("HTTP/1.0 404 Not Found");
//        exit;
//} 

     //$mda  = $newfileup->GetUploadFileExtension($_FILES['Filedata']['name']);
     //$newfileup->GetFileError($_FILES['Filedata']);

     
if (!empty($_FILES)) {

	$tempFile = $_FILES['Filedata']['tmp_name'];
    $name = $_FILES['Filedata']['name'];
    
    $type = $_POST['type'];
    //$image_extensions_allowed = array('jpg', 'jpeg', 'png', 'gif');
    //$mimeimg = array("image/jpeg", "image/jpg", "image/gif", "image/png");
    //$mimemp3 = array("audio/mpeg");
    
    //if (strstr($getID3->startup_error, "GETID3_HELPERAPPSDIR")) { $getID3->startup_error = ""; }
    //$fileinfo = $getID3->analyze($tempFile);    
    

    if($type == 'image'){
        $iusl = fileSizeInfo( MAX_SIZE_IMG );
        $filesize = fileSizeInfo( filesize($tempFile) );
        $ext = strtolower(substr(strrchr($tempFile, "."), 1));
        empty($ext) || $ext == "tmp" ? $ext = "jpg" : null ;
        $file_info = getimagesize($tempFile);
        
        if ($_FILES['Filedata']['size'] >= MAX_SIZE_IMG) { 
            echo json_encode(array("error", "<p>File size</p>", "<p>This file is too big. (". $filesize[0]." ".$filesize[1] .") <br />Size limit: " . $iusl[0]." ".$iusl[1].".</p>", $name)); 
            return; 
        }

        if(empty($file_info)) {
            echo json_encode(array("error", "<p>Corrupt File</p>", "<p>We could not read this file because it is corrupt. </p" , $name));
            return true;
        }

        //if(!in_array($ext, $image_extensions_allowed)){
        //    $exts = implode(', ',$image_extensions_allowed);
        //    echo json_encode(array("error", "<p>Corrupt File</p>", "<p>You must upload a file with one of the following extensions: ".$_FILES['Filedata']['type']."</p>" , $name));
        //    return true;
        //}

        //if($fileinfo['error'] || !in_array($fileinfo['mime_type'], $mimeimg)){
        //    echo json_encode(array("error", "<p>Corrupt File</p>", "<p>We could not read this file because it is corrupt. </p>" , $name));
        //    return true;
        //} 

        $rand_str = rand_str();
        $file_name = $rand_str . "." . $ext;
    	$targetFile =  IMG . $file_name;

        if(move_uploaded_file($tempFile,$targetFile)){
           if(file_exists(THB)){
                createthumb($targetFile,THB,$file_name,$ext);
                
                $new = array('id' => $rand_str, 
                            'name' => $file_name, 
                            'nameimg' => $name, 
                            'ext' => $ext);
                
                if(!is_array($_SESSION['img']) || !isset($_SESSION['img'])){
                    unset($_SESSION['img']);
                    $_SESSION['img'] = array();
                }
                
                $_SESSION['img'][] = $new;

                echo json_encode(array($rand_str, $ext, $name));
            } else {
                echo json_encode(array("error", "Your file was not uploaded try again ", " ", $name));
            }
        } else {
            echo json_encode(array("error", "Your file was not uploaded try again. ", " ", $name));
            //echo json_encode(array("error", "Failed to upload file.", $name));
        }
    
    }
    
    if($type == 'mp3'){
        $movie = new ffmpeg_movie($tempFile);
        $audioBitRate = $movie->getAudioBitRate();
        $audioDuration = $movie->getDuration();
        $musl = fileSizeInfo( MAX_SIZE_MP3 );
        $filesize = fileSizeInfo( filesize($tempFile) );

        //if($fileinfo['error'] || !in_array($fileinfo['mime_type'], $mimemp3)){
        if(empty($audioBitRate)){
            echo json_encode(array("error", "<p>Corrupt File</p>", "<p>We could not read this file because it is corrupt. </p>", $name));
            return;
        } 
        
        //if($fileinfo['playtime_seconds'] < 10){
        if($audioDuration < 10){
            //"That song is too short (" . $fileinfo['playtime_string'] . "s, must be more than 10 seconds long)."
        	echo json_encode(array("error", "<p>Song is too short</p>", "<p>'" . $name . "' is too short (" . $fileinfo['playtime_seconds'] . "s, must be more than 10 seconds long). </p>", $name)); 
        	return;  
        }

        if ($_FILES['Filedata']['size'] >= MAX_SIZE_MP3) { 
            echo json_encode(array("error", "<p>File size</p>", "<p>Sorry, '" . $name . "' is too big. <br />Files must be under " . $musl[0].$musl[1] . ". </p>", $name)); 
            return; 
        }
        

        $rand_str = rand_str();
        $file_name = $rand_str.".mp3";
    	$targetFile =  str_replace('//','/',MP3) . $file_name;
        
        if(move_uploaded_file($tempFile,$targetFile)){
            if(is_array($_SESSION['mp3'])){
                foreach ($_SESSION['mp3'] as $v){
                    @unlink(str_replace('//','/',MP3) . $v);
                }
            }
            $foundPath = @multi_unique($fileinfo);
            $id3v1 = @multi_unique($foundPath['id3v1']);
            //$artist = $id3v1['artist'];
            
            if($id3v1['artist'] == ""){
                $soundtrack = ' ';
                $artist = ' ';
                $title = ' ';
            } else {
                $artist = $id3v1['artist'];
                $title = $id3v1['title'];
                $soundtrack = $artist . " - " . $title;
            }
            
            
            $_SESSION['mp3'] = array('id'       => $rand_str, 
                                    'name'      => $file_name, 
                                    'path' 	    => 'uploads/' . SESID . '/audio/',
                                    'filename'  => $name,
                                    'artist'    => $artist,
                                    'title'     => $title);
                                    //'playtime'  => round($foundPath['playtime_seconds']));
                                    
            echo json_encode($_SESSION['mp3']);
            
        } else {
            echo json_encode(array("error", "", $name));
        }
    }
} else { echo "Whoops"; }

function fileSizeInfo($fs) { 
    $bytes = array('kb', 'kb', 'mb', 'GB', 'TB'); 
    // values are always displayed in at least 1 kilobyte: 
    if ($fs <= 999) { 
      $fs = 1; 
    } 
    for ($i = 0; $fs > 999; $i++) { 
      $fs /= 1024; 
    } 
    return array(ceil($fs), $bytes[$i]); 
} 

function formatbytes($file, $type){
	switch($type){
		case "kb":
			$filesize = filesize($file) * .0009765625; // bytes to KB
		break;
		case "mb":
			$filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
		break;
		case "gb":
			$filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
		break;
	}
	if($filesize <= 0){
		return $filesize = 'unknown file size';}
	else{return round($filesize, 2).' '.$type;}
}

function multi_unique($array) {
    foreach ($array as $k=>$na)
        $new[$k] = serialize($na);
    $uniq = array_unique($new);
    foreach($uniq as $k=>$ser)
        $new1[$k] = unserialize($ser);
    return ($new1);
}

function rand_str($length = 8, $chars = 'abcdefghijklmnoprstuwyzqxv'){
    $chars_length = (strlen($chars) - 1);
    $string = $chars{rand(0, $chars_length)};
    for ($i = 1; $i < $length; $i = strlen($string)){
        $r = $chars{rand(0, $chars_length)};
        if ($r != $string{$i - 1}) $string .=  $r;
    }
    return $string;
}

function rmkdir($path, $mode = 0755) {
    $path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
    $e = explode("/", ltrim($path, "/"));
    if(substr($path, 0, 1) == "/") {
        $e[0] = "/".$e[0];
    }
    $c = count($e);
    $cp = $e[0];
    for($i = 1; $i < $c; $i++) {
        if(!is_dir($cp) && !@mkdir($cp, $mode)) {
            return false;
        }
        $cp .= "/".$e[$i];
    }
    return @mkdir($path, $mode);
}

function createthumb($source,$dest,$filename,$typei,$border=0) {
    $new_width = 70; //
    $new_height = 70; //

    clearstatcache();
    $sourceinfo = stat($source);
    $destinfo = stat($dest);
    $sourcedate = $sourceinfo[10];
    $destdate = $destinfo[10];

    global $ImageTool;
    $imgsize = GetImageSize($source);
    $width = $imgsize[0];
    $height = $imgsize[1];
    
    if ($width > $height) { // If the width is greater than the height it's a horizontal picture
        $xoord = ceil(($width - $height) / 2 );
        $width = $height; // Then we read a square frame that equals the width
    } else {
        $yoord = ceil(($height - $width) / 2);
        $height = $width;
    }
    $new_im = ImageCreatetruecolor($new_width,$new_height);
    
    switch ($typei){
      case "jpg":   
      case "jpeg": 
        $im = ImageCreateFromJpeg($source);
        break;
      case "png":   
        $im = ImageCreateFromPng($source);
        break;
      case "gif": 
        $im = ImageCreateFromGif($source);
        break;
     }
     
    imagecopyresampled($new_im,$im,0,0,$xoord,$yoord,$new_width,$new_height,$width,$height);

    switch ($typei){
      case "jpg":   
      case "jpeg":  
        imagejpeg($new_im,$dest.$filename,90);
        break;
      case "png":   
        imagepng($new_im,$dest.$filename,0);
        break;
      case "gif":  
        imagegif($new_im,$dest.$filename,90);
        break;
     }
}

function resizeimage($img){
      
      $image = new SimpleImage();
      $image->load($img);
      $image->resize(1280,720);
      $image->output();
}
?>