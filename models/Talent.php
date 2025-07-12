<?php
require_once 'config/database.php';

class Talent {
    private $conn;
    private $table_name = "talents";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $skills;
    public $experience_years;
    public $salary_expectation;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar talento
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, email, phone, skills, experience_years, salary_expectation, status) 
                  VALUES (:name, :email, :phone, :skills, :experience_years, :salary_expectation, :status)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->skills = htmlspecialchars(strip_tags($this->skills));
        $this->experience_years = htmlspecialchars(strip_tags($this->experience_years));
        $this->salary_expectation = htmlspecialchars(strip_tags($this->salary_expectation));
        $this->status = htmlspecialchars(strip_tags($this->status));

        // Bind dos parâmetros
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":skills", $this->skills);
        $stmt->bindParam(":experience_years", $this->experience_years);
        $stmt->bindParam(":salary_expectation", $this->salary_expectation);
        $stmt->bindParam(":status", $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Ler todos os talentos
    public function read() {
        $query = "SELECT id, name, email, phone, skills, experience_years, salary_expectation, status, created_at 
                  FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Ler um talento específico
    public function readOne() {
        $query = "SELECT id, name, email, phone, skills, experience_years, salary_expectation, status, created_at 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->skills = $row['skills'];
            $this->experience_years = $row['experience_years'];
            $this->salary_expectation = $row['salary_expectation'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    // Atualizar talento
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, email = :email, phone = :phone, skills = :skills, 
                      experience_years = :experience_years, salary_expectation = :salary_expectation, 
                      status = :status 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->skills = htmlspecialchars(strip_tags($this->skills));
        $this->experience_years = htmlspecialchars(strip_tags($this->experience_years));
        $this->salary_expectation = htmlspecialchars(strip_tags($this->salary_expectation));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind dos parâmetros
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':skills', $this->skills);
        $stmt->bindParam(':experience_years', $this->experience_years);
        $stmt->bindParam(':salary_expectation', $this->salary_expectation);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Deletar talento
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

    // Buscar talentos por habilidades
    public function searchBySkills($skills) {
        $query = "SELECT id, name, email, phone, skills, experience_years, salary_expectation, status 
                  FROM " . $this->table_name . " 
                  WHERE skills LIKE :skills AND status = 'available' 
                  ORDER BY experience_years DESC";
        $stmt = $this->conn->prepare($query);
        $skills = "%" . $skills . "%";
        $stmt->bindParam(":skills", $skills);
        $stmt->execute();
        return $stmt;
    }

    // Contar talentos por status
    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $status);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?> 