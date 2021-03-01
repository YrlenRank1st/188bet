<?php
/**
 * Uploads the local image file $_FILES['image-upload']
 * The user access token is taken from $_SESSION['bet188']['user']
 * The image is assumed to be secure.
 */

require_once(__DIR__ . '/../functions.php');
require('params.php');

if(isset($_SESSION['bet188']['user'])){
  $access_token=$_SESSION['bet188']['user']['access_token'];
} else {
  $access_token=null;
}


/** DETECT FILE FORMAT **/
$is_image=0;
if(!empty($_FILES['image-upload']['tmp_name'])){
  $GIF_BYTES=array(0x47,0x49,0x46);
  $PNG_BYTES=array(0x89,0x50,0x4e,0x47);
  $JPG_BYTES=array(0xFF,0xD8,0xFF);

  $temp_file=fopen($_FILES['image-upload']['tmp_name'],'rb');
  $first_bytes=fread($temp_file,6);
  fclose($temp_file);

  $is_image+=string_contains_bytes($first_bytes,$GIF_BYTES);
  $is_image+=string_contains_bytes($first_bytes,$PNG_BYTES);
  $is_image+=string_contains_bytes($first_bytes,$JPG_BYTES);
  if(!$is_image){
    error_log('*NOT IMAGE* ' . $_FILES['image-upload']['name'] . ' (' . ord($first_bytes[0]) . ',' . ord($first_bytes[1]) . ',' . ord($first_bytes[2]) . ',' . ord($first_bytes[3]) . ')');
  }
}
function string_contains_bytes($str,&$bytes){
  /**
   * Returns 1 if the string contains the bytes.
   * Returns 0 if the string does not contain the bytes.
   */
  $len=count($bytes);
  if(strlen($str) < $len){ return 0; }
  for($i=0;$i<$len;$i++){
    if(ord($str[$i])!=$bytes[$i]){ return 0; }
  }
  return 1;
}

/** END DETECT FILE FORMAT **/

if(empty($_FILES['image-upload']) || !($_FILES['image-upload']['error']===UPLOAD_ERR_OK) ){
?>
{"status":<?php isset($_FILES['image-upload']['error'])?$_FILES['image-upload']['error']:'-1'; ?>,"data":null,"erro":true,"errors":null,"message":"no_image_file","messageEx":null}
<?php
} else if(empty($access_token)){//User is not logged in
?>
{"status":0,"data":null,"erro":true,"errors":null,"message":"not_logged_in","messageEx":null}
<?php
} else if(!$is_image){
?>
{"status":0,"data":null,"erro":true,"errors":null,"message":"not_image","messageEx":null}
<?php
} else {


  //Image data
  $file_data=$_FILES['image-upload'];
  $filename=$file_data['name'];
  $filepath=$file_data['tmp_name'];
  $filesize=$file_data['size'];
  $filetype=$file_data['type'];
  $boundary=md5(mt_rand() . microtime());
  $delimiter='---------' . $boundary;

  $photo_data='';
  $photo_data .= '--' . $delimiter . "\r\n";
  $photo_data .= 'Content-Disposition: form-data; '
    . 'name="file"; '
    . 'filename="' . $filename . '"' . "\r\n"
    . 'Content-Transfer-Encoding: binary' . "\r\n";
  $photo_data .= 'Content-type: ' . $filetype . "\r\n";
  $photo_data .= "\r\n";
  $photo_data .= file_get_contents($filepath) . "\r\n";
  
  $photo_data .= '--' . $delimiter . '--' . "\r\n";
  
  $response_code=0;
  $curl_user_agent = $_SERVER['HTTP_USER_AGENT'];
    //'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:51.0) Gecko/20100101 Firefox/51.0';
  $ip=get_client_ip();
  $curl_headers = array(
    'Accept: */*',
    'Content-Type: multipart/form-data; boundary=' . $delimiter,
    'Content-Length: ' . strlen($photo_data),
    
    //
    'X-Forwarded-For: ' . $ip,
    'User-Agent: ' . $curl_user_agent,
    'Forwarded: for=' . $ip . ';proto=' . ( $_SERVER['HTTPS']?'https':'http' ),
    
    'Authorization: Bearer ' . $access_token
  );
  $options = array(
    CURLOPT_URL => $API_URL . '/File/Upload',
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_USERAGENT => $curl_user_agent,
    CURLOPT_HTTPHEADER => $curl_headers,
    CURLOPT_ENCODING => ''
  );
  
  $options[CURLOPT_POST]=1;
  $options[CURLOPT_POSTFIELDS]=$photo_data;
  $result=special_request($options,$response_code);
  if(empty($result)){
?>
{"status":<?php echo $response_code; ?>,"data": null,"erro":true,"errors":null,"message":"HTTP error","messageEx":null}
<?php
  } else {
    echo $result;
  }
}
?>