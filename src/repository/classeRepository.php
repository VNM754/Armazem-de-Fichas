<?php

class classRepository{

    private PDO $pdo;


    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    private function formarObjeto($dados) 
    {
        return new Classe($dados['id'],
            $dados['sistema_id'],
            $dados['nome'],
            $dados['descricao'],
            $dados['dado_vida']
        );
    }

    public function buscarClasse($id, $pdo) {
        $sql = "SELECT nome FROM classes_kits WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $atributos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $atributos;
    }

    public function buscarClassePorNome($nome, $sistema_id, $pdo)
    {
        $sql = "SELECT id FROM classes_kits WHERE sistema_id = :sistema_id AND nome = :nome";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
        $statement->bindParam(':nome', $nome, PDO::PARAM_STR);
        $statement->execute();
        $classe = $statement->fetchAll(PDO::FETCH_NUM);
        return $classe;
    }

    public function buscarClassesPorSistema($id, $pdo) {
        $sql = "SELECT * FROM classes_kits WHERE sistema_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $atributos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $atributos;
    }

    public function adicionarClasse ($sistema_id, $nome, $descricao, $dado_vida, $pdo)
    {
        $verificaSql = "SELECT COUNT(*) FROM classes_kits WHERE sistema_id = :sistema_id AND nome = :nome";
        $verificaStatement = $pdo->prepare($verificaSql);
        $verificaStatement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
        $verificaStatement->bindParam(':nome', $nome, PDO::PARAM_STR);
        if ($verificaStatement->fetchColumn() !=0) {
            return false;
        }
        $sql = "INSERT INTO classes_kits (sistema_id, nome, descricao, dado_vida) 
        VALUES (:sistema_id, :nome, :descricao, :dado_vida)";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':sistema_id', $sistema_id, PDO::PARAM_INT);
        $statement->bindParam(':nome', $nome, PDO::PARAM_STR);
        $statement->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $statement->bindParam(':dado_vida', $dado_vida, PDO::PARAM_STR);
        return $statement->execute();
    }

    public function adicionarPMPorNivel($classe_id, $pm, $pdo) 
    {
        $verificaSql = "SELECT COUNT(*) FROM classe_pm_progressao 
        WHERE classe_id = :classe_id";
        $verificaStatement = $pdo->prepare($verificaSql);
        $verificaStatement->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
        if ($verificaStatement->fetchColumn() !=0) {
            return false;
        }
        $sql = "INSERT INTO classe_pm_progressao (classe_id, nivel, pm)
        VALUES (:classe_id, 1, :pm)";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':classe_id',$classe_id,PDO::PARAM_INT);
        $statement->bindParam(':pm',$pm,PDO::PARAM_INT);
        return $statement->execute();
    }

    public function atualizarClasse($ficha_id, $classe_id, $nivel, $pdo)
    {
        $sql = "UPDATE ficha_classes SET nivel = :nivel 
        WHERE ficha_id = :ficha_id AND classe_ou_kit_id = :classe_id ";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':nivel',$nivel,PDO::PARAM_INT);
        $statement->bindParam(':ficha_id',$ficha_id,PDO::PARAM_INT);
        $statement->bindParam(':classe_id',$classe_id,PDO::PARAM_INT);
        return $statement->execute();
    }

    public function adicionarEspacoFeitico($classe_id, $espacos_feitico, $pdo) 
    {
        $verificaSql = "SELECT COUNT(*) FROM classe_pm_progressao 
        WHERE classe_id = :classe_id";
        $verificaStatement = $pdo->prepare($verificaSql);
        $verificaStatement->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
        if ($verificaStatement->fetchColumn() !=0) {
            return false;
        }
        $sql = "INSERT INTO classe_pm_progressao (
                classe_id,
                espacos_nivel1, espacos_nivel2, espacos_nivel3,
                espacos_nivel4, espacos_nivel5, espacos_nivel6,
                espacos_nivel7, espacos_nivel8, espacos_nivel9
            ) VALUES (
                :classe_id,
                :n1, :n2, :n3, :n4, :n5, :n6, :n7, :n8, :n9
            )";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':classe_id',$classe_id,PDO::PARAM_INT);
        for ($nivel = 1; $nivel <= 9; $nivel++) {
            $param = ':n' . $nivel;
            $valor = isset($espacos_feitico[$nivel]) ? (int)$espacos_feitico[$nivel] : 0;
            $statement->bindValue($param, $valor, PDO::PARAM_INT);
        }
        return $statement->execute();
    }

    public function buscarEspacosFeiticos($classe_id, $nivel, $pdo)
    {
        $espacos_feitico = [0,0,0,0,0,0,0,0,0];
        $sql = "SELECT * FROM classe_pm_progressao";
        $statement = $pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        for ($i=1; $i < 10; $i++) { 
            foreach ($dados as $dado) {
                if ($dado['classe_id'] === $classe_id && $dado['nivel'] == $nivel) {
                    $espacos_feitico[$i-1] = $dado['espacos_nivel'.$i];
                }
            }
        }
        return $espacos_feitico;
    }
}