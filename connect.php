<?php
// Cria a classe DatabaseConnection
class DatabaseConnection {
    private $conn;

    public function __construct() {
        // Inicia a conexão com o banco de dados MySQL
        $this->conn = new PDO("mysql:host=localhost;dbname=gerenciadortarefas", "root", "");
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Retorna a conexão com o banco de dados
    public function getConnection() {
        return $this->conn;
    }
}

// Cria uma instância da classe DatabaseConnection para estabelecer uma conexão com o banco de dados
$database = new DatabaseConnection();
?>
