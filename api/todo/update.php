<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Authorization, Access-Control-Allow-Methods, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Todo.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$todo = new Todo($db);
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$user->id = $data->user_id;
if(!$user->exists()) {
  http_response_code(404);
  echo json_encode(array('message' => 'User does not exist'));
  return;
}

$todo->id = $data->id;
$todo->title = isset($data->title) ? $data->title : null;
$todo->checked = isset($data->checked) ? $data->checked : null;
$todo->user_id = $data->user_id;

if($todo->update())
  echo json_encode(array('message' => 'Todo Updated Successfully'));
else 
  echo json_encode(array('message' => 'Todo Not Updated'));