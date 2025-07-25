<?php
class Desafio {
    private $conn;
    private $table_name = "desafios";

    public $id;
    public $empresa_id;
    public $mentor;
    public $whatsapp;
    public $titulo;
    public $descricao_problema;
    public $pesquisado;
    public $descricao_pesquisa;
    public $nivel_trl;
    public $requisitos_especificos;
    public $descricao_requisitos;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (empresa_id, mentor, whatsapp, titulo, descricao_problema, 
                   pesquisado, descricao_pesquisa, nivel_trl, requisitos_especificos, descricao_requisitos, status) 
                  VALUES (:empresa_id, :mentor, :whatsapp, :titulo, :descricao_problema,
                          :pesquisado, :descricao_pesquisa, :nivel_trl, :requisitos_especificos, :descricao_requisitos, :status)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":empresa_id", $this->empresa_id);
        $stmt->bindParam(":mentor", $this->mentor);
        $stmt->bindParam(":whatsapp", $this->whatsapp);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descricao_problema", $this->descricao_problema);
        $stmt->bindParam(":pesquisado", $this->pesquisado);
        $stmt->bindParam(":descricao_pesquisa", $this->descricao_pesquisa);
        $stmt->bindParam(":nivel_trl", $this->nivel_trl);
        $stmt->bindParam(":requisitos_especificos", $this->requisitos_especificos);
        $stmt->bindParam(":descricao_requisitos", $this->descricao_requisitos);
        $stmt->bindParam(":status", $this->status);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function read() {
        $query = "SELECT d.*, e.nome_fantasia as empresa_nome 
                  FROM " . $this->table_name . " d
                  LEFT JOIN empresas e ON d.empresa_id = e.id
                  ORDER BY d.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByEmpresa($empresa_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE empresa_id = :empresa_id 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":empresa_id", $empresa_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT d.*, e.nome_fantasia as empresa_nome, e.razao_social
                  FROM " . $this->table_name . " d
                  LEFT JOIN empresas e ON d.empresa_id = e.id
                  WHERE d.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET empresa_id = :empresa_id, mentor = :mentor, whatsapp = :whatsapp,
                      titulo = :titulo, descricao_problema = :descricao_problema,
                      pesquisado = :pesquisado, descricao_pesquisa = :descricao_pesquisa,
                      nivel_trl = :nivel_trl, requisitos_especificos = :requisitos_especificos,
                      descricao_requisitos = :descricao_requisitos, status = :status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":empresa_id", $this->empresa_id);
        $stmt->bindParam(":mentor", $this->mentor);
        $stmt->bindParam(":whatsapp", $this->whatsapp);
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descricao_problema", $this->descricao_problema);
        $stmt->bindParam(":pesquisado", $this->pesquisado);
        $stmt->bindParam(":descricao_pesquisa", $this->descricao_pesquisa);
        $stmt->bindParam(":nivel_trl", $this->nivel_trl);
        $stmt->bindParam(":requisitos_especificos", $this->requisitos_especificos);
        $stmt->bindParam(":descricao_requisitos", $this->descricao_requisitos);
        $stmt->bindParam(":status", $this->status);

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

    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = :status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function search($termo) {
        $query = "SELECT d.*, e.nome_fantasia as empresa_nome 
                  FROM " . $this->table_name . " d
                  LEFT JOIN empresas e ON d.empresa_id = e.id
                  WHERE d.titulo LIKE :termo 
                  OR d.descricao_problema LIKE :termo 
                  OR e.nome_fantasia LIKE :termo
                  OR e.cidade LIKE :termo
                  ORDER BY d.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $termo = "%" . $termo . "%";
        $stmt->bindParam(":termo", $termo);
        $stmt->execute();
        return $stmt;
    }

    public function readWithFilters($empresa_id = '', $cidade = '', $titulo = '', $nome_empresa = '', $nivel_trl = '') {
        $query = "SELECT d.*, e.nome_fantasia as empresa_nome, e.cidade 
                  FROM " . $this->table_name . " d
                  LEFT JOIN empresas e ON d.empresa_id = e.id
                  WHERE 1=1";
        
        $params = array();
        
        if (!empty($empresa_id)) {
            $query .= " AND d.empresa_id = :empresa_id";
            $params[':empresa_id'] = $empresa_id;
        }
        
        if (!empty($cidade)) {
            $query .= " AND e.cidade LIKE :cidade";
            $params[':cidade'] = "%" . $cidade . "%";
        }
        
        if (!empty($titulo)) {
            $query .= " AND d.titulo LIKE :titulo";
            $params[':titulo'] = "%" . $titulo . "%";
        }
        
        if (!empty($nome_empresa)) {
            $query .= " AND (e.nome_fantasia LIKE :nome_empresa OR e.razao_social LIKE :nome_empresa)";
            $params[':nome_empresa'] = "%" . $nome_empresa . "%";
        }
        
        if (!empty($nivel_trl)) {
            $query .= " AND d.nivel_trl = :nivel_trl";
            $params[':nivel_trl'] = $nivel_trl;
        }
        
        $query .= " ORDER BY d.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt;
    }
}
?> 