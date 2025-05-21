<?php
// Exemplo: busca raças e origens do banco
// Conecte ao seu banco e substitua as queries abaixo
require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";
    require "../src/repository/classeRepository.php";

    session_start();
    $pdo = conectar();
    $id_usuario = $_SESSION['id_usuario'];
    $id_sistema = $_GET['id_sistema'];
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($id_usuario);
    
    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);


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
        <input class="ficha-T20-cabecalho-textbox criar-ficha-elemento" type="text" name="nome-personagem" required><br>
        
        <h2 class="menu__titulo">Imagem:</h2>
        <hr class="barra__divisora">
        <input class="criar-ficha-imagem-input" type="file" name="imagem"><br>
        
        <h2 class="menu__titulo">Raça:</h2>
        <hr class="barra__divisora">
        <select class="ficha-T20-cabecalho-select criar-ficha-elemento" name="raca_personagem" id="raca-select" onchange="mostrarCampoNovaRaca()">
            <?php foreach ($racas as $raca): ?>
                <option name="raca-personagem" value="<?= $raca['id'] ?>"><?= htmlspecialchars($raca['nome']) ?></option>
            <?php endforeach; ?>
                <option value="nova">Outra (digite abaixo)</option>
            </select>
            <input type="text" name="nova_raca" id="nova-raca" placeholder="Nova raça" style="display: none;">
            <input type="text" name="nova_raca_descricao" id="nova-raca-descricao" placeholder="Descrição nova raça" style="display: none;"><br>
            
            <h2 class="menu__titulo">Origem:</h2>
            <hr class="barra__divisora">
    <select class="ficha-T20-cabecalho-select criar-ficha-elemento" name="origem_personagem" id="origem-select" onchange="mostrarCampoNovaOrigem()">
        <?php foreach ($origens as $origem): ?>
            <option name="origem-personagem" value="<?= $origem['id'] ?>"><?= htmlspecialchars($origem['nome']) ?></option>
            <?php endforeach; ?>
            <option value="nova">Outra (digite abaixo)</option>
        </select>
        <input type="text" name="nova_origem" id="nova-origem" placeholder="Nova origem" style="display: none;">
        <input type="text" name="nova_origem_descricao" id="nova-origem-descricao" placeholder="Descrição nova origem" style="display: none;"><br>
        
        <section class="criar-ficha-Atributos">
            <h2 class="menu__titulo">Atributos</h2>
            <hr class="barra__divisora">
            <?php foreach ($atributos as $atributo): ?>
                <section class="criar-ficha-secao">
                <label class="criar-ficha-elemento ficha-T20-atributos-text"><?= $atributo?>:</label>
                <div class="criar-ficha-input-atributos">
                    <input class="ficha-T20-cabecalho-textbox criar-ficha-elemento criar-ficha-atributo-input" type="number" name="atributos[<?= $atributo ?>]" min="1" max="30" required><br>
                </div>    
                </section>
            <?php endforeach; ?>
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
                    <?php endforeach; ?>
                </section>
        </section>

            <hr class="barra__divisora">
            <button type="submit" class="ficha-botao">Criar Ficha</button>
        </form>
        
</body>
<hr class="barra__divisora">
<footer class="rodape">
    <span class="rodape__texto">Desenvolvido por Vinicius Nogueira Martins</span>
</footer>
<script>
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
</script>