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

    // Obtém todas as tarefas do banco de dados
    public function getTasks() {
        $stmt = $this->db->prepare("SELECT * FROM tasks");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Cria uma instância do TaskManager, passando a conexão do banco de dados como parâmetro
$taskManager = new TaskManager($database);

// Obtém as tarefas do banco de dados e as armazena em $stmt
$stmt = $taskManager->getTasks();

// Se a variável de sessão 'tasks' não estiver definida, define-a com as tarefas obtidas do banco de dados
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = $stmt;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Gerenciador de Tarefas</title>
</head>
<body>
<div class="container">

    <?php
        // Exibe uma mensagem de sucesso se a variável de sessão 'success' estiver definida
        if( isset($_SESSION['success'])) {
    ?>
        <div class = "alert-success"><?php echo $_SESSION['success'];  ?></div>
    <?php
        unset($_SESSION['success']);
        }
    ?>

    <?php
        // Exibe uma mensagem de erro se a variável de sessão 'error' estiver definida
        if( isset($_SESSION['error'])) {
    ?>
        <div class = "alert-error"><?php echo $_SESSION['error'];  ?></div>
    <?php
        unset($_SESSION['error']);
        }
    ?>

    <div class="header">
        <h1>Gerenciador de Tarefas</h1>
    </div>
    <div class="form">
        <form action="task.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="insert" value="insert">
            <label for="task_name">Tarefa:</label>
            <input type="text" name="task_name" placeholder="Nome da Tarefa">
            <label for="task_description">Descrição:</label>
            <input type="text" name="task_description" placeholder="Descrição da Tarefa">
            <label for="task_date">Data:</label>
            <input type="date" name="task_date" placeholder="">
            <label for="task_image">Imagem:</label>
            <input type="file" name="task_image">
            <button type="submit">Cadastrar</button>
        </form>
        <?php
            // Exibe uma mensagem de erro se a variável de sessão 'message' estiver definida
            if (isset($_SESSION['message'])) {
                echo "<p style='color: #D50000';>" . $_SESSION['message'] . "</p>";
                unset($_SESSION['message']);
            }
        ?>
    </div>
    <div class="separator">
    </div>

    <div class="list-tasks">
        <?php
            echo "<ul>";

            // Loop através das tarefas armazenadas na variável $stmt
            foreach ($stmt as $task) {
                echo "<li>
                    <div class='task-container'>
                        <a href='details.php?key=" . $task['id'] . "'>" . $task['task_name'] . "</a>
                        <button type='button' class='btn-clear' onclick='deletar".$task['id']."()'>Remover</button>
                    </div>
                    <script>
                        // Função JavaScript para confirmar a remoção da tarefa
                        function deletar".$task['id']."() {
                            if (confirm('Confirmar remoção?')) {
                                // Redireciona para a página 'task.php' com a chave da tarefa a ser removida.
                                window.location = 'http://localhost/gerenciadortarefas/task.php?key=".$task['id']."';
                            }
                            return false;
                        }
                    </script>
                    </li>";
            }
            
            echo "</ul>";
        ?>
    </div>
    <div class="footer">
        <p>Desenvolvido por @santiagors002</p>
    </div>
</div>
</body>
</html>
