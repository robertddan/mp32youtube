<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

session_start();

if (!isset($_GET['t'])) {
    header('Location: ../index.php');
    return;
}

$imgid = $_GET['t'];

//define('IMGPATH', '/home/mp32yt/public_html/uploads/');
define("IMGPATH", rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", substr(getcwd(), 0, -3)."uploads/" . session_id() ."/images/".$imgid), "/"));
define("THUMBS", rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", substr(getcwd(), 0, -3)."uploads/" . session_id() ."/thumbs/"), "/"));

$mimeimg = array("image/jpeg", "image/jpg", "image/gif", "image/png");
$file_info = getimagesize(IMGPATH);


!in_array($file_info['mime'], $mimeimg) ? header('Location: ../404.shtml') : null ;

switch($file_info['mime']){
    case "image/jpeg":
        $ext = "jpg";
        break;
    case "image/jpg":
        $ext = "jpg";
        break;
    case "image/gif":
        $ext = "gif";
        break;
    case "image/png":
        $ext = "png";
        break;
}

//!file_exists(THUMBS."/".$imgid) ? $rt = createthumb(IMGPATH,THUMBS,$imgid,$ext) : null ;


$im = file_get_contents(THUMBS."/".$imgid);

header('content-type: ' . $file_info['mime']);
echo $im;



?>