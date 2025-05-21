<?php

    require "src\conecction.php";
    require "src\model\classe.php";
    require "src\model\usuario.php";
    require "src\\repository\usuarioRepository.php";

    $pdo = conectar();

    if (isset($_POST['login']))
    {
        $usuario = new Usuario(null,
            "",
            $_POST['login__email'],
            $_POST['login__senha']
        );

        $usuarioRepository = new usuarioRepository($pdo);
        $resultado = $usuarioRepository->checarLogin($usuario);

        if ($resultado == true) {

            session_start();
            $_SESSION['id_usuario'] = $usuarioRepository->buscarID($usuario);
            header("Location: pages\Menu.php");
        }

    };


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
    <hr/>
    <section class="login">
        <h2 class="login__titulo"> Por Favor faça o Login:</h2>


        <form method="post">
            <div class="login__conjunto">
                <label class="login__texto" for="login__email">Email: </label>
                <input type="email" name="login__email" id="login__email" class="login__textbox">
            </div>
    
            <div class="login__conjunto">
                <label class="login__texto" for="login__senha">Senha: </label>
                <input type="password" name="login__senha" id="login__senha" class="login__textbox">
            </div>
            <div class="login__conjunto">
                <input name="login" type="submit" class="login__botao" value="Entrar">
            </div>
    
            <div class="login__conjunto">
                <p class="login__texto">Não possui um login? <a href="../pages/Cadastro.php" class="login__link">Cadastre-se aqui.</a></p>
            </div>
        </form>
        <p>
            <?php
                if (isset($resultado)) {
                    if($resultado == false){
                        "Email ou Senha incorretos";
                    } 
                }            
            ?>
        </p>
    </section>
    <hr class="barra__divisora">
    <footer class="rodape">
        <span class="rodape__texto">Desenvolvido por Vinicius Nogueira Martins</span>
    </footer>
</body>


</html>