<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";
    require "../src/repository/classeRepository.php";

    session_start();
    $pdo = conectar();
    $fichaId = $_GET["id_ficha"];
    $classeId = $_GET["id_classe"];
    $usuarioId = $_SESSION['id_usuario'];
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($fichaId);
    $classeRepository = new classRepository($pdo);

    $fichaAtual = $fichaRepository->buscarPorID($fichaId);
    $fichaRepository->excluirClasseDeFichaComSeguranca($pdo, $fichaId, $classeId, $usuarioId);

    header("Location: ../pages/Ficha" . $fichaRepository->buscarSistemaNome($fichaAtual->getId()) . ".php?id_ficha=" . $fichaAtual->getId());