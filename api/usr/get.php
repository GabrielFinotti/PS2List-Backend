<?php
include('../../index.php');

// Cabeçalho para tratar erros de CORS.
header("Access-Control-Allow-Origin: https://ps2list.netlify.app");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Condicao para verificar qual o tipo de requisicao.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Transformando o json em array.
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificando se os dados estão de acordo antes de seta-los nas variaveis.
    if (empty($data['email'])) {
        die('Email não pode estar vazio!');
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        die('Email inválido!');
    } else {
        $email = $data['email'];
    }

    if (empty($data['password'])) {
        die('Senha não pode estar vazia');
    } else {
        $password = $data['password'];
    }

    // Verificando se os dados existem na tabela.

    // Verificar se o email existe e recuperando sua respectiva senha.
    $stmt = $mysqli->prepare("SELECT usr, psw FROM usr WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Se o email nao existir, retornar o erro e fechar a requisicao.
    if (!$result->num_rows > 0) {
        die('Email nao encontrado');
        $stmt->close();
    } else {

        //Se o email existir, associar a senha recuperada para verifica-la com a senha enviada pelo usuario.
        $row = $result->fetch_assoc();
        $usr = $row['usr'];
        $psw = $row['psw'];
    }

    // Verificando se a senha enviada pelo usuário é igual a senha cadastrada.

    // Se igual, retorna o nome do usuario relativo aos dados enviados e procede com os valore em json para o frontend.
    if ($password == $psw) {
        echo json_encode(["user" => $usr], JSON_PRETTY_PRINT);
    } else {
        echo 'Senha incorreta';
    }

    // Fechando a conexao.
    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Efetuando a consulta no banco de dados dos nomes de usuarios cadastrados.
    $result = $mysqli->query("SELECT usr FROM usr");

    // Verificando se a consulta foi bem-sucedida.
    if (!$result) {
        echo "Nenhum retorno recebido, erro: " . $mysqli->error;
    } else {

        //Se bem-sucedida, armazenando os resultados em um array.
        $usrs = array();
        while ($row = $result->fetch_assoc()) {
            $usrs[] = $row['usr'];
        }
    }

    // Convertendo os dados do array em um json de string e retornando para o frontend.
    echo json_encode($usrs);

    // Fechando a conexao.
    $mysqli->close();
}
