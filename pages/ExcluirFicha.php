<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";
    require "../src/repository/classeRepository.php";

    session_start();
    $pdo = conectar();
    $fichaId = $_GET["ficha_id"];
    $usuarioId = $_SESSION['id_usuario'];
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($fichaId);
    $classeRepository = new classRepository($pdo);

    $fichaAtual = $fichaRepository->buscarPorID($fichaId);
    $fichaRepository->excluirFichaComSeguranca($pdo, $fichaId, $usuarioId);

    header("Location: ..\pages\Menu.php");