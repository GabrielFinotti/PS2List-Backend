<?php
include('../../index.php');

// Cabeçalho para tratar erros de CORS.
header("Access-Control-Allow-Origin: https://ps2list.netlify.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Condicao para verificar qual o tipo de requisicao.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Transformando o json em array.
    $data = json_decode(file_get_contents('php://input'), true);
    $userTrim = trim($data['user']);
    $emailTrim = trim($data['email']);
    $passwordTrim = trim($data['password']);
    
    // Verificando se os dados estão de acordo com as condições estabelecidas. Se de acordo, setando os valores nas variaveis.
    if (strlen($data['user']) < 4 || strlen($data['user']) > 16) {
        die('Mínimo 4 e máximo 16 caracteres!');
    } elseif (empty($data['user']) || $data['user'] !== $userTrim) {
        die('Usuário não pode estar vazio!');
    } else {
        $user = $userTrim;
    }

    if (empty($data['email'] || $data['email'] !== $emailTrim)) {
        die('Email não pode estar vazio!');
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        die('Email inválido!');
    } else {
        $email = $emailTrim;
    }

    if (strlen($data['password']) < 6 || strlen($data['password']) > 12) {
        die('Mínimo 6 e máximo 12 caracteres!');
    } elseif (empty($data['password']) || $data['password'] !== $passwordTrim) {
        die('Senha não pode estar vazia!');
    } else {
        $password = $passwordTrim;
    }

    // Verificando se os dados ja existem na tabela antes de inseri-los.

    // Verificando se o nome de usuário já existe.
    $stmt = $mysqli->prepare("SELECT * FROM usr WHERE usr = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Nome de usuário já existe!");
        exit;
        $stmt->close();
    }

    // Verificando se o email já existe.
    $stmt = $mysqli->prepare("SELECT * FROM usr WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        die("Email já existe!");
        exit;
        $stmt->close();
    }

    // Se passado pelas verificacoes, inserindo os dados na tabela.
    $stmt = $mysqli->prepare("INSERT INTO usr (usr, email, psw) VALUE (?, ?, ?)");
    $stmt->bind_param("sss", $user, $email, $password);
    // Se bem-sucedido, retornando um json com os valores cadastrados parar o frontend.
    if ($stmt->execute()) {
        echo json_encode(["user:" => $user, "email:" => $email, "password: " => $password], JSON_PRETTY_PRINT);
    } else {
        echo "Falha ao cadastrar usuários, erro:" . $stmt->error;
    }

    // Fechando a conexao.
    $stmt->close();
}
