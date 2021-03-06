<?php
require_once "functions.php";
require_once "connection.php";

$conn = OpenCon();

$cpf = get('cpf');
$nome = get('nome');

$messages = [
    'cpf_length' => 'O CPF precisa ter 11 dígitos e ser composto apenas por números',
    'nome_length' => 'O Nome precisa ter pelo menos 2 dígitos',
];

if (isset($cpf) && strlen($cpf) != 11) {
    return buildResponse($messages['cpf_length'], null, false);
}

if (isset($nome) && strlen($nome) < 2) {
    return buildResponse($messages['nome_length'], null, false);
}

try {

    $query = "SELECT * FROM `tbl_clientes` WHERE `cpf` = :cpf AND `nome` LIKE :nome";
    $data = [
        'cpf' => $cpf,
        'nome' => "%" . $nome . "%"
    ];

    if (empty($cpf) && !empty($nome)) {
        $data = ['nome' => "%" . $nome . "%"];
        $query = "SELECT * FROM `tbl_clientes` WHERE `nome` LIKE :nome";
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($data);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($clientes) > 0) {
        $message = count($clientes) . (count($clientes) > 1 ? " registros encontrados" : ' registro encontrado');
    } else {
        $message = "Nenhum cliente encontrado com os parâmetros informados: [cpf: $cpf, nome: $nome]";
    }

    return buildResponse($message, ['clientes' => $clientes]);

} catch (\Throwable $ex) {
    return buildResponse($ex->getMessage(), null, false);
}

CloseCon($conn);

