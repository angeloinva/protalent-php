<?php
class Professor {
    private $conn;
    private $table_name = "professores";

    public $id;
    public $nome;
    public $email;
    public $whatsapp;
    public $instituicao;
    public $area_atuacao;
    public $interessado_edicoes_futuras;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, email, whatsapp, instituicao, area_atuacao, interessado_edicoes_futuras) 
                  VALUES (:nome, :email, :whatsapp, :instituicao, :area_atuacao, :interessado_edicoes_futuras)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":whatsapp", $this->whatsapp);
        $stmt->bindParam(":instituicao", $this->instituicao);
        $stmt->bindParam(":area_atuacao", $this->area_atuacao);
        $stmt->bindParam(":interessado_edicoes_futuras", $this->interessado_edicoes_futuras);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, email = :email, whatsapp = :whatsapp, instituicao = :instituicao, 
                      area_atuacao = :area_atuacao, interessado_edicoes_futuras = :interessado_edicoes_futuras
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":whatsapp", $this->whatsapp);
        $stmt->bindParam(":instituicao", $this->instituicao);
        $stmt->bindParam(":area_atuacao", $this->area_atuacao);
        $stmt->bindParam(":interessado_edicoes_futuras", $this->interessado_edicoes_futuras);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countInteressados() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE interessado_edicoes_futuras = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function updateInteresseEdicoesFuturas($email, $interesse) {
        $query = "UPDATE " . $this->table_name . " 
                  SET interessado_edicoes_futuras = :interesse 
                  WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":interesse", $interesse);
        return $stmt->execute();
    }
}
?> 