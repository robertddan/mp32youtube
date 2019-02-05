<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

session_start();

require('class_thumbnail.php');
require('database.php');
include 'config.php';
//$_SESSION['developerKey'] = '';

if (!isset($_POST['op'])) {
    // if a GET variable is set then process the token upgrade
        header('Location: ../index.php');
}

$operation      = formspecialchars($_POST['op']);
$imgid          = formspecialchars($_POST['id']);

if($operation){
    switch ($operation){
        case "s1":
            checkfiles($imgid);
            //copyimg($_SESSION['img']);
          break;
        case "s2":
            premakevideo();
            //resizeimg($_SESSION['img']);
          break;
        case "s4":
            makevideo();
          break;
        case "st":
            savetrack($imgid);
          break;
        case "gi":
            getinfo();
          break;
        case "de":
            delete_image($imgid);
          break;
        case "ro":
            rotate_thimg($imgid);
          break;
        case "du":
            duplicate_image($imgid);
          break;
//        case "mkdir":
//            makeatvdir();
//          break;
        case "deleteall":
            deleteallimg();
          break;
        case "getli":
            getlastimage($imgid);
          break;
        case "sh":
            shuffle_images($imgid);
          break;
        case "sortable":
            sortable($_REQUEST['arr']);
          break;
        case "newarray":
            newarray($_REQUEST['arr']);
          break;
        case "queuefull":
            queuefull();
          break;
        case "new":
            new_project();
          break;
        case "test":
            delete_uploads();
          break;
        case "sendemail":
            //echo json_encode($_POST['name']);
            send_email($_POST['name'], $_POST['email'], $_POST['message']);
          break;
        default:
          echo json_encode("Whoopss");
    }
}

function send_email($name, $email, $message){    
    $name = stripslashes($name);
    $email = trim($email);
    $subject = "mp3 2 video";
    $message = stripslashes($message);
    
    $error = '';

    // Check name
    if(!$name){
        $error .= 'Please enter your name.<br />';
    }

    // Check email
    if(!$email){
        $error .= 'Please enter an e-mail address.<br />';
    }

    if($email && !ValidateEmail($email)){
        $error .= 'Please enter a valid e-mail address.<br />';
    }

    // Check message (length)   
    if(!$message || strlen($message) < 15){
        $error .= "Please enter your message. It should have at least 15 characters.<br />";
    }


    if(!$error){
		//$emailTo = 'oikilla@yahoo.com'; //Put your own email address here
		//$body = "Name: $name \n\nEmail: $email \n\nSubject: $subject \n\nComments:\n $comments";
		//$headers = 'From: ' . $_SERVER['SERVER_NAME'] . ' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
        
        //echo json_encode($emailTo, $subject, $body, $headers);
		//mail($emailTo, $subject, $body, $headers);
        
        $body = "Name: ". $name .
                "\n\nEmail: " . $email .
                "\n\nSubject: " . $subject .
                "\n\nIP: " . $_SERVER['REMOTE_ADDR'] .
                "\n\nComments: \n\n" . $message;
        //$headers = 'From: ' . $_SERVER['SERVER_NAME'] . ' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
        
        $mail = mail(WEBMASTER_EMAIL, $subject, $body,
         "From: ".$_SERVER['SERVER_NAME']." <".$email.">\r\n"
        ."Reply-To: ".$email."\r\n"
        ."X-Mailer: PHP/" . phpversion());
        
        if($mail){
            echo json_encode('ok');
        }
    
    } else {
        echo json_encode(array("error",$error));
        ///echo '<div class="notification_error">'.$error.'</div>';
    }
}

function ValidateEmail($email){
    $regex = '/([a-z0-9_.-]+)'. # name
    
    '@'. # at
    
    '([a-z0-9.-]+){2,255}'. # domain & possibly subdomains
    
    '.'. # period
    
    '([a-z]+){2,10}/i'; # domain extension 
    
    if($email == '') { 
    	return false;
    }
    else {
    $eregi = preg_replace($regex, '', $email);
    }
    
    return empty($eregi) ? true : false;
}
    
function checkfiles($arr){ 

    if(!isset($_SESSION['mp3']) && !is_array($_SESSION['mp3'])){
		die(json_encode(array("error" => "mp3")));  
    }
    
    if(!isset($_SESSION['img']) && !is_array($_SESSION['img'])){
		die(json_encode(array("error" => "img")));  
    }

    if($_SESSION['oldmp3'] === $_SESSION['mp3'] && $_SESSION['oldimg'] === $_SESSION['img']){
        die(json_encode(array("error" => "exist")));    
    }
    
    //if($_SESSION['allow'] != true){
    //    die(json_encode(array("error" => "ocupat")));    
    //}
    
    unset($_SESSION['video']);
    //$_SESSION['allow'] = false;

    $_SESSION['oldmp3'] = $_SESSION['mp3'];
    $_SESSION['oldimg'] = $_SESSION['img'];

    $_SESSION['video']['slider'] = $arr[0];
    $arr[1] == "" ? $_SESSION['tube']['title'] = "Video title" : $_SESSION['tube']['title'] = $arr[1];
    $_SESSION['tube']['desc'] = $arr[2];

    //$sql = "SELECT COUNT(*) FROM `queue`";
    //$result_set = $database->query($sql);
    //$mda = mysql_fetch_array($result_set);
    
    //if($mda[0] == 0){
    //    $sql = "INSERT INTO `mp32yt_autovi`.`queue` (`queue_id`) VALUES (".$videoFileName.");";
    //    $result = $database->query($sql);
    
    
    $copyimg = copyimg($_SESSION['img']);
    
    if($copyimg){
        echo json_encode("success");
        
    } else {
        echo json_encode("error","No /tmp directory"); 
    }
    

    
    //} else {
    //    echo json_encode(array("full" => $mda[0])); 
    //    return;
    //}
}

function delete_uploads(){
    $as = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", substr(getcwd(), 0, -3)."uploads/"), "/");
    $dd = destroydir($as,1);
    echo $dd;
}

function queuefull(){
    sleep(10);
    checkfiles();
}

function copyimg($img){   
    $dd = destroydir(TMP,0);
    if(file_exists(TMP)){
        $i=1; 
        foreach ( $img as $filename) {
            $n = str_pad($i, 2 ,"0",STR_PAD_LEFT).".".$filename['ext'];
            if(!@copy( IMAGES.$filename['name'],TMP.$filename['name'])){
                $i+=1;
                echo json_encode(array("error" => "Can't copy images."));
                //unset($_SESSION['tmp']);
                return;
            } 
        }
        //echo json_encode("s2"); 
        return true;
    } else {
        return false;
		//$sql2 = "DELETE FROM `mp32yt_autovi`.`queue` WHERE `queue`.`queue_id` = ".$_SESSION['video']['id']." LIMIT 1;";
		//$result1 = $database->query($sql2);
        //echo json_encode("error","No /tmp directory"); 

    }
}

function resizeimg($img){
    $i=1; 
    //unset($_SESSION['tmp']);
    //$image = new SimpleImage();
          
    foreach ( $img as $filename) {
        $n = str_pad($i, 2 ,"0",STR_PAD_LEFT).".".$filename['ext'];
        $d = TMP.$filename['name'];
        
        $returnimg = resample_picfile($d, $d, $filename['ext'], 1280, 720);
        
        //$thumb=new Thumbnail($d);	
        //$thumb->size(640,360);
        //$thumb->output_format=$filename['ext'];
        //$thumb->process($d);   
        //$thumb->save($d);
        
        //$_SESSION['tmp'][] = array( $n => $filename['name']);

        $i+=1; 
    }
    //echo json_encode("s3");
    return $returnimg;
}

function renameimg($img){
    $i=1; 
    foreach ( $img as $filename) {
        //$n = str_pad($i, 2 ,"0",STR_PAD_LEFT).".".$filename['ext'];
        $n = str_pad($i, 2 ,"0",STR_PAD_LEFT).".jpg";
        if(!@rename(TMP.$filename['name'], TMP . $n)){
            echo json_encode(array("error" => "Can't rename images."));
            return;
        } 
        $i+=1;
    }
    //echo json_encode("s4");
    return true;
}

function premakevideo(){
    $resize = resizeimg($_SESSION['img']);
    if($resize){
        $rename = renameimg($_SESSION['img']);
    } else {
        echo "error";
    }
    
    if($rename){
        makevideo();
    } else {
        echo "error";
    }
}

function makevideo(){

    //$database2 = new MySQLDatabase();
    $status = 0;
    $vid_id = rand(19999999,99999999);
    $vid_name = $vid_id.".avi";
    $flv_name = $vid_id.".flv";
    //$_SESSION['video']['id']   = $videoFileName;
    //$_SESSION['video']['name'] = $avi_name;
    $vid_filepath = VIDEO.$vid_name;
    $flv_filepath = VIDEO.$flv_name;
    //$fadeinout = VIDEO.$vid_name."fadet.avi";

    $datatime      = date("Y-m-d H:i:s");

    //if(!isset($_SESSION['slider'])){ 
    //    $slider = 2; 
    //} 
    switch ($_SESSION['video']['slider']){
        case "-2":
            $slider = 0.4; 
          break;
        case "-1":
            $slider = 0.2; 
          break;
        case "0":
            $slider = 1; 
          break;
        case "1":
            $slider = 4; 
          break;
        case "2":
            $slider = 8; 
          break;
        default:
          $slider = 2; 
    }
    
    $vhookd = "-vhook '/usr/lib64/vhook/drawtext.so -f /home/mp32yt/arial.ttf -b -x 10 -y 10 -t mp32youtube.com -c #FFFFFF -C #000000'";
    $vhookw = "-vhook '/usr/lib64/vhook/watermark.so -f ".WATERMARK."'";
    $vid_avi_com = FFMPEG_LIBRARY." -loop_input -r " . $slider . " -i " . 
                TMP . "%02d.jpg -r " . $slider . " -i " . 
                AUDIO . $_SESSION['mp3']['name'] . " -acodec copy -vcodec flv -qscale 2 -ab 128k -g 5 -cmp 3 -subcmp 3 -mbd 2 " . //-s 1280x720 " .
                "-y " . $flv_filepath . " -shortest ";// . $vhookw;

    # Text fade-out  
    //$fade_out = FFMPEG_LIBRARY." -i " . $videoFilepath . " -vhook '/usr/lib64/vhook/imlib2.so -t Hello -A max(0,255-exp(N/47))' -sameq " . $fadeinout;
    
    # scrolling credits from a graphics file
    //$scrolling = FFMPEG_LIBRARY." -sameq -i " . $videoFilepath . " -vhook '/usr/lib64/vhook/imlib2.so -x 0 -y -5.0*N -i /home/mp32yt/public_html/css/images/watermark_1280x720.png' " . $fadeinout;

    //$mda = exec($com_mpg);
    if(OP_SYS == "win"){
        $mda = exec($vid_avi_com);
    } else {
        $output1 = runExternal( $vid_avi_com, &$codeo );

        if( !$codeo ) {
            $status = 1;
        }   

    }

    //$output2 = runExternal( $scrolling, &$code );
    //if( !$code ) {
    //    $status = 1;
    //} 
    
    //echo $code ."--->".$output2;
    //return;
	//echo $com_avi . " video name: ". $_SESSION['video']['id'];
    
    //set_time_limit(120);
    
    
    //$sql2 = "DELETE FROM `mp32yt_autovi`.`queue` WHERE `queue`.`queue_id` = ".$_SESSION['video']['id']." LIMIT 1;";
    //$result2 = $database->query($sql2);
	
    if(file_exists($flv_filepath) || @filesize($flv_filepath) != 0){
        //$com_flv = FFMPEG_LIBRARY . " -i " . $videoFilepath . " -ab 56 -ar 44100 -b 200 -r 15 -f flv ".$flvFilepath;
        //$com_flv = FFMPEG_LIBRARY . " -y -i " . $videoFilepath . " -acodec mp3 -ar 22050 -f flv ".$flvFilepath;
        //$vid_flv_com  =  FFMPEG_LIBRARY . " -i " . $vid_filepath . " -y -b 800 -r 25 -f flv -vcodec flv -acodec -ab 128 -ar 44100 ".$flv_filepath;

        //$mda2 = exec($com_flv);
        //$output2 = runExternal( $vid_flv_com, &$code );
        //if( !$code ) {
        //    $status = 1;
        //}
        
        //if(file_exists($flv_filepath)){
            
        $prewimg                     = $_SESSION['img'][0];
        $_SESSION['video']['id']     = $vid_id;
        $_SESSION['video']['name']   = $flv_name;
        $_SESSION['video']['path']   = "http://" . $_SERVER['SERVER_NAME'] . "/uploads/" . session_id() . "/video/";
        $_SESSION['video']['image']  = "http://" . $_SERVER['SERVER_NAME'] . "/uploads/" . session_id() . "/images/" . $prewimg['name'];
        $_SESSION['video']['uppath'] = substr(getcwd(), 0, -3)."uploads/" . session_id() ."/video/";
        $_SESSION['video']['status'] = 1;
        
        is_array($_SESSION['videos']) ? null : $_SESSION['videos'] = array();
        $_SESSION['videos'][] = array("session_id" => session_id(),
                                    "img"       => $_SESSION['img'],
                                    "mp3"       => $_SESSION['mp3'],
                                    "video"       => $_SESSION['video']
                                    );
        
        $sql = "INSERT INTO `mp32yt_autovi`.`videos` (
                                                    `ID` ,
                                                    `no_pic` ,
                                                    `mp3_name` ,
                                                    `client_ip` ,
                                                    `play_link`,
                                                    `date`
                                                    )
                                                    VALUES (
                                                    'null', 
                                                    '" . count($_SESSION['img']) . "', 
                                                    '" . $_SESSION['mp3']['filename'] . "', 
                                                    '" . $_SERVER['REMOTE_ADDR'] . "', 
                                                    '" . session_id() . $_SESSION['video']['id'] . "',
                                                    '" . $datatime . "'
                                                    )";
        $firstimg = $_SESSION['img'][0];        
        copy(IMAGES.$firstimg['name'],VIDEO.$_SESSION['video']['id'].".jpg");
        
        if($_SESSION['mp3']['filename'] != "funguz.mp3"){
            $database = new MySQLDatabase();
            $result = $database->query($sql);
        }
        
        //$_SESSION['allow'] = true;
        
        echo json_encode( $_SESSION['video'] ); 
        //} else {
        //    $_SESSION['video'] = $status;
        //    echo json_encode( $_SESSION['video'] ); 
        //}

    } else {
        //$_SESSION['allow'] = true;
        //$_SESSION['video'] = $status;
        echo json_encode( "error" ); 
    }
}

function new_project(){
    if(DEL_UPLOADS_DIR == "ok"){
      $ss = destroydir(UPLOADS,1);  
        echo "uploads deleted " . $ss;
    }
    unset($_SESSION['img']);
    unset($_SESSION['mp3']);
    unset($_SESSION['tmp']);
    unset($_SESSION['video']);
    $tu = destroydir(THUMBS,1);
    $im = destroydir(IMAGES,1);
    $au = destroydir(AUDIO,1);
    $tm = destroydir(TMP,1);
    return true;
}

function getinfo(){        
    $im = 0;
    $mp = 0;
    $vi = 0;

    if($_SESSION['video']['status']==1 && file_exists($_SESSION['video']['uppath'].$_SESSION['video']['name'])){
        //$vi = array("title" => $_SESSION['video']['title'],
        //            "desc"  => $_SESSION['video']['desc'],
        //            "name"  => $_SESSION['video']['name'],
        //            "path"  => $_SESSION['video']['path'],
        //            "image" => $_SESSION['video']['image'],
        //            "status" => $_SESSION['video']['status']);
        $vi = $_SESSION['video'];
    } 
     
    if(isset($_SESSION['img']) && !empty($_SESSION['img'])){
        

        foreach ($_SESSION['img'] as $v1) {
            
            if(!$v1){
                unset($_SESSION['img']);
            }

            if(!is_array($v1)){
                deleteFromArray($_SESSION['img'],$v1 );
            } else {
                
                foreach ($v1 as $v2 => $v3) {
                                              
                    if(!file_exists(IMAGES.$v1['name'])){
                        deleteFromArray($_SESSION['img'],$v1 );
                    }
                    
                    $im = array();
                    $im[$v1['id']] = array($v1['ext'], $v1['nameimg']);
                }
                
                
                //echo json_encode($v1[id]);
            }
        }
    } 
    
    if(isset($_SESSION['mp3']) && !empty($_SESSION['mp3'])){
        if(!is_array($_SESSION['mp3'])){
            $mp == 0;
        }
        
        if(!file_exists(AUDIO.$_SESSION['mp3']['name'])){
            $mp == 0;
        } else {
            $mp = array("id"        => $_SESSION['mp3']['id'],
            			"path"	   => "uploads/" . session_id() . "/audio/" .$_SESSION['mp3']['name'],
                        "filename" => $_SESSION['mp3']['filename'],
                        "title"    => $_SESSION['mp3']['title'],
                        "artist"   => $_SESSION['mp3']['artist']);
        }
                
    }
    
    echo json_encode(array("img" => $im, "mp3" => $mp, "vi" => $vi));
}

function deleteFromArray(&$array, $deleteIt, $useOldKeys = FALSE){
    $key = array_search($deleteIt,$array,TRUE);
    if($key === FALSE)
        return FALSE;
    unset($array[$key]);
    if(!$useOldKeys)
        $array = array_values($array);
    return TRUE;
}

function resizeimage($img){
      $image = new SimpleImage();
      $image->load($img);
      $image->resize(1280,720);
      //$img = $image->output();
      $image->save($img);
      //echo json_encode(array($image));
}

function savetrack($imgid){
    $_SESSION['mp3']['artist'] = $imgid[0];
    $_SESSION['mp3']['title'] = $imgid[1];
    $_SESSION['mp3']['soundtrack'] = $imgid[0] . " - " . $imgid[1];
    echo json_encode("ok");
}


//function makeatvdir(){
//    $targetPath = $_SERVER['DOCUMENT_ROOT'] . UPSES . '/';
//    $thumbdir   = $_SERVER['DOCUMENT_ROOT'] . UPSES . '/' . 'thumbs' . '/';
//    $audiodir   = $_SERVER['DOCUMENT_ROOT'] . UPSES . '/' . 'audio' . '/';
//    $mda = rmkdir(str_replace('//','/',$targetPath));
//    rmkdir(str_replace('//','/',$thumbdir));
//    rmkdir(str_replace('//','/',$audiodir));
//    echo $mda;
//    //@mkdir(str_replace('//','/',$targetPath), 0755, true);
//    //@mkdir(str_replace('//','/',$thumbdir), 0755, true);
//    //@mkdir(str_replace('//','/',$audiodir), 0755, true);
//}

function newarray($arr){
    if($arr){
        $newArray=array();
        
        foreach($arr as $k=>$v){
            $v = $v.".jpg";
            $newArray[$k]=$v;
        }
    
        $_SESSION['img'] = $newArray;
    
        echo json_encode("success");
    }    
}

function deleteallimg(){
    $a = substr(getcwd(), 0, -3)."uploads/" . session_id() ."/images/";
    $b = substr(getcwd(), 0, -3)."uploads/" . session_id() ."/thumbs/";
    $c = substr(getcwd(), 0, -3)."uploads/" . session_id() ."/tmp/";
    $d = substr(getcwd(), 0, -3)."uploads/" . session_id() ."/video/";
    
    unset($_SESSION['img']);
    
    if(is_dir($a)){
        if(is_dir($b)){
            destroydir($a,0);
            destroydir($b,0);
            //destroydir($c,1);
            //destroydir($d,1);
            echo json_encode("success");
        }else{ 
            echo json_encode("error");
        }
    }else{ 
        echo json_encode("error");
    }
}

function destroydir($dir, $sidir, $virtual = false){
	$ds = DIRECTORY_SEPARATOR;
	$dir = $virtual ? realpath($dir) : $dir;
	$dir = substr($dir, -1) == $ds ? substr($dir, 0, -1) : $dir;
	if (is_dir($dir) && $handle = opendir($dir)){
		while ($file = readdir($handle)){
			if ($file == '.' || $file == '..'){
				continue;
			}elseif (is_dir($dir.$ds.$file)){
				destroydir($dir.$ds.$file,1);
			}
			else{
				unlink($dir.$ds.$file);
			}
		}
		closedir($handle);
        if($sidir==1){
		  rmdir($dir);
        }
		return true;
	} else {
		return false;
	}
}


function sortable($arr){
    $newArray=array();
    
    foreach($arr as $k=>$v){
        //$v = $v.".jpg";
        $newArray[$k]=$v;
    }
    $_SESSION['img'] = $newArray;
    print_r($newArray);  
}

function shuffle_images($arr){
    $new = array();
    $sa  = $_SESSION['img'];
	
    if(is_array($arr)){
		foreach($arr as $id){
			//echo $id." -id ";
			foreach($sa as $k => $v){
				if($id == $v['id']){
					$new[] = array('id' => $v['id'],
								   'name' => $v['name'],
								   'nameimg' => $v['nameimg'],
								   'ext' => $v['ext']);
				}
			}
		} 
		$_SESSION['img'] = $new;
	}
    
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

function duplicate_image($varid){
    $a = $_SESSION['img']; 
    
    foreach($a as $k=>$v){
        if($v['id'] == $varid) {
            $id = rand_str();
            $imgpath = IMAGES.$v['name'];
            $imgthumb = THUMBS.$v['name'];
            $ext = $v['ext'];
            $n = $id.".".$ext;
            $nameimg = $v['nameimg'];
            $key = $k+1;
            
            $new = array($key => array('id' => $id,
                                  'name'   => $n,
                                  'nameimg'=> $v['nameimg'],
                                  'ext'    => $v['ext']));

            if (copy($imgpath, IMAGES.$n) && copy($imgthumb,THUMBS.$n) ) {
                array_splice($_SESSION['img'], $key, 0, $new);
                
                $ar = array($v['id'],$id,$v['ext']);
                
            } else {
                $ar = array("error" => ""); 
            }
    
        }        
    }

    print json_encode(array("du" => $ar)); 

}

function delete_image($imgid){
    
    $a = $_SESSION['img']; 
    
    $id = rand_str();
  
    foreach($imgid as $id){
        foreach($a as $k=>$v){
            if($v['id']==$id) {
                unset($a[$k]);  
                unlink(IMAGES.$v['name']);
                unlink(THUMBS.$v['name']);   
            }
        }
        $_SESSION['img'] = $a; 
    }
    
    if(empty($_SESSION['img'])){
        unset($_SESSION['img']);
    }
    
    if(!file_exists($imgid[0])){
        print json_encode("ok");
    }
}

function rotate_thimg($imgid){
    
    $a = $_SESSION['img']; 
    $id = rand_str();
    $ceiasa = array();
        
    foreach($a as $k=>$v){
        if(in_array($v['id'], $imgid)) {
            $img = rotate_image($v['name'], $v['ext'], IMAGES, "", -90, 100, 1);
            $thumb = rotate_image($v['name'], $v['ext'], THUMBS, "", -90, 100, 1);
            $imgname = $v['name'];
                        
            if($img || $thumb){
                $ceiasa[$v['id']] = $v['ext'];
            }                     
        }                
    }
    echo json_encode(array("ro" => $ceiasa)); 

}

function rotate_image($img, $imagetype, $imgPath, $suffix, $degrees, $quality, $save){
    //$imagename = $im.".jpg";
    // Open the original image.
        switch ($imagetype){
          case "jpg":   
            $original = imagecreatefromjpeg($imgPath . $img) or die("Error Opening original");
            break;
          case "png":   
            $original = imagecreatefrompng($imgPath . $img) or die("Error Opening original");
            break;
          case "gif": 
            $original = imagecreatefromgif($imgPath . $img) or die("Error Opening original");
            break;
         }
         
    
    list($width, $height, $type, $attr) = getimagesize($imgPath . $img);

    // Resample the image.
    $tempImg = imagecreatetruecolor($width, $height) or die("Cant create temp image");
    imagecopyresized($tempImg, $original, 0, 0, 0, 0, $width, $height, $width, $height);// or die("Cant resize copy");

    // Rotate the image.
    $rotate = imagerotate($original, $degrees, 0);

    // Save.
    if($save){
        // Create the new file name.
    $newNameE = explode(".", $img);
    $newName = $newNameE[0] . $suffix .".". $newNameE[1];
    
    // Save the image.
    switch ($imagetype){
      case "jpg":   
        imagejpeg($rotate, "$imgPath/$newName", $quality) or die("Cant save image");
        break;
      case "png":   
        imagepng($rotate, "$imgPath/$newName", 9) or die("Cant save image");
        break;
      case "gif": 
        imagegif($rotate, "$imgPath/$newName", $quality) or die("Cant save image");
        break;
     }
    }

    // Clean up.
    //$a = array($im => $img);
    imagedestroy($original);
    imagedestroy($tempImg);
    //echo json_encode($a);
    return true;
    
}

function getlastimage($newkey){
    $arr = $_SESSION['img'];
    $oldkey = array_pop(array_keys($arr));
    $arr[$newkey] = $arr[$oldkey];
    unset($arr[$oldkey]);
    $results = end($arr);
    $_SESSION['img'] = $arr;
    echo json_encode($results);
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

function runExternal( $cmd, &$code ) {
    $descriptorspec = array(
        0 => array("pipe", "r"), // stdin is a pipe that the child will read from
        1 => array("pipe", "w"), // stdout is a pipe that the child will write to
        2 => array("pipe", "w")  // stderr is a file to write to
    );
    
    $pipes= array();
    $process = @proc_open($cmd, $descriptorspec, $pipes);
    
    $output= "";
    
    if (!is_resource($process)) return false;
    
    #close child's input imidiately
    fclose($pipes[0]);
    
    stream_set_blocking($pipes[1],false);
    stream_set_blocking($pipes[2],false);
    
    $todo= array($pipes[1],$pipes[2]);
    
    while( true ) {
        $read= array();
        if( !feof($pipes[1]) ) $read[]= $pipes[1];
        if( !feof($pipes[2]) ) $read[]= $pipes[2];
        
        if (!$read) break;
        
        $ready= stream_select($read, $write=NULL, $ex= NULL, 2);
        
        if ($ready === false) {
            break; #should never happen - something died
        }
        
        foreach ($read as $r) {
            $s= fread($r,1024);
            $output.= $s;
        }
    }
    
    fclose($pipes[1]);
    fclose($pipes[2]);
    
    $code= proc_close($process);
    
    return $output;
}

function formspecialchars($var){
    $pattern = '/&(#)?[a-zA-Z0-9]{0,};/';
   
    if (is_array($var)) {    // If variable is an array
        $out = array();      // Set output as an array
        foreach ($var as $key => $v) {     
            $out[$key] = formspecialchars($v);         // Run formspecialchars on every element of the array and return the result. Also maintains the keys.
        }
    } else {
        $out = $var;
        while (preg_match($pattern,$out) > 0) {
            $out = htmlspecialchars_decode($out,ENT_QUOTES);      
        }                            
        $out = htmlspecialchars(stripslashes(trim($out)), ENT_QUOTES,'UTF-8',true);     // Trim the variable, strip all slashes, and encode it
       
    }
   
    return $out;
} 

function resample_picfile($src, $dst, $ext, $w, $h){
    // If distortion stretching is within the range below,
    // then let image be distorted.
    //$lowend = 0.8;
    //$highend = 1.25;
    
    if($ext == "jpg"){
        $src_img = imagecreatefromjpeg($src);
    }
    
    if($ext == "png"){
        $src_img = imagecreatefrompng($src);
    }
    
    if($ext == "gif"){
        $src_img = imagecreatefromgif($src);
    }
    
    if($src_img){
        $dst_img = ImageCreateTrueColor($w, $h);
        /* if you don't want aspect-preserved images
to have a black bkgnd, fill $dst_img with the color of your choice here.
        */

        if($dst_img) {
            $src_w = imageSX($src_img);
            $src_h = imageSY($src_img);

            $scaleX = (float)$w / $src_w;
            $scaleY = (float)$h / $src_h;
            $scale = min($scaleX, $scaleY);

            $dstW = $w;
            $dstH = $h;
            $dstX = $dstY = 0;

            $scaleR = $scaleX / $scaleY;
            if($scaleR < $lowend || $scaleR > $highend){
                $dstW = (int)($scale * $src_w + 0.5);
                $dstH = (int)($scale * $src_h + 0.5);

                // Keep pic centered in frame.
                $dstX = (int)(0.5 * ($w - $dstW));
                $dstY = (int)(0.5 * ($h - $dstH));
            }
           
            imagecopyresampled(
                $dst_img, $src_img, $dstX, $dstY, 0, 0,
                $dstW, $dstH, $src_w, $src_h);
            imagejpeg($dst_img, $dst);
            imagedestroy($dst_img);
        }
        imagedestroy($src_img);
        return file_exists($dst);
    }
    return false;
}

?>
