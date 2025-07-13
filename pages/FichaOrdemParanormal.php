<?php
    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";
    require "../src/repository/classeRepository.php";
    require "../src/repository/poderesRepository.php";
    require "../src/repository/origemRepository.php";
    require "../src/repository/equipamentoRepository.php";
    require "../src/repository/magiasRepository.php";

    session_start();
    $pdo = conectar();
    $fichaId = $_GET["id_ficha"];
    $usuarioRepository = new usuarioRepository($pdo);
    $fichaRepository = new fichaRepository($pdo);
    $classeRepository = new classRepository($pdo);
    $poderesRepository = new poderesRepository($pdo);
    $equipamentosRepository = new equipamentoRepository($pdo);
    $magiasRepository = new magiasRepository($pdo);
    $fichaAtual = $fichaRepository->buscarPorID($_GET["id_ficha"]);
    $id_sistema = $fichaAtual->getSistema();
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichas = $fichaRepository->buscarTodos($usuario);
    $classes = $classeRepository->buscarClassesPorSistema($id_sistema,$pdo);
    $equipamentosFicha = $equipamentosRepository->buscarEquipamentosDaFicha($fichaId, $pdo);
    $poderesFicha = $poderesRepository->buscarPoderesDaFicha($fichaId, $pdo);
    $atributos = $fichaRepository->buscarAtributos($fichaAtual->getId(), $pdo);
    $classesDaFicha = $fichaRepository->buscarClassesDaFicha($fichaAtual->getId(), $pdo);
    $periciasMarcadas = array_map('trim', explode(',', $fichaAtual->getPericias()));
    $pericias = $fichaRepository->definirPericias($id_sistema);
    $magias = $magiasRepository->buscarMagiasDaFicha($fichaId, $pdo);
    
    $racas = $pdo->prepare("SELECT * FROM racas WHERE sistema_id = :id_sistema");
    $racas->execute([':id_sistema' => $id_sistema]);
    $racas = $racas->fetchAll(PDO::FETCH_ASSOC);
    
    $origens = $pdo->prepare("SELECT * FROM origens WHERE sistema_id = :id_sistema");
    $origens->execute([':id_sistema' => $id_sistema]);
    $origens = $origens->fetchAll(PDO::FETCH_ASSOC);
    
    $vidaMaxima = 0;
    $manaMaximo = 0;
    $nivelTotal = 0;
    $classesExistentes = 0;
    $modificadores = [];
    
    foreach ($atributos as $atributo) {
        if ($atributo['nome'] == 'VIG') {
            $modificadorVida = floor((($atributo['valor'])-10)/2);
            break;
        }
    }
    
    $classes_disponiveis = array_filter($classes, function ($classe) use ($classesDaFicha) 
    {
        foreach ($classesDaFicha as $adicionada) {
            if ($classe['id'] == $adicionada['classe_id']) {
                return false; // já está na ficha
            }
        }
        return true;
    });
    
    foreach ($classesDaFicha as $classe) {
        $nivelTotal += $classe['nivel'];
    }

    
    foreach ($atributos as $atributo) {
        $modificadores[$atributo['nome']] = floor(($atributo['valor']-10)/2);
    }


?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha - <?= $fichaAtual->getNome()?> </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/0955cef844.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../reset.css">
</head>
<body>
    <header class="cabecalho">
        <input type="checkbox" id="perfil" class="cabecalho__botao">
        <label for="perfil">
            <span class="perfil__texto">
                <i class="fa-solid fa-circle-user fa-xl cabecalho__imagem"></i>
                Bem-Vindo 
                <?=
                $usuario['nome'];
                ?>
            </span>
        </label>
        <ul class="lista-menu">
            <li class="lista-menu__item">
                  <a href="\pages\Perfil.php" class="lista-menu__link">Editar Perfil</a>
             </li>
             <li class="lista-menu__item">
                  <a href="../pages/Menu.php" class="lista-menu__link">Voltar ao Menu</a>
             </li>
             <li class="lista-menu__item">
                <a href="../pages/Deslogar.php" class="lista-menu__link">Logout</a>
             </li>
        </ul>

    </header>
    <hr class="barra__divisora">
    <section>
        <form action="<?= "AtualizarFicha.php?id_ficha= " . $fichaId?>" class="ficha__T20" method="post">
            <input type="hidden" name="ficha_id" value="<?= $fichaId ?>">
            <img src="<?php echo "../" . $fichaAtual->getImagemDiretorio()?>" alt="<?="Imagem " . $fichaAtual->getNome()?>" class="ficha-T20-Imagem">
            <section class="ficha-T20-cabecalho">
                <section class="ficha-T20-cabecalho-textboxes">
                    <label for="nome-personagem" class="ficha-T20-cabecalho-label">Nome: </label>
                    <input type="text" name="nome-personagem" id="nome-personagem" class="ficha-T20-cabecalho-textbox ficha-T20-valores-textbox" placeholder="Nome" value="<?=$fichaAtual->getNome()?>">
                    <label for="raca-personagem" class="ficha-T20-cabecalho-label">Raça: </label>
                    <select name="raca-personagem" id="raca-personagem" class="ficha-T20-cabecalho-select">
                        <option value="<?=$fichaAtual->getRaca()?>"><?=$fichaRepository->buscarRaca($fichaAtual->getRaca())?></option>
                        <?php foreach ($racas as $raca): ?>
                            <?php 
                                    if (($raca['id']) == ($fichaAtual->getRaca())) {
                                        continue;
                                    }
                                ?>
                            <option value="<?= $raca['id'] ?>"><?= htmlspecialchars($raca['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="origem-personagem" class="ficha-T20-cabecalho-label">Origem: </label>
                    <select name="origem-personagem" id="origem-personagem" class="ficha-T20-cabecalho-select">
                        <option value="<?=$fichaAtual->getOrigem()?>"><?=$fichaRepository->buscarOrigem($fichaAtual->getOrigem())?></option>
                        <?php foreach ($origens as $origem): ?>
                            <?php 
                                    if (($origem['id']) == ($fichaAtual->getOrigem())) {
                                        continue;
                                    }
                                ?>
                            <option value="<?= $origem['id'] ?>"><?= htmlspecialchars($origem['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label class="ficha-T20-cabecalho-label">Classes:</label>
                    <div>
                        <?php foreach ($classesDaFicha as $classe) :?>
                            <span class="ficha-T20-cabecalho-label">
                                <?= " - " . $classe['nome_classe']?>
                            </span> <br>
                            <input type="hidden" name="classes_existentes_ids[]" value="<?=$classe['classe_id']?>">
                            <input class="ficha-T20-cabecalho-textbox ficha-t20-classes-nivel" type="number" name="niveis[<?=$classe['classe_id']?>]" min="1" value="<?= $classe['nivel']?>" onmousewheel>
                            <button class="ficha-T20-btn-add-classes" type="button" id="btn-Add-Classe">
                                <i class="fa-solid fa-circle-plus fa-xl ficha-T20-cabecalho-delete" alt="Adicionar Classe" onclick="adicionarCampoClasse()"></i>
                            </button>
                            <a href="<?= "\pages\ExcluirClasse.php?id_ficha=". $fichaId ."&id_classe=" . $classe['classe_id']?>">
                                <i class="fa-solid fa-circle-xmark fa-xl ficha-T20-cabecalho-delete" alt="Excluir Ficha"></i>
                            </a>
                            <br>
                        <?php endforeach;?>
                            <?php if (empty($classesDaFicha)):?>
                                <button class="ficha-T20-btn-add-classes" type="button" id="btn-Add-Classe">
                                    <i class="fa-solid fa-circle-plus fa-xl ficha-T20-cabecalho-delete" alt="Adicionar Classe" onclick="adicionarCampoClasse()"></i>
                                </button>
                            <?php endif;?>
                        <div id="container-classes"></div>
                            <script>
                            function adicionarCampoClasse() {
                                const container = document.getElementById("container-classes");

                                // Criação do wrapper para este bloco de classe
                                const div = document.createElement("div");
                                div.classList.add("bloco-classe");

                                // Criar o select de classes
                                const selectHTML = `
                                    <select class="ficha-T20-cabecalho-select criar-ficha-elemento" name="classes_adicionadas[]" onchange="mostrarCamposNovaClasse(this)">
                                        <option value="">Selecione uma classe</option>
                                        <?php foreach ($classes_disponiveis as $classe): ?>
                                            <option value="<?= $classe['id'] ?>"><?= htmlspecialchars($classe['nome']) ?></option>
                                        <?php endforeach; ?>
                                        <option value="nova">Nova Classe</option>
                                    </select>

                                    <div class="ficha-T20-cabecalho-textbox campos-nova-classe" style="display: none;">
                                        <input class="ficha-T20-valores-textbox" type="text" name="nova_classe" placeholder="Nome da nova classe"><br>
                                        <textarea class="ficha-T20-valores-textbox" name="descricao_nova_classe" placeholder="Descrição da nova classe"></textarea><br>
                                        <input class="ficha-T20-valores-textbox" type="text" name="dado_vida" placeholder="Dado de Vida (ex: 1d8)"><br>
                                        <input class="ficha-T20-valores-textbox" type="text" name="pm_por_nivel" placeholder="Pms por Nível (ex: 5)"><br>
                                        <label><input type="checkbox" name="classe_inicial"> Classe Inicial?</label>
                                    </div>

                                    <label for="nivel[]">Nível:</label>
                                    <input class="ficha-T20-valores-textbox" type="number" name="nivel_nova_classe[]" min="1" value="1" onmousewheel>
                                `;

                                div.innerHTML = selectHTML;
                                container.appendChild(div);
                            }
                            function mostrarCamposNovaClasse(select) {
                                const wrapper = select.closest(".bloco-classe");
                                const camposNovaClasse = wrapper.querySelector(".campos-nova-classe");
                                camposNovaClasse.style.display = (select.value === "nova") ? "block" : "none";
                            }
                            </script>
                        </select>
                    </div>
                </section>
                <section class="ficha-T20-cabecalho-valores">
                    <label class="ficha-T20-cabecalho-label">Pontos de Vida:</label>
                        <p>
                            <input name="pv_atual" class="ficha-T20-valores-textbox" type="number" placeholder="" value="<?= ($fichaRepository->buscarPV($fichaAtual->getId())) ? ($fichaRepository->buscarPV($fichaAtual->getId())) : 0 ?>" onmousewheel><label class="ficha-T20-cabecalho-label">
                                <?php
                                foreach ($classesDaFicha as $classe) {
                                    $dadoVida = intval($classe['dado_vida']);
                                    $nivel = $classe['nivel'];

                                    if ($classe['inicial'] == 1) {
                                        $vidaMaxima+= $dadoVida*4;

                                        if ($nivel > 1) {
                                            $vidaMaxima += $dadoVida*($nivel-1) + $modificadorVida*$nivel;
                                        }
                                    } else {
                                        $vidaMaxima += $dadoVida * $nivel + $modificadorVida*$nivel;
                                    }
                                }
                                foreach ($atributos as $atributo) {
                                    if ($atributo['nome'] == "VIG") {
                                        echo "/".(($vidaMaxima + ($vidaMaxima/4)*$nivelTotal) + ($nivelTotal*$modificadorVida));
                                    }
                                }
                                ?>
                            </label>
                        </p>
                    <label class="ficha-T20-cabecalho-label">Pontos de Mana:</label>
                        <p>
                            <input name="pm_atual" class="ficha-T20-valores-textbox" type="number" placeholder="" value="<?= ($fichaRepository->buscarPM($fichaAtual->getId())) ? ($fichaRepository->buscarPM($fichaAtual->getId())) : 0 ?>" onmousewheel><label class="ficha-T20-cabecalho-label">
                                <?php
                                foreach ($classesDaFicha as $classe) {
                                    $manaClasse = $fichaRepository->buscarPMPorClasseENivel($pdo,$classe['classe_id'],1);
                                    $manaMaximo += $manaClasse*$classe['nivel'];
                                    foreach ($atributos as $atributo) {
                                        if ($atributo['nome'] == "PRE") {
                                            $manaMaximo += $atributo['valor'];
                                        }
                                    }
                                }

                                echo "/" . $manaMaximo;
                                ?>
                            </label>
                        </p>
                    </label>
                </section>
            </section>
            <hr class="barra__divisora">
    
            <section class="ficha-T20-Atributos">
                <input type="checkbox" id="T20-atributos" class="ficha-T20-atributos-liberar">
                <label for="T20-atributos">
                    <h2 class="menu__titulo">
                        Atributos
                    </h2>
                </label>
                <ul class="ficha-T20-atributos-lista">
                    <?php foreach ($atributos as $atributo) :?>
                    <li class="ficha-T20-atributos-lista-atributo">
                        <section class="ficha-T20-atributos-individual">
                            <p class="ficha-T20-atributos-nome ficha-T20-atributos-text"><?= $atributo['nome']?></p><br><br>
                            <p class="ficha-T20-atributos-valor ficha-T20-atributos-text"><?= ($atributo['valor'])?></p>
                            <input type="text" name="atributos[<?= $atributo['nome']?>]" class="ficha-T20-atributos-input" value="<?= $atributo['valor']?>">
                    </section>
                    </li>
                    <?php endforeach;?>
                </ul>
            </section>
            <hr class="barra__divisora">
    
            <section class="ficha-T20-Pericias">
                <input type="checkbox" id="T20-pericias" class="ficha-T20-pericias-liberar">
                <label for="T20-pericias">
                    <h2 class="menu__titulo">
                        Pericias
                    </h2>
                </label>
                    <ul class="ficha-T20-pericias-lista">
                        <li class="menu__titulo">
                             <?="Bônus de Treinamento: " . $fichaRepository->calcularBonusDeTrei($fichaAtual->getId()) ?>
                        </li>
                        <?php foreach ($pericias as $pericia): ?>
                            <label>
                                <li class="ficha-T20-pericias-lista-item">
                                    <label class="ficha-T20-atributos-text" for="<?= "ficha-T20-pericias-" . $pericia?>">
                                    <input type="checkbox" id="<?= "ficha-T20-pericias-" . $pericia?>" name="pericias[]" 
                                    value="<?= $pericia ?>" <?= in_array($pericia, $periciasMarcadas) ? 'checked' : '' ?>>
                                        <?= $pericia ?>
                                    </label>
                                    <div>
                                        <label>
                                            <?php 
                                            if (in_array($pericia, $periciasMarcadas)) {
                                                echo $fichaRepository->calcularBonusDeTreiOP($fichaAtual->getId());
                                            } else 0;
                                            ?>
                                        </label>
                                        <select name="atributo_pericia[<?= $pericia?>]" id="" class="ficha-T20-cabecalho-select">
                                            <?php $atributoPadrao = $fichaRepository->definirAtributoPericiasOP($pericia);
                                            foreach ($atributos as $atributo):?>
                                                <option value="<?= $atributo['nome']?>" <?= $atributo['nome'] == $atributoPadrao ? 'selected': ''?>>
                                                    <?= $atributo['nome']?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="text" placeholder="Bônus">
                                    </div>
                                </li>
                            </label>
                        <?php endforeach; ?>
                    </ul>
            </section>
            <hr class="barra__divisora">
    
            <section class="ficha-T20-Ataques">
                <section class="ficha-T20-Ataques">
                <input type="checkbox" id="T20-ataques" class="ficha-T20-ataques-liberar">
                <label for="T20-ataques">
                    <h2 class="menu__titulo">
                        ataques
                    </h2>
                </label>
                <ul class="ficha-T20-ataques-lista">
                        <?php foreach ($equipamentosFicha as $equipamento): ?>
                            <?php if ($equipamento['tipo'] == "arma") :?>
                            <li class="ficha-T20-ataques-lista-item">
                                <div class="ficha-T20-poderes-item">
                                    <label class="ficha-T20-cabecalho-label ficha-T20-poder-titulo"><?=$equipamento['nome']?></label>
                                        <hr class="barra__divisora">
                                    <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto">
                                        <?php $detalhes = json_decode($equipamento['detalhes'], true);
                                        echo "Dano: " . $detalhes['dano'] . ". - Tipo de Dano: " . $detalhes['tipo']; 
                                        ?></label>
                                    <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto"><?= "Descrição: " . $equipamento['descricao']?></label>
                                </div>
                            </li>
                            <?php endif;?>
                        <?php endforeach; ?>
                    </ul>
            </section>
            <hr class="barra__divisora">
            
            <section class="ficha-T20-Poderes">
                <input type="checkbox" id="T20-poderes" class="ficha-T20-poderes-liberar">
                <label for="T20-poderes">
                    <h2 class="menu__titulo">
                        poderes
                    </h2>
                </label>
                <ul class="ficha-T20-poderes-lista">
                    <label for="T20-poderes">
                    <h2 class="menu__titulo">
                        Habilidades de Raça
                    </h2>
                </label>
                    <?php $poderes = $poderesRepository->buscarPoderesDaRaca($fichaAtual->getRaca(),$pdo)?>
                    <?php foreach ($poderes as $poder): ?>
                        <li class="ficha-T20-poderes-lista-item">
                            <div class="ficha-T20-poderes-item">
                                <label class="ficha-T20-cabecalho-label ficha-T20-poder-titulo">
                                    <?=$poder['nome']?>
                                </label>
                                <hr class="barra__divisora">
                                <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto"><?= $poder['descricao']?></label>
                            </div>
                        </li>
                    <?php endforeach; ?>    
                <hr class="barra__divisora">
                    <label for="T20-poderes">
                    <h2 class="menu__titulo">
                        Habilidades de Classe
                    </h2>
                </label>
                    <?php foreach ($classesDaFicha as $classe): ?>
                        <?php $poderes = $poderesRepository->buscarPoderesDaClasse($classe['classe_id'], $pdo)?>
                        <?php foreach ($poderes as $poder): ?>
                        <li class="ficha-T20-poderes-lista-item">
                            <div class="ficha-T20-poderes-item">
                                <label class="ficha-T20-cabecalho-label ficha-T20-poder-titulo">
                                    <?=$poder['nome']?>
                                </label>
                                <hr class="barra__divisora">
                                <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto"><?= $poder['descricao']?></label>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <hr class="barra__divisora">
                    <label for="T20-poderes">
                    <h2 class="menu__titulo">
                        poderes Gerais
                    </h2>
                </label>
                    <?php foreach ($poderesFicha as $poder): ?>
                        <li class="ficha-T20-poderes-lista-item">
                            <div class="ficha-T20-poderes-item">
                                <label class="ficha-T20-cabecalho-label ficha-T20-poder-titulo">
                                    <?=$poder['nome']?>
                                </label>
                                <hr class="barra__divisora">
                                <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto"><?= $poder['descricao']?></label>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <hr class="barra__divisora">


    
            <section class="ficha-T20-Magias">
                <input type="checkbox" id="T20-magias" class="ficha-T20-magias-liberar">
                <label for="T20-magias">
                    <h2 class="menu__titulo">
                        magias
                    </h2>
                </label>
                <ul class="ficha-T20-magias-lista">
                    <?php foreach ($magias as $magia): ?>
                        <li class="ficha-T20-magias-lista-item">
                            <div class="ficha-T20-poderes-item">
                                <label class="ficha-T20-cabecalho-label ficha-T20-poder-titulo"><?=$magia['nome']?></label>
                            </div>
                            <hr class="barra__divisora">
                            <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto"><?= $magia['nivel'] . "º Círculo. Escola: " . $magia['escola'] ?></label><br>
                            <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto"><?= $magia['descricao']?></label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <hr class="barra__divisora">
    
            <section class="ficha-T20-Equipamentos">
                <input type="checkbox" id="T20-equipamentos" class="ficha-T20-equipamentos-liberar">
                <label for="T20-equipamentos">
                    <h2 class="menu__titulo">
                        equipamentos
                    </h2>
                </label>
                <ul class="ficha-T20-equipamentos-lista">
                        <?php foreach ($equipamentosFicha as $equipamento): ?>
                            <?php if ($equipamento['tipo'] != "arma") :?>
                            <li class="ficha-T20-poderes-lista-item">
                                <div class="ficha-T20-poderes-item">
                                    <label class="ficha-T20-cabecalho-label ficha-T20-poder-titulo"><?=$equipamento['nome']?></label>
                                        <hr class="barra__divisora">
                                    <label class="ficha-T20-cabecalho-label ficha-T20-poder-texto"><?=$equipamento['descricao']?></label>
                                    <hr class="barra__divisora">
                                </div>
                            </li>
                            <?php endif;?>
                        <?php endforeach; ?>
                    </ul>
            </section>
            <hr class="barra__divisora">
            <input type="hidden" name="data" value="<?= date("Y-m-d h:m:s")?>">
            <input class="ficha-botao" type="submit" value="Salvar">
        </form>
    </section>

</body>

<hr class="barra__divisora">
<footer class="rodape">
    <span class="rodape__texto">Desenvolvido por Vinicius Nogueira Martins</span>
</footer>
</html>