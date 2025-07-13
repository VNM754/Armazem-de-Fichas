<?php

    class equipamentoRepository
    {
        private PDO $pdo;

        public function __construct(PDO $pdo)
        {
            $this->pdo = $pdo;
        }

        public function buscarEquipamentos($pdo) {
            $sqlEquipamentos = "SELECT * FROM equipamentos
            ORDER BY tipo";
            $statementEquipamentos = $pdo->prepare($sqlEquipamentos);
            $statementEquipamentos->execute();
            $equipamentos = $statementEquipamentos->fetchAll(PDO::FETCH_ASSOC);

            return $equipamentos;
            
        } 

        public function buscarEquipamentosPorTipo($tipo, $pdo) {
            $sqlEquipamentos = "SELECT * FROM equipamentos 
            WHERE tipo = :tipo
            ORDER BY nome";
            $statementEquipamentos = $pdo->prepare($sqlEquipamentos);
            $statementEquipamentos->bindValue(':tipo', $tipo, PDO::PARAM_STR);
            $statementEquipamentos->execute();
            $equipamentos = $statementEquipamentos->fetchAll(PDO::FETCH_ASSOC);

            return $equipamentos;
            
        }

        public function buscarEquipamentosDaFicha($ficha_id, $pdo) 
        {
            $sqlEquipamentos = "SELECT equipamento_id FROM ficha_equipamentos
            WHERE ficha_id = :ficha_id";
            $statementEquipamentos = $pdo->prepare($sqlEquipamentos);
            $statementEquipamentos->bindValue(':ficha_id',$ficha_id,PDO::PARAM_INT);
            $statementEquipamentos->execute();
            $equipamentos = $statementEquipamentos->fetchAll(PDO::FETCH_COLUMN);

            if (!$equipamentos) {
                return [];
            }


            $placeholders = implode(',', array_fill(0, count($equipamentos), '?'));
            $sqlDetalhes = "SELECT * FROM equipamentos WHERE id IN ($placeholders)";
            $statementDetalhes = $pdo->prepare($sqlDetalhes);
            $statementDetalhes->execute(array_values($equipamentos));

            return $statementDetalhes->fetchAll(PDO::FETCH_ASSOC);

        }

        public function adicionarEquipamento ($tipo, $nome, $descricao, $pdo)
        {

            $tipo = strtolower($tipo);
            $verificaSql = "SELECT id FROM equipamentos WHERE tipo = :tipo AND nome = :nome";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $verificaStatement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $verificaStatement->execute();
            $poderExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($poderExistente) {
                return $poderExistente['id'];
            }


            $sql = "INSERT INTO equipamentos (tipo, nome, descricao) 
            VALUES (:tipo, :nome, :descricao)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $statement->bindParam(':nome', $nome, PDO::PARAM_STR);
            $statement->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            
            if ($statement->execute()) {
                    $novopoder_id = $pdo->lastInsertId();
                    return $novopoder_id;
                } else return false;
        }

        public function adicionarEquipamentoFicha($equipamento_id, $ficha_id, $pdo) {
            $verificaSql = "SELECT id FROM ficha_equipamentos WHERE ficha_id = :ficha_id AND equipamento_id = :equipamento_id";
            $verificaStatement = $pdo->prepare($verificaSql);
            $verificaStatement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $verificaStatement->bindParam(':equipamento_id', $equipamento_id, PDO::PARAM_INT);
            $verificaStatement->execute();
            $equipamentoExistente = $verificaStatement->fetch(PDO::FETCH_ASSOC);

            if ($equipamentoExistente) {
                return true;
            }

            $sql = "INSERT INTO ficha_equipamentos (ficha_id, equipamento_id) 
            VALUES (:ficha_id, :equipamento_id)";
            $statement = $pdo->prepare($sql);
            $statement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
            $statement->bindParam(':equipamento_id', $equipamento_id, PDO::PARAM_INT);
            
            if ($statement->execute()) {
                    return true;
                } else return false;
        }

    }