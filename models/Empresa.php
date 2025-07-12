<?php
class Empresa {
    private $conn;
    private $table_name = "empresas";

    public $id;
    public $cnpj;
    public $razao_social;
    public $nome_fantasia;
    public $nome_mentor;
    public $email;
    public $password;
    public $whatsapp;
    public $cep;
    public $endereco;
    public $numero;
    public $complemento;
    public $bairro;
    public $cidade;
    public $estado;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (cnpj, razao_social, nome_fantasia, nome_mentor, email, password, whatsapp, 
                   cep, endereco, numero, complemento, bairro, cidade, estado) 
                  VALUES (:cnpj, :razao_social, :nome_fantasia, :nome_mentor, :email, :password, :whatsapp,
                          :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":cnpj", $this->cnpj);
        $stmt->bindParam(":razao_social", $this->razao_social);
        $stmt->bindParam(":nome_fantasia", $this->nome_fantasia);
        $stmt->bindParam(":nome_mentor", $this->nome_mentor);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":whatsapp", $this->whatsapp);
        $stmt->bindParam(":cep", $this->cep);
        $stmt->bindParam(":endereco", $this->endereco);
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":complemento", $this->complemento);
        $stmt->bindParam(":bairro", $this->bairro);
        $stmt->bindParam(":cidade", $this->cidade);
        $stmt->bindParam(":estado", $this->estado);

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

    public function authenticate($email, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($empresa && password_verify($password, $empresa['password'])) {
            $this->id = $empresa['id'];
            $this->cnpj = $empresa['cnpj'];
            $this->razao_social = $empresa['razao_social'];
            $this->nome_fantasia = $empresa['nome_fantasia'];
            $this->nome_mentor = $empresa['nome_mentor'];
            $this->email = $empresa['email'];
            $this->whatsapp = $empresa['whatsapp'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET cnpj = :cnpj, razao_social = :razao_social, nome_fantasia = :nome_fantasia,
                      nome_mentor = :nome_mentor, email = :email, password = :password, whatsapp = :whatsapp,
                      cep = :cep, endereco = :endereco, numero = :numero, complemento = :complemento,
                      bairro = :bairro, cidade = :cidade, estado = :estado
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":cnpj", $this->cnpj);
        $stmt->bindParam(":razao_social", $this->razao_social);
        $stmt->bindParam(":nome_fantasia", $this->nome_fantasia);
        $stmt->bindParam(":nome_mentor", $this->nome_mentor);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":whatsapp", $this->whatsapp);
        $stmt->bindParam(":cep", $this->cep);
        $stmt->bindParam(":endereco", $this->endereco);
        $stmt->bindParam(":numero", $this->numero);
        $stmt->bindParam(":complemento", $this->complemento);
        $stmt->bindParam(":bairro", $this->bairro);
        $stmt->bindParam(":cidade", $this->cidade);
        $stmt->bindParam(":estado", $this->estado);

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
}
?> 