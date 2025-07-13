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
    $id_usuario = $_SESSION['id_usuario'];
    $id_sistema = $_GET['id_sistema'];
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($id_usuario);
    
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);

    $classeRepository = new classRepository($pdo);
    $classes = $classeRepository->buscarClassesPorSistema($id_sistema,$pdo);

    $poderesRepository = new poderesRepository($pdo);
    $poderes = $poderesRepository->buscarPoderesPorSistema($id_sistema,$pdo);

    $equipamentosRepository = new equipamentoRepository($pdo);
    $equipamentos = $equipamentosRepository->buscarEquipamentos($pdo);

    $magiasRepository = new magiasRepository($pdo);
    $magias = $magiasRepository->buscarMagiasPorSistema($id_sistema,$pdo);


    $racas = $pdo->prepare("SELECT * FROM racas WHERE sistema_id = :id_sistema");
    $racas->execute([':id_sistema' => $id_sistema]);
    $racas = $racas->fetchAll(PDO::FETCH_ASSOC);

    $origens = $pdo->prepare("SELECT * FROM origens WHERE sistema_id = :id_sistema");
    $origens->execute([':id_sistema' => $id_sistema]);
    $origens = $origens->fetchAll(PDO::FETCH_ASSOC);
    
    $atributos = $fichaRepository->definirAtributos($id_sistema);
    $pericias = $fichaRepository->definirPericias($id_sistema);
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Ficha</title>
    <link rel="stylesheet" href="../reset.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/0955cef844.js" crossorigin="anonymous"></script>
</head>
<body class="criar-ficha-body">
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
                  <a href="../pages/Perfil.php" class="lista-menu__link">Editar Perfil</a>
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
    <form method="POST" action="SalvarFicha.php" enctype="multipart/form-data">
        <input type="hidden" name="sistema_id" value="<?= $id_sistema ?>">
        
        <h2 class="menu__titulo">Nome do Personagem:</h2>
        <hr class="barra__divisora">
        <input class="criar-ficha-elemento criar-ficha-input" type="text" name="nome-personagem" required><br>
        
        <h2 class="menu__titulo">Imagem:</h2>
        <hr class="barra__divisora">
        <input class="criar-ficha-imagem-input" type="file" name="imagem-personagem"><br>
        
        <h2 class="menu__titulo">Raça:</h2>
        <hr class="barra__divisora">
        <select class="ficha-T20-cabecalho-select criar-ficha-elemento" name="raca_personagem" id="raca-select" onchange="mostrarCampoNovaRaca()">
            <option value="">Selecione Uma Raça</option>
            <?php foreach ($racas as $raca): ?>
                <option name="raca-personagem" value="<?= $raca['id'] ?>"><?= htmlspecialchars($raca['nome']) ?></option>
            <?php endforeach; ?>
                <option value="nova">Outra (digite abaixo)</option>
            </select>
            <input type="text"class="criar-ficha-elemento criar-ficha-input" name="nova_raca" id="nova-raca" placeholder="Nova raça" style="display: none;">
            <textarea class="criar-ficha-elemento criar-ficha-input" name="nova_raca_descricao" id="nova-raca-descricao" placeholder="Descrição nova raça" style="display: none;"></textarea><br>
            
            <h2 class="menu__titulo">Origem:</h2>
            <hr class="barra__divisora">
            <select class="ficha-T20-cabecalho-select criar-ficha-elemento" name="origem_personagem" id="origem-select" onchange="mostrarCampoNovaOrigem()">
                <option value="">Selecione Uma Origem</option>
                <?php foreach ($origens as $origem): ?>
                    <option name="origem-personagem" value="<?= $origem['id'] ?>"><?= htmlspecialchars($origem['nome']) ?></option>
                <?php endforeach; ?>
                <option value="nova">Outra (digite abaixo)</option>
            </select>
            <input type="text"class="criar-ficha-elemento criar-ficha-input" name="nova_origem" id="nova-origem" placeholder="Nova origem" style="display: none;">
            <textarea class="criar-ficha-elemento criar-ficha-input" name="nova_origem_descricao" id="nova-origem-descricao" placeholder="Descrição nova origem" style="display: none;"></textarea><br>
        
        <section class="criar-ficha-Atributos">
            <h2 class="menu__titulo">Atributos</h2>
            <hr class="barra__divisora">
            <?php foreach ($atributos as $atributo): ?>
                <section class="criar-ficha-secao">
                <label class="criar-ficha-elemento ficha-T20-atributos-text"><?= $atributo?>:</label>
                <div class="criar-ficha-input-atributos">
                    <input class="ficha-T20-cabecalho-textbox criar-ficha-elemento criar-ficha-atributo-input" type="number" name="atributos[<?= $atributo ?>]" min="0" max="50" required onmousewheel placeholder="0"><br>
                </div>    
                </section>
            <?php endforeach; ?>
        </section>
            
        <section>
            <h2 class="menu__titulo">Classes</h2>
            <hr class="barra__divisora">
            
            <div id="classes-container"></div>
            <button type="button" onclick="adicionarClasse()" class="ficha-botao">Adicionar Classe</button>

            <script>
                const listaDeClasses = <?= json_encode($classes)?>;
                let contadorClasse = 0;
            function adicionarClasse() {
                const container = document.getElementById("classes-container");
                const index = contadorClasse++;

                const html = `
                    <fielset class = "bloco-classe">
                        <legend class="criar-ficha-elemento ficha-T20-atributos-text">Classe ${index + 1}</legend>
                        <select class="ficha-T20-cabecalho-select criar-ficha-elemento classe-select" name="classes[${index}][id]"  onchange="mostrarCampoNovaClasse(this)">
                        <option value="">Selecione uma classe</option>
                        ${listaDeClasses.map(c => `<option value="${c.id}" data-descricao="${c.descricao.replace(/"/g, '&quot;')}">${c.nome}</option>`).join('')}
                        <option value="nova">Outra (digite abaixo)</option>
                        </select>

                        <div class="descricao-classe" style="margin-top: 5px; font-style: italic; color: #555;"></div>

                        <div class="campos-nova-classe" style="display: none;">
                            <input type="text" class="criar-ficha-elemento criar-ficha-input" name="classes[${index}][nova_classe]" placeholder="Nova classe"><br>
                            <textarea class="criar-ficha-elemento criar-ficha-input" name="classes[${index}][nova_classe_descricao]" placeholder="Descrição nova classe"></textarea><br>
                            <input type="text" class="criar-ficha-elemento criar-ficha-input" name="classes[${index}][nova_classe_dado_vida]" id="" placeholder="PV p/ Nível Ex: (5)"><br>
                            <input type="text" class="criar-ficha-elemento criar-ficha-input" name="classes[${index}][pm_por_nivel]" placeholder="PM p/ Nível Ex: (3)"><br>
                            </div>
                            <label class="ficha-T20-atributos-text"><input type="checkbox" class="criar-ficha-input[]" name="classes[${index}][inicial]"> Classe Inicial</label><br>
                    </fieldset>
                `;

                container.insertAdjacentHTML('beforeend', html);
            }
            </script>

        </section>
        
        <section>
            <h2 class="menu__titulo">Perícias</h2>
            <hr class="barra__divisora">
            <?php foreach ($pericias as $pericia): ?>
                <section>
                    <label class="ficha-T20-atributos-text">
                        <input class="criar-ficha-pericias-individual" type="checkbox" name="pericias[]" value="<?= $pericia ?>">
                        <?= htmlspecialchars($pericia) ?>
                    </label><br>
                </section>
            <?php endforeach; ?>
        </section>

        <section class="bloco-poderes">
            <h2 class="menu__titulo">Poderes</h2>
            <hr class="barra__divisora">

            <div id="poderes-container"></div>
            <button type="button" onclick="adicionarPoder()" class="ficha-botao">Adicionar Poder</button>
            
            <script>
                let contadorPoder = 0;
                const listaDePoderes = <?= json_encode($poderes) ?>;
            function adicionarPoder() {
                const container = document.getElementById("poderes-container");
                const index = contadorPoder++;

                let options = '<option value="">Selecione um poder</option>';
                listaDePoderes.forEach(p => {
                    options += `<option value="${p.id}" data-descricao="${p.descricao.replace(/"/g, '&quot;')}">${p.nome}</option>`;
                });
                options += '<option value="nova">Outra (digite abaixo)</option>';

                const html = `
                    <fieldset class="bloco-poder" style="margin-bottom: 10px;">
                        <legend class="criar-ficha-elemento ficha-T20-atributos-text">Poder ${index + 1}</legend>

                        <select class="ficha-T20-cabecalho-select criar-ficha-elemento poder-select" name="poderes[${index}][id]" onchange="exibirDescricaoPoder(this)">
                            ${options}
                        </select>

                        <div class="descricao-poder" style="margin-top: 5px; font-style: italic; color: #555;"></div>

                        <div class="campos-novo-poder" style="display: none;">
                            <input type="text" class="criar-ficha-elemento criar-ficha-input" name="poderes[${index}][novo_poder]" placeholder="Novo poder"><br>
                            <textarea class="criar-ficha-elemento criar-ficha-input" name="poderes[${index}][novo_poder_descricao]" placeholder="Descrição do novo poder"></textarea><br>
                        </div>
                    </fieldset>
                `;

                container.insertAdjacentHTML('beforeend', html);
            }
            </script>
        </section>

        <section>
            <h2 class="menu__titulo">Equipamentos</h2>
            <hr class="barra__divisora">

            <div id="equipamentos-container"></div>
            <button type="button" onclick="adicionarEquipamento()" class="ficha-botao">Adicionar Equipamento</button>

            <script>
                let contadorEquip = 0;
                const listaDeEquipamentos = <?= json_encode($equipamentos) ?>;
                function adicionarEquipamento() {
                    const container = document.getElementById("equipamentos-container");
                    const index = contadorEquip++;

                    // Gera opções com nome e descrição no data-atributo
                    let options = '<option value="">Selecione um equipamento</option>';
                    listaDeEquipamentos.forEach(equip => {
                        options += `<option 
                            value="${equip.id}" 
                            data-descricao="${equip.descricao.replace(/"/g, '&quot;')}" 
                            data-tipo="${equip.tipo}">
                            ${equip.nome}
                        </option>`;
                    });

                    options += '<option value="novo">Outro (digite abaixo)</option>';

                    const html = `
                        <fieldset class="bloco-equipamento" style="margin-bottom: 10px;">
                            <legend class= "criar-ficha-elemento ficha-T20-atributos-text">Equipamento ${index + 1}</legend>

                            <select class="ficha-T20-cabecalho-select criar-ficha-elemento" name="equipamentos[${index}][id]" onchange="exibirDescricaoEquipamento(this)">
                                ${options}
                            </select>

                            <div class="descricao-equipamento" style="margin-top: 5px; font-style: italic; color: #555;"></div>

                            <div class="campos-novo-equipamento" style="display: none;">
                                <input type="text" class="criar-ficha-elemento criar-ficha-input" name="equipamentos[${index}][novo_equipamento_nome]" placeholder="Nome"><br>
                                <input type="text" class="criar-ficha-elemento criar-ficha-input" name="equipamentos[${index}][novo_equipamento_tipo]" placeholder="Tipo (Arma, Armadura, Item)"><br>
                                <textarea class="criar-ficha-elemento criar-ficha-input" name="equipamentos[${index}][novo_equipamento_descricao]" placeholder="Descrição"></textarea><br>
                            </div>
                        </fieldset>
                    `;

                    container.insertAdjacentHTML('beforeend', html);
                }

            </script>
        </section>

        <section class="bloco-magias">
            <h2 class="menu__titulo">Magias</h2>
            <hr class="barra__divisora">

            <div id="magias-container"></div>
            <button type="button" onclick="adicionarMagia()" class="ficha-botao">Adicionar Magia</button>
            
            <script>
                let contadorMagias = 0;
                const listaDeMagias = <?= json_encode($magias) ?>;
            function adicionarMagia() {
                const container = document.getElementById("magias-container");
                const index = contadorMagias++;

                let options = '<option value="">Selecione uma Magia</option>';
                listaDeMagias.forEach(m => {
                    options += `<option value="${m.id}" 
                    data-nivel = "${m.nivel}"
                    data-escola = "${m.escola}"
                    data-descricao="${m.descricao.replace(/"/g, '&quot;')}"
                    >
                    ${m.nome}
                    </option>`;
                });
                options += '<option value="nova">Outra (digite abaixo)</option>';

                const html = `
                    <fieldset class="bloco-magias" style="margin-bottom: 10px;">
                        <legend class="criar-ficha-elemento ficha-T20-atributos-text">Magia ${index + 1}</legend>

                        <select class="ficha-T20-cabecalho-select criar-ficha-elemento magia-select" name="magias[${index}][id]" onchange="exibirDescricaoMagia(this)">
                            ${options}
                        </select>

                        <div class="descricao-magia" style="margin-top: 5px; font-style: italic; color: #555;"></div>

                        <div class="campos-nova-magia" style="display: none;">
                            <input type="text" class="criar-ficha-elemento criar-ficha-input" name="magias[${index}][nova_magia]" placeholder="Nova Magia"><br>
                            <input type="number" class="criar-ficha-elemento criar-ficha-input" name="magias[${index}][nova_magia_nivel]" placeholder="Nivel da Magia" min=0 max=9><br>
                            <input type="text" class="criar-ficha-elemento criar-ficha-input" name="magias[${index}][nova_magia_escola]" placeholder="Escola da Magia"><br>
                            <textarea class="criar-ficha-elemento criar-ficha-input" name="magias[${index}][nova_magia_descricao]" placeholder="Descrição da nova Magia"></textarea><br>
                        </div>
                    </fieldset>
                `;

                container.insertAdjacentHTML('beforeend', html);
            }
            </script>
        </section>


            <hr class="barra__divisora">
            <button type="submit" class="ficha-botao" name="enviar">Criar Ficha</button>
        </form>
        
</body>
<hr class="barra__divisora">
<footer class="rodape">
    <span class="rodape__texto">Desenvolvido por Vinicius Nogueira Martins</span>
</footer>
<script>

    function exibirDescricaoPoder(selectElement) {
    const descricaoDiv = selectElement.closest('.bloco-poder').querySelector('.descricao-poder');
    const valor = selectElement.value;

    if (valor === 'nova') {
        selectElement.closest('.bloco-poder').querySelector('.campos-novo-poder').style.display = 'block';
        descricaoDiv.textContent = '';
        return;
    } else {
        selectElement.closest('.bloco-poder').querySelector('.campos-novo-poder').style.display = 'none';
    }

    const selecionado = selectElement.selectedOptions[0];
    const descricao = selecionado.getAttribute('data-descricao');
    descricaoDiv.textContent = descricao || '';
    }

    function exibirDescricaoEquipamento(selectElement) {
        const bloco = selectElement.closest('.bloco-equipamento');
        const descricaoDiv = bloco.querySelector('.descricao-equipamento');
        const camposNovo = bloco.querySelector('.campos-novo-equipamento');

        if (selectElement.value === 'novo') {
            descricaoDiv.textContent = '';
            camposNovo.style.display = 'block';
            return;
        }

        camposNovo.style.display = 'none';

        const selecionado = selectElement.selectedOptions[0];
        const descricao = selecionado.getAttribute('data-descricao');
        const tipo = selecionado.getAttribute('data-tipo');

        descricaoDiv.innerHTML = `<strong>Tipo:</strong> ${tipo}<br><strong>Descrição:</strong> ${descricao}`;
    }

    function exibirDescricaoMagia(selectElement) {
        const bloco = selectElement.closest('.bloco-magias');
        const descricaoDiv = bloco.querySelector('.descricao-magia');
        const camposNovo = bloco.querySelector('.campos-nova-magia');

        if (selectElement.value === 'nova') {
            descricaoDiv.textContent = '';
            camposNovo.style.display = 'block';
            return;
        }

        camposNovo.style.display = 'none';

        const selecionado = selectElement.selectedOptions[0];
        const descricao = selecionado.getAttribute('data-descricao');
        const nivel = selecionado.getAttribute('data-nivel');
        const escola = selecionado.getAttribute('data-escola');

        descricaoDiv.innerHTML = `<strong>Nível:</strong> ${nivel}<br><strong>Escola:</strong> ${escola}<br><strong>Descrição:</strong> ${descricao}`;
    }


    function mostrarCampoNovaRaca() {
        var select = document.getElementById("raca-select");
        var input = document.getElementById("nova-raca");
        var input_desc = document.getElementById("nova-raca-descricao");
        input.style.display = (select.value === "nova") ? "block" : "none";
        input_desc.style.display = (select.value === "nova") ? "block" : "none";
    }

    function mostrarCampoNovaOrigem() {
        var select = document.getElementById("origem-select");
        var input = document.getElementById("nova-origem");
        var input_desc = document.getElementById("nova-origem-descricao");
        input.style.display = (select.value === "nova") ? "block" : "none";
        input_desc.style.display = (select.value === "nova") ? "block" : "none";
    }

    function mostrarCampoNovaClasse(selectElement) {
        const blocoClasse = selectElement.closest('.bloco-classe');
        const camposNovaClasse = blocoClasse.querySelector('.campos-nova-classe');
        const descricaoDiv = blocoClasse.querySelector('.descricao-classe');

         if (selectElement.value === 'nova') {
            camposNovaClasse.style.display = 'block';
            descricaoDiv.textContent ='';
            return;
        } else {
            camposNovaClasse.style.display = 'none';
    
            const selectedOption = selectElement.selectedOptions[0];
            const descricao = selectedOption.getAttribute('data-descricao');
            descricaoDiv.textContent = descricao || '';
        }
            
    }
</script>