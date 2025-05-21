<?php

    require "../src/conecction.php";
    require "../src/model/usuario.php";
    require "../src/repository/usuarioRepository.php";

    $pdo = conectar();

    if (isset($_POST['cadastro'])) 
    {
        $usuario = new Usuario(null,
            $_POST['login__nome'],
            $_POST['login__email'],
            $_POST['login__senha']
        );

        $usuarioRepository = new usuarioRepository($pdo);
        $usuarioRepository->salvar($usuario);

        header("Location: /Login.php");
    }

?>


<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../reset.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="../style.css">
    <script src="https://kit.fontawesome.com/0955cef844.js" crossorigin="anonymous"></script>
</head>
<body class="login__body">
    <hr/>
    <section class="login">
        <h2 class="login__titulo">Por favor, preencha as informações abaixo para realizar o cadastro:</h2>
            <form method="post">
                
                <div class="login__conjunto ">
                    <label class="login__texto" for="login__nome">Nome:</label>
                    <input type="text" name="login__nome" id="login__nome" required>
                </div>

                <div class="login__conjunto">
                    <label class="login__texto" for="login__email">Email:</label>
                    <input type="email" name="login__email" id="login__email" required> <br>
                </div>

                <div class="login__conjunto">
                    <label class="login__texto" for="login__senha" form="">Senha:</label>
                    <input type="password" name="login__senha" id="login__senha" required><br>
                </div>
                <div class="cadastro__conjunto">
                    <input name="cadastro" class="cadastro__botao" type="submit" value="Cadastrar">
                </form>
                <a href="../Login.php">
                    <button class="cadastro__botao">Voltar</button>
                </a>
                </div>
    </section>
    <footer class="rodape">
        <span class="rodape__texto">Desenvolvido por Vinicius Nogueira Martins</span>
    </footer>
</body>
</html>