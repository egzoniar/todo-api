<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Authorization, Access-Control-Allow-Methods, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Todo.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$todo = new Todo($db);
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(!isset($data->user_id)) {
  http_response_code(400);
  echo json_encode(array('message' => 'Bad Request'));
  return;
}

$user->id = $data->user_id;
if(!$user->exists()) {
  http_response_code(404);
  echo json_encode(array('message' => 'User does not exist'));
  return;
}

if(!isset($data->id)) {
  http_response_code(400);
  echo json_encode(array('message' => 'Bad Request'));
  return;
}

$todo->id = $data->id;
if($todo->delete())
  echo json_encode(array('message' => 'Todo Deleted Successfully'));
else 
  echo json_encode(array('message' => 'Todo Not Deleted'));