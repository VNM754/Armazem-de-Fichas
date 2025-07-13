<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";
    require "../src/repository/classeRepository.php";
    require "../src/repository/origemRepository.php";
    require "../src/repository/racaRepository.php";
    require "../src/repository/poderesRepository.php";
    require "../src/repository/equipamentoRepository.php";
    require "../src/repository/magiasRepository.php";

    session_start();
    $pdo = conectar();
    $Id_usuario = $_SESSION['id_usuario'];
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($usuario);
    $classeRepository = new classRepository($pdo);
    $origemRepository = new origemRepository($pdo);
    $racaRepository = new racaRepository($pdo);
    $poderRepository = new poderesRepository($pdo);
    $equipamentoRepository = new equipamentoRepository($pdo);
    $magiasRepository = new magiasRepository($pdo);
    
    // var_dump($_POST);
    // var_dump($_FILES["imagem-personagem"]);
    
    $diretorio_alvo = "../images/";
    $imagem_padrao = $diretorio_alvo . "imagem.jpg";
    if (!isset($_FILES["imagem-personagem"]) || $_FILES["imagem-personagem"]["error"] === UPLOAD_ERR_NO_FILE) {
        $imagem_url = $imagem_padrao;
    } else {
        $arquivo_alvo = $diretorio_alvo . basename($_FILES["imagem-personagem"]["name"]);
        $uploadOK = 1;
        $tipoArquivo = strtolower(pathinfo($arquivo_alvo, PATHINFO_EXTENSION));
        
        if (isset($_POST["enviar"])) {
            $check = getimagesize($_FILES["imagem-personagem"]["tmp_name"]);
            if ($check !== false) {
                echo "Arquivo é uma imagem - " . $check["mime"]. ".\n";
                $uploadOK = 1;
            }else {
                echo "Arquivo não é uma imagem.\n";
                $uploadOK = 0;
            }
        }
    
        if (file_exists($arquivo_alvo)) {
            $imagem_url = $arquivo_alvo;
            echo "Arquivo já existente!\n";
            $uploadOK = 0;
        }
    
        if ($tipoArquivo != "jpg" & $tipoArquivo != "png" && $tipoArquivo != "jpeg" && $tipoArquivo != "gif") {
            echo "Apenas arquivos JPG, PNG, JPEG e GIF são aceitos.\n";
            $uploadOK = 0;
        }
    
        if ($uploadOK == 0) {
            echo "Erro no upload do arquivo.";
        } else {
            if (move_uploaded_file($_FILES["imagem-personagem"]["tmp_name"], $arquivo_alvo)) {
                $imagem_url = $arquivo_alvo;
                echo "O arquivo" . htmlspecialchars( basename($_FILES["imagem-personagem"]["name"])) . "foi feiot upload.\n";
            } else echo "Erro no upload do arquivo.\n";
        }
    }


    
    $sistema_id = $_POST['sistema_id'];
    $nome_personagem = $_POST['nome-personagem'] ?? '';
    $raca_personagem = $_POST['raca_personagem'] ?? '';
    $nova_raca = $_POST['nova_raca'] ?? '';
    $nova_raca_descricao = $_POST['nova_raca_descricao'] ?? '';
    $origem_personagem = $_POST['origem_personagem'] ?? '';
    $nova_origem = $_POST['nova_origem'] ?? '';
    $nova_origem_descricao = $_POST['nova_origem_descricao'] ?? '';
    $atributos = $_POST['atributos'] ?? '';
    $classes = $_POST['classes'] ?? '';
    $poderes = $_POST['poderes'] ?? '';
    $equipamentos = $_POST['equipamentos'] ?? '';
    $magias = $_POST['magias'] ?? '';
    $pericias = $_POST['pericias'] ?? '';
    $periciasTexto = implode(', ', $pericias) ?? '';

    $raca_id = 0;
    $origem_id = 0;
    
    if (isset($nova_raca) && $nova_raca != '') {
        $raca_id = $racaRepository->adicionarRaca($sistema_id, $nova_raca, $nova_raca_descricao, $pdo);
        if ($raca_id != false) {
            echo "Raça Criada no Banco de Dados";
        } else echo "Falha na Criação da Raça";
    }else {
        $raca_id = $raca_personagem;
    }

    if (isset($nova_origem) && $nova_origem != '') {
        $origem_id = $origemRepository->adicionarorigem($sistema_id, $nova_origem, $nova_origem_descricao, $pdo);
        if ($origem_id != false) {
            echo "Origem Criada no Banco de Dados";
        } else echo "Falha na Criação da Origem";
    }else $origem_id = $origem_personagem;
    // var_dump($origem_personagem);

    if ($ficha_id = $fichaRepository->adicionarNovaFicha($Id_usuario, $nome_personagem, $sistema_id, $imagem_url, $raca_id, $origem_id, $periciasTexto, $pdo)) {
        echo "Ficha Criada com Sucesso";
    } else echo "Falha na Criação da Ficha";

    if ($fichaRepository->criarAtibutos($ficha_id, $atributos, $pdo)) {
        echo "Atributos colocados na Ficha com Sucesso";
    } else echo "Falha na Atribuição de Atributos";

    if ($classes != '') {
        foreach ($classes as $classe) {
            if ($classe['nova_classe'] !== "") {
                var_dump($classe);
                $verifica = $classeRepository->adicionarclasse($sistema_id, $classe['nova_classe'], $classe['nova_classe_descricao'], $classe['nova_classe_dado_vida'], $pdo);
                if ($verifica == true) {
                    echo "Classe Criada no Banco de Dados";
                    $classe_id = $classeRepository->buscarClassePorNome($classe['nova_classe'], $sistema_id, $pdo);
                    var_dump($classe_id[0][0]);
                    $verifica = $classeRepository->adicionarPMPorNivel($classe_id[0][0], $classe['pm_por_nivel'], $pdo);
                    if ($verifica == true) {
                        echo "PMs Atribuidos a Classe com Sucesso";
                    } else "Falha na Atribuição de PMs";
                }
            } else {
                $verifica = $fichaRepository->adicionarClasse($ficha_id,);
            }
        }    
    }

    if ($poderes != '') {
        foreach ($poderes as $poder) {
            if ($poder["novo_poder"] !== "") {
                $novo_poder_id = $poderRepository->adicionarPoder($sistema_id, $poder["novo_poder"], $poder["novo_poder_descricao"], $pdo);
                if ($novo_poder_id != false) {
                    echo "Poder Criado no Banco de Dados";
                    $verifica = $poderRepository->adicionarPoderFicha($novo_poder_id, $ficha_id, $pdo);
                    if ($verifica == true) {
                        echo "Poder Vinculado a Ficha com Sucesso.";
                    } else return "Falha na Vinculação do Poder com a Ficha.";
                }else return "Falha na Criação do Poder";
            }
        }
    }

    if ($equipamentos != '') {
        foreach ($equipamentos as $equipamento) {
            if ($equipamento["novo_equipamento_nome"] !== "") {
                $novo_equipamento_id = $equipamentoRepository->adicionarEquipamento($equipamento["novo_equipamento_tipo"], $equipamento["novo_equipamento_nome"], $equipamento["novo_equipamento_descricao"], $pdo);
                if ($novo_equipamento_id != false) {
                    echo "Equipamento Criado no Banco de Dados";
                    $verifica = $equipamentoRepository->adicionarEquipamentoFicha($novo_equipamento_id, $ficha_id, $pdo);
                    if ($verifica == true) {
                        echo "Equipamento Vinculado a Ficha com Sucesso.";
                    } else return "Falha na Vinculação do Equipamento com a Ficha.";
                }else return "Falha na Criação do Equipamento";
            }
        }
    }

    if ($magias != '') {
        foreach ($magias as $magia) {
            if ($magia["nova_magia"] !== "") {
                $nova_magia_id = $magiasRepository->adicionarMagia($sistema_id, $magia["nova_magia"], $magia["nova_magia_nivel"], $magia["nova_magia_escola"], $magia["nova_magia_descricao"], $pdo);
                if ($nova_magia_id != false) {
                    echo "Magia Criada no Banco de Dados";
                    $verifica = $magiasRepository->adicionarMagiaFicha($nova_magia_id, $ficha_id, $pdo);
                    if ($verifica == true) {
                        echo "Magia Vinculada a Ficha com Sucesso.";
                    } else return "Falha na Vinculação da Magia com a Ficha";
                } else return "Falha na Criação da Magia";
            }
        }
        
    }


    // switch ($sistema_id) {
    //     case 1:
    //         header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
    //         break;

    //     case 2:
    //         header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
    //         break;

    //     case 3:
    //         header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
    //         break;

    //     case 4:
    //         header("Location: " . "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha_id) . ".php?id_ficha=" . $ficha_id);
    //         break;
        
    //     default:
    //         # code...
    //         break;
    // }