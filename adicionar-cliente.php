<?php
require_once "functions.php";
require_once "connection.php";

$conn = OpenCon();

$messages = [
    'cpf' => 'O CPF é obrigatório',
    'cpf_length' => 'O CPF precisa ter 11 dígitos',
    'nome' => 'O Nome é obrigatório',
    'nome_length' => 'O Nome não pode ter mais que 100 dígitos',
    'data_nascimento' => 'A Data de Nascimento é obrigatória',
    'rg' => 'O RG é obrigatório',
    'rg_length' => 'O RG não pode ter mais de 20 dígitos',
];

$cpf = getPost('cpf');
$nome = getPost('nome');
$data_nascimento = getPost('data_nascimento');
$rg = getPost('rg');

if (!empty($cpf) && strlen($cpf) != 11) {
    return buildResponse($messages['cpf_length'], null, false);
}

if (!empty($nome) && strlen($nome) > 100) {
    return buildResponse($messages['nome_length'], null, false);
}

if (!empty($rg) && strlen($rg) > 20) {
    return buildResponse($messages['rg_length'], null, false);
}

if (empty($cpf)) {
    return buildResponse($messages['cpf'], null, false);
}

if (empty($nome)) {
    return buildResponse($messages['nome'], null, false);
}

if (empty($data_nascimento)) {
    return buildResponse($messages['data_nascimento'], null, false);
}

if (empty($rg)) {
    return buildResponse($messages['rg'], null, false);
}


try {

    $data = [
        'cpf' => $cpf,
        'nome' => $nome,
        'data_nascimento' => DateTime::createFromFormat('d/m/Y', $data_nascimento)->format("Y-m-d"),
        'rg' => $rg,
    ];

    $stmt = $conn->prepare('SELECT * FROM `tbl_clientes` WHERE `cpf` = :cpf');
    $stmt->execute(['cpf' => $cpf]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    $message = null;

    if (!empty($cliente)) {
        $update = $conn->prepare('UPDATE `tbl_clientes` SET `nome` = :nome, `rg` = :rg, `data_nascimento` = :data_nascimento WHERE (cpf = :cpf)');
        if ($update->execute($data)) {
            $data['id'] = $cliente['id'];
            $message = "Cliente atualizado com sucesso.";
        }
    } else {
        $insert = $conn->prepare('INSERT INTO `tbl_clientes` (`cpf`, `nome`, `data_nascimento`, `rg`) VALUES(:cpf, :nome, :data_nascimento, :rg)');
        if ($insert->execute($data)) {
            $data['id'] = $conn->lastInsertId();
            $message = "Cliente adicionado com sucesso.";
        }
    }

    return buildResponse($message, ['cliente' => $data]);

} catch (\Throwable $ex) {
    return buildResponse($ex->getMessage(), null, false);
}

CloseCon($conn);

