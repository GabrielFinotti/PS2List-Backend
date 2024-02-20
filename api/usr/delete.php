<?php
include('../../index.php');

// Cabeçalho para tratar erros de CORS.
header("Access-Control-Allow-Origin: https://ps2list.netlify.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Transformando o json em array.
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificando se o nome do usuario nao esta vazio.
    if (empty($data['user'])) {
        die('Usuario inexistente');
    } else {
        $user = $data['user'];
    }

    // Verificando se o usuario existe no banco de dados.
    $stmt = $mysqli->prepare("SELECT * FROM usr WHERE usr = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se nao existir, retorna um erro, se existir, deleta o usuario e retorna uma mensagem informativa.
    if ($result->num_rows > 0) {
        $stmt = $mysqli->prepare("DELETE FROM usr WHERE usr = ?");
        $stmt->bind_param("s", $user);
        if ($stmt->execute()) {
            echo json_encode(['Usuario deletado com sucesso']);
        } else {
            echo "Erro ao deleter usuario, erro:" . $stmt->error;
        }
    } else {
        echo 'Usuario nao encontrado';
        $stmt->close();
    }
}
