<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $nome;
    public $email;
    public $password;
    public $tipo;
    public $criado_em;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar usuário
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
          (nome, email, password, tipo) 
          VALUES (:nome, :email, :password, :tipo)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));

        // Bind dos parâmetros
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":tipo", $this->tipo);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Ler todos os usuários
    public function read() {
        $query = "SELECT id, nome, email, tipo, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Ler um usuário específico
    public function readOne() {
        $query = "SELECT id, nome, email, tipo, created_at FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->tipo = $row['tipo'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Atualizar usuário
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, email = :email, tipo = :tipo 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind dos parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Deletar usuário
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Autenticar usuário
    public function authenticate($email, $password) {
        $query = "SELECT id, nome, email, password, tipo FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row && password_verify($password, $row['password'])) {
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->email = $row['email'];
            $this->tipo = $row['tipo'];
            return true;
        }
        return false;
    }
}
?> 