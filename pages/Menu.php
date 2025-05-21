<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";
    require "../src/model/ficha.php";
    require "../src/repository/fichaRepository.php";

    session_start();
    $pdo = conectar();
    $id_usuario = $_SESSION['id_usuario'];
    $id_sistema = isset($_GET['id_sistema']) ? $_GET['id_sistema'] : null;

    $usuarioRepository = new usuarioRepository($pdo);
    $usuario = $usuarioRepository->buscarPorID($_SESSION['id_usuario']);
    $fichaRepository = new fichaRepository($pdo);
    $fichas = $fichaRepository->buscarTodos($id_usuario);
    $fichasRecentes = $fichaRepository->buscarRecentes($id_usuario);

    if ($id_sistema) {
        $fichas = $fichaRepository->buscarPorUsuarioComFiltro($id_usuario, $id_sistema);
    } else {
        $fichas = $fichaRepository->buscarTodos($id_usuario);
    };
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XXX - Menu</title>
    <link rel="stylesheet" href="../reset.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/0955cef844.js" crossorigin="anonymous"></script>
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
                  <a href="../pages/Perfil.php" class="lista-menu__link">Editar Perfil</a>
             </li>
             <li class="lista-menu__item">
                <a href="../pages/Deslogar.php" class="lista-menu__link">Logout</a>
             </li>
        </ul>
    </header>
    <hr class="barra__divisora">
    
    <section class="carrossel">
        <h2 class="carrossel__titulo">Acessados Recentemente</h2>
        <hr class="barra__divisora">
        <!-- Slider main container -->
        <div class="swiper">
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
            <!-- Additional required wrapper -->
            <div class="swiper-wrapper">
            <!-- Slides -->
            <?php foreach ($fichasRecentes as $ficha): ?>
                    <div class="swiper-slide">
                        <a href="<?php echo "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha->getId()) . ".php?id_ficha=" . $ficha->getId() ?>">
                            <img src="<?php echo "../" . $ficha->getImagemDiretorio()?>" alt="<?="Ficha " . $ficha->getNome()?>" class="swiper-imagem">
                        </a>
                    </div>
            <?php endforeach;?>
            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </section>
    <hr class="barra__divisora">

    <section class="menu__botoes">
        <input type="checkbox" id="sistemas__filtro" class="menu-botoes__filtro">
        <label for="sistemas__filtro">
            <h2 class="menu__titulo">
                Filtras Fichas por Sistema:
            </h2>
        </label>
        <ul class="lista-sistemas">
            <li class="lista-menu__item">
                <a href="\pages\Menu.php?id_sistema=2" class="lista-menu__link">
                    Tormenta 20
                </a>
            </li>
            <li class="lista-menu__item">
                <a href="\pages\Menu.php?id_sistema=3" class="lista-menu__link">
                    3D&T Victory
                </a>
            </li>
            <li class="lista-menu__item">
                <a href="\pages\Menu.php?id_sistema=4" class="lista-menu__link">
                    Ordem Paranormal
                </a>
            </li>
            <li class="lista-menu__item">
                <a href="\pages\Menu.php?id_sistema=1" class="lista-menu__link">
                    Dungeons & Dragons 5e
                </a>
            </li>
            <li class="lista-menu__item">
                <a href="\pages\Menu.php" class="lista-menu__link">
                    Todas
                </a>
            </li>
        </ul>

        <hr class="barra__divisora">

        <input type="checkbox" id="criar-fichas__sistemas" class="menu-botoes__criarFichas">
        <label for="criar-fichas__sistemas">
            <h2 class="menu__titulo">
                Criar Nova Ficha:
            </h2>
        </label>
            <ul class="lista-criarFichas">
                <li class="lista-menu__item">
                    <a href="\pages\CriarFicha.php?id_sistema=2" class="lista-menu__link">
                        Tormenta 20
                    </a>
                </li>
                <li class="lista-menu__item">
                    <a href="\pages\CriarFicha.php?id_sistema=3" class="lista-menu__link">
                        3D&T Victory
                    </a>
                </li>
                <li class="lista-menu__item">
                    <a href="\pages\CriarFicha.php?id_sistema=4" class="lista-menu__link">
                        Ordem Paranormal
                    </a>
                </li>
                <li class="lista-menu__item">
                    <a href="\pages\CriarFicha.php?id_sistema=1" class="lista-menu__link">
                        Dungeons & Dragons 5e
                    </a>
                </li>
            </ul>

    </section>
    <hr class="barra__divisora">

    <section class="menu__fichas">
        <h2 class="menu__titulo">Fichas Existentes</h2>
        <hr class="barra__divisora">
        <?php foreach ($fichas as $ficha): ?>
            <div class="menu-fichas__item">
                <a href="<?php echo "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha->getId()) . ".php?id_ficha=" . $ficha->getId() ?>" class="menu-fichas__link">
                    <img src="<?php echo "../" . $ficha->getImagemDiretorio()?>" class="menu-fichas__icone">
                </a>
                <a href="<?php echo "../pages/Ficha" . $fichaRepository->buscarSistemaNome($ficha->getId()) . ".php?id_ficha=" . $ficha->getId() ?>" class="menu-fichas__link">
                    <p class="menu-fichas__titulo"><?= $ficha->getNome()?></p>
                </a>
                <a href="">
                    <i class="fa-solid fa-circle-xmark fa-xl menu-fichas__excluir" alt="Excluir Ficha"></i>
                </a>
            </div>
        <?php endforeach;?>
    </section>

    <hr class="barra__divisora">
    <footer class="rodape">
        <span class="rodape__texto">Desenvolvido por Vinicius Nogueira Martins</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper', {
            speed: 10,
            spaceBetween: 10,
            slidesPerView: 3,
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                type: 'bullets',
            },
        });
    </script>
</body>
</html>