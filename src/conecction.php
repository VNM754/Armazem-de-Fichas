<?php

function conectar() {
    $local_serve = 'localhost';
    $usuario_serve = 'root';
    $senha_serve = 'Bolona15';
    $banco_de_dados = 'armazem_de_fichas';

    $pdo = new PDO("mysql:host=$local_serve;dbname=$banco_de_dados", $usuario_serve, $senha_serve);
    $pdo->exec("SET CHARACTER SET utf8");


    return $pdo;
}

