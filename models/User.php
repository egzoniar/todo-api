<?php 

class User {
  private $conn;
  private $table = 'users';

  public $id;
  public $username;
  public $password;

  public function __construct($db) {
    $this->conn = $db;
  }
  
  public function login() {
    $query = "SELECT * FROM " . $this->table . " WHERE username = :username AND password = :password";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':username', $this->username);
    $stmt->bindParam(':password', $this->password);
    $stmt->execute();
    $num = $stmt->rowCount();

    if($num <= 0) {
      return false;
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $this->id = $row['id'];

    return true;
  }

  public function exists() {
    $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $this->id);
    $stmt->execute();
    $num = $stmt->rowCount();

    if($num <= 0) return false;
    return true;
  }
}
