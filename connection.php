<?php

function OpenCon()
{
    $dsn = 'mysql:dbname=phpwebservice;host=localhost';
    $user = 'root';
    $password = '';
    $conn = null;

    try {
        $conn = new PDO($dsn, $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }

    return $conn;
}

function CloseCon($conn)
{
    $conn = null;
}
