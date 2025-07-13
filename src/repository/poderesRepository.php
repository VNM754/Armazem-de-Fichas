<?php

    class poderesRepository
    {
        private PDO $pdo;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }


        public function buscarPoderesPorSistema($sistema_id, $pdo) {
            $sqlPoderes = "SELECT * FROM poderes 
            WHERE sistema_id = :sistema_id 
            AND id NOT IN (
                SELECT poder_id FROM raca_poderes
                UNION
                SELECT poder_id FROM classe_poderes
            )
            ORDER BY nome";
            $statementPoderes = $pdo->prepare($sqlPoderes);
            $statementPoderes->bindValue(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $statementPoderes->execute();
            $poderes = $statementPoderes->fetchAll(PDO::FETCH_ASSOC);

            return $poderes;
            
        }
        
        public function adicionarPoder ($sistema_id, $nome, $descricao, $pdo)
        {
            $verificaSql = "SELECT id FROM poderes WHERE sistema_id = :sistema_id AND nome = :nome";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $verificaStatement->execute();
            $poderExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($poderExistente) {
                return $poderExistente['id'];
            }


            $sql = "INSERT INTO poderes (sistema_id, nome, descricao) 
            VALUES (:sistema_id, :nome, :descricao)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
            $statement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            
            if ($statement->execute()) {
                    $novopoder_id = $pdo->lastInsertId();
                    return $novopoder_id;
                } else return false;
        }

        public function adicionarPoderFicha($poder_id, $ficha_id, $pdo) {
            $verificaSql = "SELECT id FROM ficha_poderes WHERE ficha_id = :ficha_id AND poder_id = :poder_id";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':poder_id', $poder_id, PDO::PARAM_INT);
            $verificaStatement->execute();
            $poderExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($poderExistente) {
                return true;
            }

            $sql = "INSERT INTO ficha_poderes (ficha_id, poder_id) 
            VALUES (:ficha_id, :poder_id)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $statement->bindParam(':poder_id', $poder_id, PDO::PARAM_INT);
            
            if ($statement->execute()) {
                    return true;
                } else return false;
        }
        
        public function adicionarPoderRaca($poder_id, $ficha_id, $pdo) {
            $verificaSql = "SELECT id FROM ficha_poderes WHERE ficha_id = :ficha_id AND poder_id = :poder_id";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':poder_id', $poder_id, PDO::PARAM_INT);
            $verificaStatement->execute();
            $poderExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($poderExistente) {
                return true;
            }

            $sql = "INSERT INTO ficha_poderes (ficha_id, poder_id) 
            VALUES (:ficha_id, :poder_id)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $statement->bindParam(':poder_id', $poder_id, PDO::PARAM_INT);
            
            if ($statement->execute()) {
                    return true;
                } else return false;
        }

        public function adicionarPoderClasse($poder_id, $ficha_id, $pdo) {
            $verificaSql = "SELECT id FROM ficha_poderes WHERE ficha_id = :ficha_id AND poder_id = :poder_id";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':poder_id', $poder_id, PDO::PARAM_INT);
            $verificaStatement->execute();
            $poderExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($poderExistente) {
                return true;
            }

            $sql = "INSERT INTO ficha_poderes (ficha_id, poder_id) 
            VALUES (:ficha_id, :poder_id)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $statement->bindParam(':poder_id', $poder_id, PDO::PARAM_INT);
            
            if ($statement->execute()) {
                    return true;
                } else return false;
        }

        public function buscarPoderesDaFicha($ficha_id, $pdo) 
        {
            $sqlPoderes = "SELECT poder_id FROM ficha_poderes
            WHERE ficha_id = :ficha_id";
            $statementPoderes = $pdo->prepare($sqlPoderes);
            $statementPoderes->bindValue(':ficha_id',$ficha_id,PDO::PARAM_INT);
            $statementPoderes->execute();
            $poderes = $statementPoderes->fetchAll(PDO::FETCH_COLUMN);

            if (!$poderes) {
                return [];
            }


            $placeholders = implode(',', array_fill(0, count($poderes), '?'));
            $sqlDetalhes = "SELECT * FROM poderes WHERE id IN ($placeholders)";
            $statementDetalhes = $pdo->prepare($sqlDetalhes);
            $statementDetalhes->execute(array_values($poderes));

            return $statementDetalhes->fetchAll(PDO::FETCH_ASSOC);

        }

        public function buscarPoderesDaClasse($classe_id, $pdo) 
        {
            $sqlPoderes = "SELECT poder_id FROM classe_poderes
            WHERE classe_ou_kit_id = :classe_id";
            $statementPoderes = $pdo->prepare($sqlPoderes);
            $statementPoderes->bindValue(':classe_id',$classe_id,PDO::PARAM_INT);
            $statementPoderes->execute();
            $poderes = $statementPoderes->fetchAll(PDO::FETCH_COLUMN);

            if (!$poderes) {
                return [];
            }


            $placeholders = implode(',', array_fill(0, count($poderes), '?'));
            $sqlDetalhes = "SELECT * FROM poderes WHERE id IN ($placeholders)";
            $statementDetalhes = $pdo->prepare($sqlDetalhes);
            $statementDetalhes->execute(array_values($poderes));

            return $statementDetalhes->fetchAll(PDO::FETCH_ASSOC);

        }

        public function buscarPoderesDaRaca($raca_id, $pdo) 
        {
            $sqlPoderes = "SELECT poder_id FROM raca_poderes
            WHERE raca_id = :raca_id";
            $statementPoderes = $pdo->prepare($sqlPoderes);
            $statementPoderes->bindValue(':raca_id',$raca_id,PDO::PARAM_INT);
            $statementPoderes->execute();
            $poderes = $statementPoderes->fetchAll(PDO::FETCH_COLUMN);

            if (!$poderes) {
                return [];
            }


            $placeholders = implode(',', array_fill(0, count($poderes), '?'));
            $sqlDetalhes = "SELECT * FROM poderes WHERE id IN ($placeholders)";
            $statementDetalhes = $pdo->prepare($sqlDetalhes);
            $statementDetalhes->execute(array_values($poderes));

            return $statementDetalhes->fetchAll(PDO::FETCH_ASSOC);

        }

    }