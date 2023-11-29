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

    // Obtém uma tarefa com base no ID fornecido
    public function getTaskById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para atualizar uma tarefa
    public function updateTaskDescription($id, $newDescription) {
        $stmt = $this->db->prepare('UPDATE tasks SET task_description = :description WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':description', $newDescription);
        return $stmt->execute();
    }
}

// Cria uma instância do TaskManager, passando a conexão do banco de dados como parâmetro
$taskManager = new TaskManager($database);

// Verifica se uma solicitação GET com a chave 'key' foi recebida
if (isset($_GET['key'])) {
    $id = $_GET['key'];
    // Obtém os detalhes da tarefa com base no ID
    $data = $taskManager->getTaskById($id);

    // Verifica se os dados do formulário foram enviados
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_description']) && isset($_POST['new_description'])) {
            // Obtém os dados do formulário
            $newDescription = $_POST['new_description'];

            // Atualiza a descrição no banco de dados
            if ($taskManager->updateTaskDescription($id, $newDescription)) {
                $_SESSION['success'] = "Descrição atualizada com sucesso.";
                // Recarrega a página para exibir a descrição atualizada
                header("Location: details.php?key=$id");
                exit();
            } else {
                $_SESSION['error'] = "Erro ao atualizar a descrição.";
            }
        }
    }
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
<div class="details-container">
    <div class="header">
        <h1><?php echo $data[0]['task_name'];?></h1>
    </div>
    <div class="row">
        <div class="details">
            <dl>
                <dt>Descrição da Tarefa:</dt>
                <dd><?php echo $data[0]['task_description'] ?></dd>
                <dt>Data da Tarefa:</dt>
                <dd><?php echo $data[0]['task_date'] ?></dd>
            </dl>
            <!-- Adiciona um botão para exibir o formulário de atualização -->
            <button class = "update-button" onclick="toggleUpdateForm()">Atualizar Descrição</button>
            <!-- Formulário para atualizar a descrição (inicialmente oculto) -->
            <form class = "update-button" id="updateForm" style="display: none;" method="post" action="">
                <label for="new_description">Nova Descrição:</label>
                <input type="text" name="new_description" required>
                <input type="hidden" name="update_description" value="update">
                <button type="submit">Atualizar</button>
            </form>
            <!-- Adiciona um botão para voltar para o índice -->
            <button class="back-button" onclick="goToIndex()">Voltar</button>
        </div>
        <div class="image">
            <img src = "uploads/<?php echo $data[0]['task_image'] ?>" alt = "Imagem tarefa">
        </div>
    </div>
    <div class="footer">
        <p>Desenvolvido por @santiagors002</p>
    </div>
</div>
<script>
    function toggleUpdateForm() {
        var form = document.getElementById("updateForm");
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }

    // Função para redirecionar para o índice
    function goToIndex() {
        window.location.href = 'index.php';
    }
</script>

</body>
</html>
