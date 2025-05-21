<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";
    require "../src/repository/classeRepository.php";
    require "../src/repository/origemRepository.php";

    session_start();
    $pdo = conectar();
    $Id_usuario = $_SESSION['id_usuario'];
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($usuario);
    $classeRepository = new classRepository($pdo);
    $origemRepository = new origemRepository($pdo);

    $raca_id = $_POST['raca_personagem'];
    $nome = $_POST['nome-personagem'] ?? '';
    $origem = $_POST['origem_personagem'] ?? '';
    $pv_atual = $_POST['pv_atual'] ?? null;
    $pm_atual = $_POST['pm_atual'] ?? null;
    $periciasSelecionadas = $_POST['pericias'] ?? [];
    $id_sistema = $_POST['sistema_id'];
    $nova_origem = $_POST['nova_origem'];
    $descricao_nova_origem = $_POST['nova_origem_descricao'];
    $periciasTexto = implode(', ', $periciasSelecionadas);

    if (isset($nova_origem)) {        
        $origemRepository->adicionarOrigem($id_sistema,$nome,$descricao,$pdo);
    }
    if (isset($nova_classe)) {
        # code...
    }
    

    $sqlFicha = "INSERT INTO fichas (nome, origem_id, pv_atual, pm_atual, pericias, raca_id, sistema_id, usuario_id) 
                             VALUES (:nome, :origem, :pv_atual, :pm_atual, :pericias, :raca_id, :sistema_id, :usuario_id)";
    $stmtFicha = $pdo->prepare($sqlFicha);
    $stmtFicha->bindParam(':nome',$nome,PDO::PARAM_STR);
    $stmtFicha->bindParam(':origem',$origem,PDO::PARAM_INT);
    $stmtFicha->bindParam(':pv_atual',$pv_atual,PDO::PARAM_INT);
    $stmtFicha->bindParam(':pm_atual',$pm_atual,PDO::PARAM_INT);
    $stmtFicha->bindParam(':pericias',$periciasTexto,PDO::PARAM_STR);
    $stmtFicha->bindParam(':raca_id',$raca_id,PDO::PARAM_INT);
    $stmtFicha->bindParam(':sistema_id',$id_sistema,PDO::PARAM_INT);
    $stmtFicha->bindParam(':usuario_id',$Id_usuario,PDO::PARAM_INT);
    $stmtFicha->execute();

    header("Location: " . "\Menu.php");




