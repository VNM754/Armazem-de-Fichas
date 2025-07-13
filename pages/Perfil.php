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
?>


<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../reset.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/0955cef844.js" crossorigin="anonymous"></script>
</head>


<body class="login__body">
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
                  <a href="../pages/Menu.php" class="lista-menu__link">Voltar ao Menu</a>
             </li>
             <li class="lista-menu__item">
                <a href="../pages/Deslogar.php" class="lista-menu__link">Logout</a>
             </li>
        </ul>
    </header>
    <hr class="barra__divisora">
    <hr/>
    <section class="login">
        <h2 class="login__titulo"> Alterar Informações do Usuário:</h2>


        <form action="..\pages\AtualizarPerfil.php" method="post">
            <div class="login__conjunto">
                <label class="login__texto" for="login__nome">Nome: </label>
                <input type="text" name="login__nome" id="login__nome" class="login__textbox" required>
            </div>

            <div class="login__conjunto">
                <label class="login__texto" for="login__email">Email: </label>
                <input type="email" name="login__email" id="login__email" class="login__textbox">
            </div>
    
            <div class="login__conjunto">
                <label class="login__texto" for="login__senha">Senha: </label>
                <input type="password" name="login__senha" id="login__senha" class="login__textbox">
            </div>
            <div class="login__conjunto">
                <input name="update" type="submit" class="login__botao" value="Editar">
            </div>
        </form>
    </section>
    <hr class="barra__divisora">
    <footer class="rodape">
        <span class="rodape__texto">Desenvolvido por Vinicius Nogueira Martins</span>
    </footer>
</body>


</html>