<?php
// Inclui o arquivo de conexão 'connect.php'
require_once __DIR__ . '/connect.php';

// Inicia a sessão
session_start();

// Cria a classe TaskManager
class TaskManager {
    private $db;

    public function __construct($database) {
        $this->db = $database->getConnection();
    }

    // Método para adicionar uma tarefa ao banco de dados
    public function addTask($name, $description, $image, $date) {
        $stmt = $this->db->prepare('INSERT INTO tasks (task_name, task_description, task_image, task_date)
                                VALUES (:name, :description, :image, :date)');

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':date', $date);

        return $stmt->execute();
    }

    // Método para remover uma tarefa do banco de dados
    public function removeTask($id) {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id');
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}

// Cria uma instância do TaskManager, passando a conexão do banco de dados como parâmetro
$taskManager = new TaskManager($database);

// Verifica se o formulário de adição de tarefa foi submetido
if (isset($_POST['task_name'])) {
    if ($_POST['task_name'] != "") {
        // Verifica se uma imagem foi enviada e a move para a pasta 'uploads'
        if (isset($_FILES['task_image'])) {
            $ext = strtolower(substr($_FILES['task_image']['name'], -4));
            $file_name = md5(date('Y.m.d.H.i.s')) . $ext;
            $dir = 'uploads/';
            move_uploaded_file($_FILES['task_image']['tmp_name'], $dir . $file_name);
        }

        $name = $_POST['task_name'];
        $description = $_POST['task_description'];
        $image = $file_name;
        $date = $_POST['task_date'];

        // Tenta adicionar a tarefa ao banco de dados
        if ($taskManager->addTask($name, $description, $image, $date)) {
            $_SESSION['success'] = "Dados cadastrados.";
            header('Location:index.php');
        } else {
            $_SESSION['error'] = "Dados não cadastrados.";
            header('Location:index.php');
        }
    } else {
        $_SESSION['message'] = "O campo nome da tarefa não pode ser vazio!";
        header('Location:index.php');
    }
}

// Verifica se uma solicitação GET com a chave 'key' foi recebida
if (isset($_GET['key'])) {
    $id = $_GET['key'];
    // Tenta remover a tarefa do banco de dados
    if ($taskManager->removeTask($id)) {
        $_SESSION['success'] = "Dados removidos.";
        header('Location:index.php');
    } else {
        $_SESSION['error'] = "Dados não removidos.";
        header('Location:index.php');
    }
}
?>
