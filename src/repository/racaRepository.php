<?php

    class racaRepository
    {
        private PDO $pdo;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        public function adicionarRaca($sistema_id, $nome, $descricao, $pdo)
        {
            $sqlVerifica = "SELECT id FROM racas WHERE sistema_id = :sistema_id AND nome = :nome";
            $statementVerifica = $pdo->prepare($sqlVerifica);
            $statementVerifica->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $statementVerifica->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statementVerifica->execute();
            
            $racaExistente = $statementVerifica->fetch(PDO::FETCH_ASSOC);

            if ($racaExistente) {
                return $racaExistente['id'];
            }

                $sqlRaca = "INSERT INTO racas (sistema_id, nome, descricao) 
                VALUES (:sistema_id, :nome, :descricao)";
                $statementInsere = $pdo->prepare($sqlRaca);
                $statementInsere->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
                $statementInsere->bindParam(':nome', $nome, PDO::PARAM_STR);
                $statementInsere->bindParam(':descricao', $descricao, PDO::PARAM_STR);

                if ($statementInsere->execute()) {
                    $novaraca_id = $pdo->lastInsertId();
                    return $novaraca_id;
                } else return false;
        } 
    }