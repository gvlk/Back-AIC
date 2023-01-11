<?php
function response($msg, $http_status_code, $data)
{
  $array = array('msg' => $msg, 'status' => $http_status_code, 'data' => $data); 
  header('Content-Type:application/json');
  http_response_code($http_status_code);
  echo json_encode($array);
  exit();
}
