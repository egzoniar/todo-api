<?php 

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Todo.php';
include_once '../../models/User.php';

$database = new Database();
$db = $database->connect();

$todo = new Todo($db);
$user = new User($db);


if(!isset($_GET['user_id'])) {
  http_response_code(400);
  echo json_encode(array('message' => 'Bad Request'));
  return;
}

$userID = $_GET['user_id'];
$user->id = $userID;

if(!$user->exists()) {
  http_response_code(404);
  echo json_encode(array('message' => 'User does not exist'));
  return;
}

if(!isset($_GET['keyword'])) {
  http_response_code(400);
  echo json_encode(array('message' => 'Bad Request'));
  return;
}

$todo->user_id = $userID;
$result = $todo->find($_GET['keyword']);
$num = $result->rowCount();

// No data
if($num <= 0) {
  http_response_code(404);
  echo json_encode(array('message' => 'No todos found'));
  return;
}

$todosArr = array();
$todosArr['data'] = array();

while($row = $result->fetch(PDO::FETCH_ASSOC)) {
  extract($row);

  $todoItem = array(
    'id' => $id,
    'title' => $title,
    'checked' => $checked,
    'createdAt' => $created_at
  );

  array_push($todosArr['data'], $todoItem);
}

echo json_encode($todosArr);