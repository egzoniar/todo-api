<?php
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Accept");

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$user->username = $data->username;
$user->password = $data->password;

if(!$user->login()) {
  http_response_code(401);
  echo json_encode(array('message' => 'Wrong username or password'));
  return;
}

$secret_key = "thesecretkey";
$issuer_claim = "localhost"; // this can be the servername
$audience_claim = "THE_AUDIENCE";
$issuedat_claim = time(); // issued at
$notbefore_claim = $issuedat_claim + 10; //not before in seconds
$expire_claim = $issuedat_claim + (60*60); // expire time in seconds
$token = array(
  "iss" => $issuer_claim,
  "aud" => $audience_claim,
  "iat" => $issuedat_claim,
  "nbf" => $notbefore_claim,
  "exp" => $expire_claim,
  "data" => array(
    "id" => $user->id,
    "username" => $user->username
));

http_response_code(200);

$jwt = JWT::encode($token, $secret_key);
echo json_encode(
  array(
    "message" => "Successful login.",
    "jwt" => $jwt,
    "id" => $user->id,
    "username" => $user->username,
    "expireAt" => $expire_claim
    // "expireAt" => date("Y-m-d H:m:s", $expire_claim)
));