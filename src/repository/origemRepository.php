<?php

    class origemRepository
    {
        private PDO $pdo;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        public function adicionarOrigem ($sistema_id, $nome, $descricao, $pdo)
        {
            $verificaSql = "SELECT id FROM origens WHERE sistema_id = :sistema_id AND nome = :nome";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $verificaStatement->execute();
            $origemExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($origemExistente) {
                return $origemExistente['id'];
            }


            $sql = "INSERT INTO origens (sistema_id, nome, descricao) 
            VALUES (:sistema_id, :nome, :descricao)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $statement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            
            if ($statement->execute()) {
                    $novaorigem_id = $pdo->lastInsertId();
                    return $novaorigem_id;
                } else return false;
        }

    }