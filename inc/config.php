<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

# script paths
//define("ABSOLUTE_PATH", 'http://mp32youtube.com/');
//define("RELATIVE_PATH", '/home/mp32yt/public_html/');

# contact 
define("WEBMASTER_EMAIL", 'robertddan@yahoo.com');

# database Constants
defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER')   ? null : define("DB_USER", "mp32yt_oim8");
defined('DB_PASS')   ? null : define("DB_PASS", "parola");
defined('DB_NAME')   ? null : define("DB_NAME", "mp32yt_autovi");

//defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
//defined('DB_USER')   ? null : define("DB_USER", "root");
//defined('DB_PASS')   ? null : define("DB_PASS", "");
//defined('DB_NAME')   ? null : define("DB_NAME", "_autovi");

# operatyng syststem type (if not windows comment the line bellow)
//define('OP_SYS', 'win');

# ffmpeg
if(OP_SYS == 'win'){
    define('FFMPEG_LIBRARY', 'C:\\xampp\\htdocs\\ffmpeg\\ffmpeg.exe'); //daca SO este windows
} else {
    define('FFMPEG_LIBRARY', '/usr/bin/ffmpeg'); //daca SO este linux
}
define('WATERMARK', '/home/mp32yt/public_html/img/watermark_1280x720.png');  //path la imaginea folosita ca logo

//define('DEL_SES_DIR', "ok");

# youtube stuff
define('YTDESCRIPTION', " - created at http://mp32youtube.com"); //descrierea folosita pe youtube
define('YTTAGS', "pictures, videos, slideshow, slide show, clips, spotlight, footage, songs, animation, mp32youtube");  //tags folosite pe youtube
define('YTDEVTAGS', "mp32youtube.com"); //tagurile folosite de developer

//define("UPLOADS", rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", substr(getcwd(), 0, -3)."uploads/")));
define("UPSES", rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", substr(getcwd(), 0, -3)."uploads/" . session_id() ."/"), "/"));
define('THUMBS', UPSES."/thumbs/"); //directorul pt thumbs
define('IMAGES', UPSES."/images/"); //dir. pt imagini
define('AUDIO', UPSES."/audio/"); //dir pentru mp3
define('VIDEO', UPSES."/video/"); //dir pt video
define('TMP', UPSES."/tmp/"); //dir folosit pentru fisierele temporare



?>