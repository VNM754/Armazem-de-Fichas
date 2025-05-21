<?php

class usuarioRepository
{
    private PDO $pdo;


    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    private function formarObjeto($dados) 
    {
        return new Usuario($dados['id'],
            $dados['nome'],
            $dados['email'],
            $dados['senha_hash']
        );
    }

    public function checarLogin($usuario)
    {
        $sql = "SELECT * FROM usuarios";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dados as $dado) {
            if ($dado['email'] == $usuario->getEmail() && $dado['senha_hash'] == $usuario->getSenha()) {
                return true;
            }
        }
        return false;
    }

    public function buscarTodos()
    {
        $sql = "SELECT * FROM usuarios";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todosOsDados = array_map(function ($usuario){
            return $this->formarObjeto($usuario);
        },$dados);

        return $todosOsDados;
    }

    public function buscarPorID(int $id) 
    {
        $sql = "SELECT * FROM usuarios";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($dados as $dado) {
            if ($dado['id'] == $id) {
                return $dado;
            }
        }

        return $dados;
    }

    public function buscarID($usuario) 
    {   
        
        $sql = "SELECT * FROM usuarios";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dados as $dado) {
            if ($dado['email'] == $usuario->getEmail()) {
                return $dado['id'];
            }
        }
    }

    public function deletar (int $id)
    {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1,$id);
        $statement->execute();
    }


    public function salvar(Usuario $usuario) 
    {
        $sql = "INSERT INTO usuarios (id, nome, email, senha_hash) VALUES (?,?,?,?)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $usuario->getId());
        $statement->bindValue(2, $usuario->getNome());
        $statement->bindValue(3, $usuario->getEmail());
        $statement->bindValue(4, $usuario->getSenha());
        $statement->execute();

    }
}
