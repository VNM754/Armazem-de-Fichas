<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";

    session_start();
    $pdo = conectar();
    $id_usuario = $_SESSION['id_usuario'];
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($id_usuario);
    $fichasRecentes = $fichaRepository->buscarRecentes($id_usuario);

    $nome = $_POST['login__nome'] ?? $usuario->getNome();
    $email = $_POST['login__email'] ?? $usuario->getEmail();
    $senha = $_POST['login__senha'] ?? $usuario->getSenha();

    var_dump($nome, $email, $senha);
    $usuarioRepository->atualizarPerfil($id_usuario, $nome, $email, $senha, $pdo);
    header("Location: ..\pages\Menu.php");