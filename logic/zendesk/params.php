<?php
require_once(__DIR__ . '/../functions.php');
global $ZD_API_URL;
global $ZD_API_EMAIL;
global $ZD_API_TOKEN;
$ZD_API_URL='https://188bet.zendesk.com/api/v2/';
$ZD_API_EMAIL='suporte@188app.com.br';
$ZD_API_TOKEN='B19QVUrPtgbtPyvlkTGK2CMXZGaTBSK5MDbpW7Tg';

function zd_api_call($url){
  global $ZD_API_URL;
  global $ZD_API_EMAIL;
  global $ZD_API_TOKEN;
  $options=array(
    CURLOPT_URL=>$url,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_HEADER => false,
    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
    CURLOPT_USERPWD => $ZD_API_EMAIL . '/token:' . $ZD_API_TOKEN,
    CURLOPT_ENCODING => '', //Accept all encoding
  );
  
  $result=special_request($options,$response);
  if(!empty($result)){
    return $result;
  } else {
    return null;
  }
}

function zd_api_json($url,$json){
  global $ZD_API_URL;
  global $ZD_API_EMAIL;
  global $ZD_API_TOKEN;
  $options=array(
    CURLOPT_URL=>$url,
    CURLOPT_FOLLOWLOCATION => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_HEADER => false,
    CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
    CURLOPT_USERPWD => $ZD_API_EMAIL . '/token:' . $ZD_API_TOKEN,
    CURLOPT_ENCODING => '', //Accept all encoding
    CURLOPT_POST=>1,
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
    CURLOPT_POSTFIELDS=>$json
  );
  
  $result=special_request($options,$response);
  if(!empty($result)){
    return $result;
  } else {
    return null;
  }
}


?>