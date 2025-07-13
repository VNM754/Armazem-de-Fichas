<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";
    require "../src/repository/classeRepository.php";

    session_start();
    $pdo = conectar();
    $id_usuario = $_SESSION['id_usuario'];
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($id_usuario);
    $classeRepository = new classRepository($pdo);


    // print_r($_POST);

    $ficha_id = $_POST['ficha_id'];
    $nome_personagem = $_POST['nome-personagem'] ?? '';
    $raca = $_POST['raca-personagem'];
    $origem = $_POST['origem-personagem'] ?? '';
    $pv_atual = $_POST['pv_atual'] ?? null;
    $pm_atual = $_POST['pm_atual'] ?? null;
    $periciasSelecionadas = $_POST['pericias'] ?? [];
    $classes_existentes_ids = $_POST['classes_existentes_ids'] ?? null;
    $classes_adicionadas = $_POST['classes_adicionadas'] ?? null;
    $niveis = $_POST['niveis'] ?? null;
    $nova_classe = $_POST['nova_classe'] ?? null;
    $descricao_nova_classe = $_POST['descricao_nova_classe'] ?? null;
    $dado_vida = $_POST['dado_vida'] ?? null;
    $pm_por_nivel = $_POST['pm_por_nivel'] ?? null;
    $nivel_nova_classe = $_POST['nivel_nova_classe'] ?? null;
    $inicial = $_POST['classe_inicial'] ?? 0;
    $data_nova = $_POST['data'];
    $espacos_feitico = $_POST['espacos_feitico'] ?? null;
    $atributos = $_POST ['atributos'];

    if (isset($nova_classe) && $nova_classe != "") 
    {
        $sistema_id = $fichaRepository->buscarSistemaIdFicha($ficha_id);
        $result = $classeRepository->adicionarClasse($sistema_id,$nova_classe,$descricao_nova_classe,$dado_vida,$pdo);
        $classe_id = $classeRepository->buscarClassePorNome($nova_classe,$sistema_id,$pdo);
        if ($sistema_id != 1) {
            $classeRepository->adicionarPMPorNivel($classe_id,$pm_por_nivel,$pdo);
        } else $classeRepository->adicionarEspacoFeitico($classe_id[0][0], $espacos_feitico, $pdo);
        $result = true;
        if ($result == true) {
            $fichaRepository->adicionarClasse($ficha_id,$classe_id[0][0],$nivel_nova_classe,$inicial,$pdo);
        }
    }

    if (isset($classes_adicionadas) && $classes_adicionadas != "" && $classes_adicionadas[0] != "nova") {
        
        if (isset($classes_existentes_ids)) {
            $inicial = 0;
        } else $inicial = 1;
        
        for ($i=0; $i < sizeof($classes_adicionadas); $i++) { 
            $fichaRepository->adicionarClasse($ficha_id,$classes_adicionadas[$i],$nivel_nova_classe[$i],$inicial,$pdo);
        }
    }

    
    $periciasTexto = implode(', ', $periciasSelecionadas);

    // Atualiza ficha principal
    $fichaRepository->AtualizarFicha($ficha_id, $nome_personagem,$raca, $origem, $pv_atual, $pm_atual, $periciasTexto, $data_nova, $pdo);
    $check = $fichaRepository->atualizarAtributos($ficha_id,$atributos,$pdo);

    if (isset($classes_existentes_ids)) {
        for ($i=0; $i < sizeof($classes_existentes_ids); $i++) {
            $classeRepository->atualizarClasse($ficha_id,$classes_existentes_ids[$i],$niveis[$classes_existentes_ids[$i]],$pdo);
        }
    }


    $sistema_id = $fichaRepository->buscarSistemaIdFicha($ficha_id);
    switch ($sistema_id) {
        case 1:
            header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
            break;

        case 2:
            header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
            break;

        case 3:
            header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
            break;

        case 4:
            header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
            break;
        
        default:
            # code...
            break;
    }
    ?>