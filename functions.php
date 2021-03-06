<?php

function getPost($input)
{
    return $_POST[$input] ?? null;
}

function get($input)
{
    return $_GET[$input] ?? null;
}

/**
 * @param $message
 * @param null $object
 * @param bool $success
 *
 * @return string
 */
function buildResponse($message, $object = null, $success = true)
{
    $response = (object)[
        'sucesso' => $success,
        'mensagem' => $message,
        'dados' => $object
    ];

    if ($success) {
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 400 Bad Request');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
    exit;
}
