<?php
function get_client_ip(){
  if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
    $list=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
    foreach($list as $item){
      if(!empty($item)){
        return $item;
      }
    }
  }
  if(isset($_SERVER['REMOTE_ADDR'])){
    return $_SERVER['REMOTE_ADDR'];
  } else if(isset($_SERVER['HTTP_CLIENT_IP'])){
    return $_SERVER['HTTP_CLIENT_IP'];
  } else {
    return '';
  }
}

/************************/
/*** STRING FUNCTIONS ***/
/************************/
global $NORMALIZE_CHARS;
$NORMALIZE_CHARS = array(
  'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
  'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
  'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
  'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
  'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
  'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
  'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
  'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
);
function normalize_special_chars(&$text){
  global $NORMALIZE_CHARS;
  return strtr($text,$NORMALIZE_CHARS);
}

/**********************/
/*** CURL FUNCTIONS ***/
/**********************/
function special_request(&$options,&$response_code){
  /**
   * Sends a request to the URL given in the first
   * parameter, using the options given in the second
   * parameter. Returns the response and writes the
   * response code in the third parameter.
   */
  if(!isset($options[CURLOPT_TIMEOUT])){
    $options[CURLOPT_TIMEOUT]=15;
  }
  $curl=curl_init();
  curl_setopt_array($curl,$options);
  $results = curl_exec($curl);
  $response_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
  $info=curl_getinfo($curl);
  curl_close($curl);
  return $results;
}
function plain_request($url,$post_data,&$response_code){
  /**
   * Sends a quest to the URL given in the first
   * parameter using a set of predefined curl options.
   * If the second parameter is not empty, uses the string
   * as the CURLOPT_POSTFIELDS value.
   * Returns the response and writes the response code
   * in the third parameter.
   */
  $curl_user_agent = $_SERVER['HTTP_USER_AGENT'];
    //'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:51.0) Gecko/20100101 Firefox/51.0';
  $ip=get_client_ip();
  $curl_headers = array(
    'Accept: application/json',
    //
    'X-Forwarded-For: ' . $ip,
    //'User-Agent: ' . $curl_user_agent,
    'Forwarded: for=' . $ip . ';proto=' . ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] )?'https':'http' )
  );
  $options = array(
    CURLOPT_URL => $url,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_HEADER => false,
    CURLOPT_USERAGENT => $curl_user_agent,
    CURLOPT_HTTPHEADER => $curl_headers,
    CURLOPT_ENCODING => '', //Accept all encoding
  );
  if(!empty($post_data)){
    $options[CURLOPT_POST]=1;
    $options[CURLOPT_POSTFIELDS]=$post_data;
  }
  return special_request($options,$response_code);
}
function json_request($url,$json,$access_token,&$response_code){
  /**
   * Sends a quest to the URL given in the first
   * parameter using a set of predefined curl options.
   * If the second parameter is not empty, uses the string
   * as the CURLOPT_POSTFIELDS value.
   * Returns the response and writes the response code
   * in the third parameter.
   */
  $curl_user_agent = $_SERVER['HTTP_USER_AGENT'];
    //'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:51.0) Gecko/20100101 Firefox/51.0';
  $ip=get_client_ip();
  $curl_headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    //'Content-Length: ' . strlen($json),
    //
    'X-Forwarded-For: ' . $ip,
    //'User-Agent: ' . $curl_user_agent,
    'Forwarded: for=' . $ip . ';proto=' . ( $_SERVER['HTTPS']?'https':'http' )
  );
  if(isset($access_token)){
    $curl_headers[]='Authorization: Bearer ' . $access_token;
  }
  $options = array(
    CURLOPT_URL => $url,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_HEADER => false,
    CURLOPT_USERAGENT => $curl_user_agent,
    CURLOPT_HTTPHEADER => $curl_headers,
    //CURLOPT_ENCODING => ''
  );
  if(!empty($json)){
    $options[CURLOPT_POST]=1;
    $options[CURLOPT_POSTFIELDS]=$json;
  }
  return special_request($options,$response_code);
}

/**************************/
/** WEBSITE CALCULATIONS **/
/**************************/
function get_level_image($level){
  $level_image='/sites/default/files/images/extern/user-lv' . $level . '.png';
  if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $level_image)){
    $level_image=null;
  }
  return $level_image;
}

/***********************/
/*** CACHE FUNCTIONS ***/
/***********************/
global $CACHE_FILE_NAME;
$CACHE_FILE_NAME=$_SERVER['DOCUMENT_ROOT'] . '/logic/cache/';
function read_cache_file($local_filename){
  global $CACHE_FILE_NAME;
  $file=fopen($CACHE_FILE_NAME . $local_filename,'rb');
  $str=fgets($file);
  if($str<time()){
    fclose($file);
    return null;
  }
  $position=ftell($file);
  fseek($file,0,SEEK_END);
  $size=ftell($file);
  fseek($file,$position,SEEK_SET);
  $str2=fread($file,$size-$position);
  return $str2;
}
function write_cache_file($local_filename,$string,$life=3600){
  global $CACHE_FILE_NAME;
  $date=time()+$life;
  file_put_contents($CACHE_FILE_NAME . $local_filename,$date . "\n" . $string);
}
function get_states(){
  return get_endpoint_cache('/Address/States','states.dat');
}
function get_teams(){
  return get_endpoint_cache('/Team','teams.dat');
}
function get_monthly_prizes(){
  return get_endpoint_cache('/Awards/Month','mprizes.dat',true);
}
function get_weekly_prizes(){
  return get_endpoint_cache('/Awards/Week','wprizes.dat',true);
}
function get_endpoint_cache($end_point,$cache_file,$login_required){
  //Returns data from an endpoint and writes it into the cache
  //If a cache already exists, the data from the cache is returned.
  $data=null;
  $cache_data=read_cache_file($cache_file);
  if(isset($cache_data)){
    return json_decode($cache_data,true);
  }
  ob_start();
  $ENDPOINT=$end_point;
  if($login_required){
    include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  } else {
    include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-plain.php');
  }
  $json=ob_get_contents();
  ob_end_clean();
  $info=json_decode($json,true);
  if($info['erro']===false){
    $data=$info['data'];
    write_cache_file($cache_file,json_encode($data));
  } else {
    $data=null;
  }
  return $data;

}

function get_user_info(){
  //Returns a cache if the user info was loaded in the last hour.
  //Reads from $_SESSION['bet188']['user'] to verify that the user was logged in.
  //A session must be started for this function to work.
  
  //Check cache
  
  if(isset($_SESSION['bet188']['cache']['user_info'])
  && isset($_SESSION['bet188']['user']['username'])){
    $time=$_SESSION['bet188']['cache']['user_info']['time'];
    if($time-3600<time()){
      $user_data=$_SESSION['bet188']['cache']['user_info']['value'];
      //Return the data only if the user name is the same
      if(isset($user_data['data']['username'])
      && $user_data['data']['username']===$_SESSION['bet188']['user']['username']){
        return $user_data;
      }
    }
  }
  
  //Load new user data
  ob_start();
  $ENDPOINT='/User/GetInfo';
  include($_SERVER['DOCUMENT_ROOT'] . '/logic/api/api-user.php');
  $user_json=ob_get_contents();
  ob_end_clean();
  $user_data=json_decode($user_json,true);
  if($user_data['erro']===false){
    if(!isset($_SESSION['bet188'])){
      $_SESSION['bet188']=array();
    }
    if(!isset($_SESSION['bet188']['cache'])){
      $_SESSION['bet188']['cache']=array();
    }
    $_SESSION['bet188']['cache']['user_info']=array(
      'time'=>time(),
      'value'=>$user_data
    );
    $_SESSION['bet188']['user']['username']=$user_data['data']['username'];
  }
  
  return $user_data;
}


?>