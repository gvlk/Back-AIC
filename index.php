<?php
require_once('database/database.php');

$requestBody = file_get_contents('php://input');
$requestBody = json_decode($requestBody, true) 
              or die("Could not decode JSON");

if (array_key_exists('login', $_GET)) {
  require_once('controller/loginController.php');

} else if(array_key_exists('cadastro', $_GET)) {
  echo 'cadastro';

} else if(array_key_exists('refreshToken', $_GET)) {
  echo 'Renovar token';
  
} else if(array_key_exists('deletarToken', $_GET)) {
  echo 'Deletar token';

} else {
  echo '404';
}