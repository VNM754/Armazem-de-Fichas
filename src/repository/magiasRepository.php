<?php

    class magiasRepository
    {
        private PDO $pdo;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }


        public function buscarMagiasPorSistema($sistema_id, $pdo) {
            $sqlMagias = "SELECT * FROM magias 
            WHERE sistema_id = :sistema_id 
            ORDER BY nome";
            $statementMagias = $pdo->prepare($sqlMagias);
            $statementMagias->bindValue(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $statementMagias->execute();
            $magias = $statementMagias->fetchAll(PDO::FETCH_ASSOC);

            return $magias;
            
        }
        
        public function buscarMagiasDaFicha($ficha_id, $pdo) 
        {
            $sqlMagias = "SELECT magia_id FROM ficha_magias
            WHERE ficha_id = :ficha_id";
            $statementMagias = $pdo->prepare($sqlMagias);
            $statementMagias->bindValue(':ficha_id',$ficha_id,PDO::PARAM_INT);
            $statementMagias->execute();
            $magias = $statementMagias->fetchAll(PDO::FETCH_COLUMN);

            if (!$magias) {
                return [];
            }


            $placeholders = implode(',', array_fill(0, count($magias), '?'));
            $sqlDetalhes = "SELECT * FROM magias WHERE id IN ($placeholders)";
            $statementDetalhes = $pdo->prepare($sqlDetalhes);
            $statementDetalhes->execute(array_values($magias));

            return $statementDetalhes->fetchAll(PDO::FETCH_ASSOC);

        }

        public function adicionarMagia($sistema_id, $nome, $nivel, $escola, $descricao, $pdo)
        {
            $verificaSql = "SELECT id FROM magias WHERE sistema_id = :sistema_id AND nome = :nome";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $verificaStatement->execute();
            $magiaExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($magiaExistente) {
                return $magiaExistente['id'];
            }


            $sql = "INSERT INTO magias (sistema_id, nome, nivel, escola, descricao) 
            VALUES (:sistema_id, :nome, :nivel, :escola, :descricao)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $statement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement->bindParam(':nivel', $nivel, PDO::PARAM_INT);
            $statement->bindParam(':escola', $escola, PDO::PARAM_STR);
            $statement->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            
            if ($statement->execute()) {
                    $novamagia_id = $pdo->lastInsertId();
                    return $novamagia_id;
                } else return false;
        }

        public function adicionarMagiaFicha($magia_id, $ficha_id, $pdo) {
            $verificaSql = "SELECT id FROM ficha_magias WHERE ficha_id = :ficha_id AND magia_id = :magia_id";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':magia_id', $magia_id, PDO::PARAM_INT);
            $verificaStatement->execute();
            $magiaExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($magiaExistente) {
                return true;
            }

            $sql = "INSERT INTO ficha_magias (ficha_id, magia_id) 
            VALUES (:ficha_id, :magia_id)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $statement->bindParam(':magia_id', $magia_id, PDO::PARAM_INT);
            
            if ($statement->execute()) {
                    return true;
                } else return false;
        }

    }