<?php

/**
 * Export video to YouTube - mp32youtube.com
 * author: robert d. dan
 * contact: robertddan@yahoo.com
 */

session_start();

require('../inc/database.php');
require_once('Zend/Loader.php'); // the Zend dir must be in your include_path
//require_once('Zend/Gdata/YouTube.php');
Zend_Loader::loadClass('Zend_Gdata_YouTube');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');


$_SESSION['developerKey'] = '`12345326457985689124534674679578';

if (!isset($_GET['op'])) {
    // if a GET variable is set then process the token upgrade
    if (isset($_GET['token'])) {
        updateAuthSubToken($_GET['token']);
    } else {
        header('Location: ../index.php');
    }
}
if (isset($_GET['op'])) {
    $operation = $_GET['op'];
    //$imgid          = formspecialchars($_POST['id']);
}
//$operation = $_GET['op'];
//$imgid     = $_REQUEST['id'];

if($operation){
    switch ($operation){
        case "genasrl":
            generateAuthSubRequestLink();
          break;
        case "viup":
            $_SESSION['video']['up']  = 'none';
            //directvVideoUp($_SESSION['video']['name'], $_SESSION['video']['uppath'], "video/x-flv");
            //echo $_SESSION['video']['uppath'].$_SESSION['video']['name'];
            baga_data_in_mysql();
          break;
        default:
          header('Location: ../index.php');
    }
}
function baga_data_in_mysql(){
    
    //if(!isset($_SESSION['video']['title']) || $_SESSION['video']['title'] == ""){
        $_SESSION['video']['title'] = substr($_SESSION['mp3']['filename'], 0 , -4);
    //}
    
    $_SESSION['tube']['user'] = fetchUsername();
    $datatime = date("Y-m-d H:i:s");
   
    $upstatus = @directVideoUpload($_SESSION['video']['title'], 
                        "Music",
                        "mp32youtube",
                        $_SESSION['video']['desc'] . " - created at http://mp32youtube.com", 
                        "mp32youtube", 
                        $_SESSION['video']['name'], 
                        $_SESSION['video']['uppath'].$_SESSION['video']['name'], 
                        "video/x-flv");
                        
    $sql = "INSERT INTO `mp32yt_autovi`.`youtube` (
                                                `ID` ,
                                                `video_id` ,
                                                `username` ,
                                                `songname` ,
                                                `client_ip` ,
                                                `date`
                                                )
                                                VALUES (
                                                'null', 
                                                '" . $_SESSION['tube']['id'] . "', 
                                                '" . $_SESSION['tube']['user'] . "', 
                                                '" . $_SESSION['video']['title'] . "', 
                                                '" . $_SERVER['REMOTE_ADDR'] . "', 
                                                '" . $datatime . "'
                                                )";
                                                
    if($_SESSION['mp3']['filename'] != "funguz.mp3"){
        //$database = new MySQLDatabase();
        //$result = $database->query($sql);
    }                          
    //$database = new MySQLDatabase();
    //$result = $database->query($sql);
    
    echo json_encode($upstatus);  

}

function getYtService(){
    $applicationId = "YTAViewer";
    $clientId = $GLOBALS['ytaviewer_config']['client_id'];
    $devKey = $GLOBALS['ytaviewer_config']['dev_key'];
    $httpClient = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
    $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $devKey);
    $yt->setMajorProtocolVersion(2);
    return $yt;
}

function fetchUsername(){
    try {
      $yt = getYtService();
      $userProfileEntry = $yt->getUserProfile('default');
      $username = $userProfileEntry->getUsername()->text;
      $_SESSION['tube']['user'] = $username;
    } catch(Zend_Gdata_App_HttpException $e){
      $username = 'UNKNOWN';
    }
    
    return $username;
}
function generateAuthSubRequestLink($nextUrl = null){
    $scope = 'http://gdata.youtube.com';
    $secure = false;
    $session = true;

    //if (!$nextUrl) {
    //    generateUrlInformation();
    //    $nextUrl = $_SESSION['operationsUrl'];
    //}
    $nextUrl = "http://". $_SERVER['SERVER_NAME']. "/youtube/";

    $url = Zend_Gdata_AuthSub::getAuthSubTokenUri($nextUrl, $scope, $secure, $session);
    echo json_encode($url); 
}

function generateUrlInformation(){
    if (!isset($_SESSION['operationsUrl']) || !isset($_SESSION['homeUrl'])) {
        $_SESSION['operationsUrl'] = 'http://'. $_SERVER['HTTP_HOST']
                                   . $_SERVER['PHP_SELF'];
        $path = explode('/', $_SERVER['PHP_SELF']);
        $path[count($path)-1] = 'index.php';
        $_SESSION['homeUrl'] = 'http://'. $_SERVER['HTTP_HOST']
                             . implode('/', $path);
    }
}
/**
 * Upgrade the single-use token to a session token.
 *
 * @param string $singleUseToken A valid single use token that is upgradable to a session token.
 * @return void
 */
function updateAuthSubToken($singleUseToken){
    try {
        $sessionToken = Zend_Gdata_AuthSub::getAuthSubSessionToken($singleUseToken);
    } catch (Zend_Gdata_App_Exception $e) {
        print 'ERROR - Token upgrade for ' . $singleUseToken
            . ' failed : ' . $e->getMessage();
        return;
    }

    $_SESSION['sessionToken'] = $sessionToken;
    $_SESSION['video']['up']  = "ok";

    header('Location: ../export.php'); 
    
}


/**
* Upload a video directly. Prints form HTML to page.
*
* @param string videoTitle The title for new video
* @param string videoCategory The catecory for the video
* @param string videoTags white space separated string of tags
* @param string videoDescription The Description for the video
* @param string videoDeveloperTags white space separated string of developer tags
* @param string videoFileName The fileName for the video
* @param string videoFilePath The fileName path for the video
* @param string videoMimeType the MIME type for the video
* @return unknown_type
*/
function directVideoUpload($videoTitle, $videoCategory, $videoTags, $videoDescription, $videoDeveloperTag, $videoFileName, $videoFilePath, $videoMimeType) {
   //NB: Code taken from http://code.google.com/intl/it-IT/apis/youtube/2.0/developers_guide_php.html#Direct_Upload
   $httpClient = getAuthSubHttpClient();

   // timeout modified to avoit timed out request.
   // hint taken from: http://code.google.com/intl/it-IT/apis/gdata/articles/php_client_lib.html#appendix_hints
   $config = array('timeout' => 360);
   $httpClient->setConfig($config);
   
   $httpClient->setAuthSubToken($_SESSION['sessionToken']);

   // Note that this example creates an unversioned service object.
   // You do not need to specify a version number to upload content
   // since the upload behavior is the same for all API versions.
   $yt = new Zend_Gdata_YouTube($httpClient);
   
   //added even if not necessary
   $yt->setMajorProtocolVersion(2);
   
   // create a new VideoEntry object
   $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

   // create a new Zend_Gdata_App_MediaFileSource object
   $filesource = $yt->newMediaFileSource($videoFilePath);

   $filesource->setContentType($videoMimeType);
   // set slug header
   $filesource->setSlug($videoFilePath);

   // add the filesource to the video entry
   $myVideoEntry->setMediaSource($filesource);

   $myVideoEntry->setVideoTitle($videoTitle);
   
   $myVideoEntry->setVideoDescription($videoDescription);

   // The category must be a valid YouTube category!
   //make sure first character in category is capitalized
   $videoCategory = strtoupper(substr($videoCategory, 0, 1))
   . substr($videoCategory, 1);
   $myVideoEntry->setVideoCategory($videoCategory);

   // Set keywords. Please note that this must be a comma-separated string
   // and that individual keywords cannot contain whitespace
   // convert videoTags from whitespace separated into comma separated
   $videoTagsArray = explode(' ', trim($videoTags));
   $myVideoEntry->SetVideoTags(implode(', ', $videoTagsArray));

   // set some developer tags -- this is optional
   // (see Searching by Developer Tags for more details)
   $myVideoEntry->setVideoDeveloperTags(explode(' ',$videoDeveloperTag));

   // upload URI for the currently authenticated user
   $uploadUrl = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';

   // try to upload the video, catching a Zend_Gdata_App_HttpException,
   // if available, or just a regular Zend_Gdata_App_Exception otherwise
   $_SESSION['video']['up'] = "none";
   
   try {

      $newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');

        $newEntry->setMajorProtocolVersion(2);
        $videoId = $newEntry->getVideoId(); 
        $checkup = checkUpload($videoId);
        
        if($videoId != ''){
            $_SESSION['tube']['id'] = $videoId;
            return array($videoId, $checkup);
        } else {
            return array("finish", $checkup);
            //echo'No further status information available yet.<br /><br />';
        }
        
    } catch (Zend_Gdata_App_HttpException $httpException) {
        return array("error", $httpException->getMessage());
        //echo json_encode('<h3>FINISH</h3><br />ERROR ' . );
        return;
    } catch (Zend_Gdata_App_Exception $e) {
        return array("error",  $e->getMessage());
        //echo json_encode('<h3>FINISH</h3><br />ERROR - ' . $e->getMessage());
        return;
    }   
    return; 
}

/**
 * Check the upload status of a video
 *
 * @param string $videoId The video to check.
 * @return string A message about the video's status.
 */
function checkUpload($videoId)
{
    $httpClient = getAuthSubHttpClient();
    $youTubeService = new Zend_Gdata_YouTube($httpClient);

    $feed = $youTubeService->getuserUploads('default');
    $message = 'No further status information available yet.';

    foreach($feed as $videoEntry) {
        if ($videoEntry->getVideoId() == $videoId) {
            // check if video is in draft status
            try {
                $control = $videoEntry->getControl();
            } catch (Zend_Gdata_App_Exception $e) {
                print 'ERROR - not able to retrieve control element '
                    . $e->getMessage();
                return;
            }

            if ($control instanceof Zend_Gdata_App_Extension_Control) {
                if (($control->getDraft() != null) &&
                    ($control->getDraft()->getText() == 'yes')) {
                    $state = $videoEntry->getVideoState();
                    if ($state instanceof Zend_Gdata_YouTube_Extension_State) {
                        $message = $state->getName() . $state->getText();
                    } else {
                        return $message;
                    }
                }
            }
        }
    }
    return $message;
}

/**
 * Convenience method to obtain an authenticted Zend_Http_Client object.
 *
 * @return Zend_Http_Client An authenticated client.
 */
function getAuthSubHttpClient()
{
    try {
        $httpClient = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
    } catch (Zend_Gdata_App_Exception $e) {
        print 'ERROR - Could not obtain authenticated Http client object. '
            . $e->getMessage();
        return;
    }
    $httpClient->setHeaders('X-GData-Key', 'key='. $_SESSION['developerKey']);
    return $httpClient;
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
?>