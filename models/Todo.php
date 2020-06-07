<?php

class Todo {
  private $conn;
  private $table = 'todos';

  public $id;
  public $title;
  public $checked;
  public $createdAt;
  public $user_id;

  public function __construct($db) {
    $this->conn = $db;
  }

  public function read() {
    $query = 'SELECT * FROM ' . $this->table . ' WHERE user_id = :id ORDER BY id DESC';

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $this->user_id);

    $stmt->execute();
    
    return $stmt;
  }

  public function create() {
    $checked = 0;
    $now = time();

    $query = 'INSERT INTO ' . $this->table . '
      SET
        title = :title,
        checked = :checked,
        created_at = :now,
        user_id = :user_id';

    $stmt = $this->conn->prepare($query);

    $this->title = htmlspecialchars(strip_tags($this->title));
    $this->checked = htmlspecialchars(strip_tags($this->checked));
    $this->user_id = htmlspecialchars(strip_tags($this->user_id));

    $stmt->bindParam(':title', $this->title);
    $stmt->bindParam(':checked', $checked);
    $stmt->bindParam(':now', $now);
    $stmt->bindParam(':user_id', $this->user_id);

    if($stmt->execute()) 
      return true;

    printf('Error: %s.\n' . $stmt->error);
    return false;
  }

  public function update() {
    $validateTitle = isNull($this->title) ? '' : 'title = :title,';
    $validateChecked = isNull($this->checked) ? '' : 'checked = :checked,';

    $query = 'UPDATE ' . $this->table . '
      SET
        '.$validateTitle.'
        '.$validateDesc.'
        '.$validateChecked.'
        user_id = :user_id
      WHERE
        id = :id';

    $stmt = $this->conn->prepare($query);

    if(!isNull($this->title)) $this->title = htmlspecialchars(strip_tags($this->title));
    if(!isNull($this->checked)) $this->checked = htmlspecialchars(strip_tags($this->checked));
    $this->user_id = htmlspecialchars(strip_tags($this->user_id));
    $this->id = htmlspecialchars(strip_tags($this->id));

    if(!isNull($this->title)) $stmt->bindParam(':title', $this->title);
    if(!isNull($this->checked)) $stmt->bindParam(':checked', $this->checked);
    $stmt->bindParam(':user_id', $this->user_id);
    $stmt->bindParam(':id', $this->id);

    if($stmt->execute()) 
      return true;

    printf('Error: %s.\n' . $stmt->error);
    return false;
  }

  public function delete() {
    $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

    $stmt = $this->conn->prepare($query);

    $this->title = htmlspecialchars(strip_tags($this->id));
    $stmt->bindParam(':id', $this->id);

    if($stmt->execute()) 
      return true;

    printf('Error: %s.\n' . $stmt->error);
    return false;
  }

  public function find($keyword) {
    $this->title = htmlspecialchars(strip_tags($keyword));
    $query = 'SELECT * FROM ' . $this->table 
    ." WHERE user_id = :user_id AND title LIKE '%" .$keyword. "%'";


    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':user_id', $this->user_id);

    $stmt->execute();

    return $stmt;
  }
}

function isNull($var) { 
  return ($var == null);
}