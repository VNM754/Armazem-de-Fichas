<?php

class fichaRepository {

    private PDO $pdo;


    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    private function formarObjeto($dados) 
    {
        return new Ficha($dados['id'],
            $dados['usuario_id'],
            $dados['nome'],
            $dados['sistema_id'],
            $dados['imagem_url'],
            $dados['raca_id'],
            $dados['origem_id'],
            $dados['pericias']
        );
    }

    public function buscarTodos($id)
    {
        $sql = "SELECT * FROM fichas WHERE usuario_id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':id',$id,PDO::PARAM_INT);
        $statement->execute();
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todosOsDados = array_map(function ($ficha){
            return $this->formarObjeto($ficha);
        },$dados);
        return $todosOsDados;
    }

    public function buscarPorUsuario(int $id)
    {
        $sql = "SELECT * FROM fichas WHERE usuario_id = :usuario_id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':usuario_id',$id,PDO::PARAM_INT);
        $statement->execute();
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todosOsDados = array_map(function ($ficha){
            return $this->formarObjeto($ficha);
        },$dados);

        return $todosOsDados;
    }

    public function buscarRecentes(int $id)
    {
        $sql = "SELECT * FROM fichas WHERE usuario_id = :usuario_id ORDER BY data_ultimo_acesso DESC";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':usuario_id',$id,PDO::PARAM_INT);
        $statement->execute();
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todosOsDados = array_map(function ($ficha){
            return $this->formarObjeto($ficha);
        },$dados);
        return $todosOsDados;
    }

    public function buscarPorUsuarioComFiltro(int $usuario_id, int $sistema_id)
    {
        $sql = "SELECT * FROM fichas WHERE usuario_id = :usuario_id AND sistema_id = :sistema_id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindParam(':usuario_id',$usuario_id,PDO::PARAM_INT);
        $statement->bindParam(':sistema_id',$sistema_id,PDO::PARAM_INT);
        $statement->execute();
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todosOsDados = array_map(function ($ficha){
            return $this->formarObjeto($ficha);
        },$dados);

        return $todosOsDados;
    }

    public function buscarSistemaNome(int $ficha_id) {
        $sqlFicha = "SELECT * FROM fichas ";
        $statement = $this->pdo->query($sqlFicha);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($dados as $dado) {
            if ($dado['id'] === $ficha_id) {
                $sistema_id = $dado['sistema_id'];
            }
        }

        $sqlSistema = "SELECT * FROM sistemas ";
        $statementSistema = $this->pdo->query($sqlSistema);
        $dadosSistemas = $statementSistema->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dadosSistemas as $dado) {
            if ($dado['id'] === $sistema_id) {
                return $dado['nome'];
            }
        }
    }

    public function buscarSistemaId(int $sistema_id) {
        $sqlSistema = "SELECT * FROM sistemas";
        $statement = $this->pdo->query($sqlSistema);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dados as $dado) {
            if ($dado['id'] === $sistema_id) {
                return $dado['id'];
            }
        }
    }

    public function buscarSistemaIdFicha(int $ficha_id) {
        $sqlFicha = "SELECT * FROM fichas";
        $statement = $this->pdo->query($sqlFicha);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        foreach ($dados as $dado) {
            if ($dado['id'] === $ficha_id) {
                return $dado['sistema_id'];
            }
        }
    }

    public function buscarRaca(int $id) {
        $sql = "SELECT * FROM racas";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($dados as $dado) {
            if ($dado['id'] === $id) {
                return $dado['nome'];
            }
        }
        
    }

    public function buscarOrigem(int $id) {
        $sql = "SELECT * FROM origens";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($dados as $dado) {
            if ($dado['id'] === $id) {
                return $dado['nome'];
            }
        } 
    }

    public function buscarPV(int $id) {
        $sql = "SELECT * FROM fichas";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($dados as $dado) {
            if ($dado['id'] === $id) {
                return $dado['pv_atual'];
            }
        }
        
    }

    public function buscarPM(int $id) {
        $sql = "SELECT * FROM fichas";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($dados as $dado) {
            if ($dado['id'] === $id) {
                return $dado['pm_atual'];
            }
        }
        
    }

    public function buscarOrigemDescricao(int $id) {
        $sql = "SELECT * FROM origens";
        $statement = $this->pdo->query($sql);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($dados as $dado) {
            if ($dado['id'] === $id) {
                return $dado['descricao'];
            }
        } 
    }

    public function buscarAtributos($fichaId, $pdo) {
        $sql = "SELECT nome, valor FROM ficha_atributos WHERE ficha_id = :ficha_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ficha_id', $fichaId, PDO::PARAM_INT);
        $stmt->execute();
        $atributos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $atributos;
    }

    public function buscarClasse($fichaId, $pdo) {
        $sql = "SELECT classe_ou_kit_id FROM ficha_classes WHERE ficha_id = :ficha_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ficha_id', $fichaId, PDO::PARAM_INT);
        $stmt->execute();
        $classe = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $classe;
    }

    function buscarClassesDaFicha($fichaId, $pdo) {
        $sql = "
            SELECT 
                ck.id AS classe_id,
                ck.nome AS nome_classe,
                ck.descricao AS descricao_classe,
                ck.dado_vida,
                fc.inicial,
                fc.nivel AS nivel
            FROM ficha_classes fc
            JOIN classes_kits ck ON fc.classe_ou_kit_id = ck.id
            WHERE fc.ficha_id = :ficha_id;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ficha_id', $fichaId, PDO::PARAM_INT);
        $stmt->execute();

        $classe = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $classe;
    }

    public function calcularBonusDeTrei($ficha_id)
    {
        $classes = $this->buscarClassesDaFicha($ficha_id, $this->pdo);

        foreach ($classes as $classe) {
            $nivel =+ $classe['nivel'];
        }
        if ($nivel>1 && $nivel<7) {
            return +2;
        }elseif ($nivel>=7 && $nivel<14) {
            return +4;
        }elseif ($nivel>=14) {
            return +6;
        }

    }

    public function calcularBonusDeProf($ficha_id)
    {
        $classes = $this->buscarClassesDaFicha($ficha_id, $this->pdo);

        foreach ($classes as $classe) {
            $nivel =+ $classe['nivel'];
        }
        if ($nivel>1 && $nivel<5) {
            return +2;
        }elseif ($nivel>=5 && $nivel<9) {
            return +3;
        }elseif ($nivel>=9 && $nivel<13) {
            return +4;
        }elseif ($nivel>=13 && $nivel<17) {
            return +5;
        }elseif ($nivel>=17) {
            return +6;
        }
    }

    public function buscarPorID(int $id) {
        $sql = "SELECT * FROM fichas WHERE id=id";
        $statement = $this->pdo->query($sql);
        $statement->bindParam('int', $id, PDO::PARAM_INT);
        $dados = $statement->fetchAll(PDO::FETCH_ASSOC);

        $todosOsDados = array_map(function ($ficha){
            return $this->formarObjeto($ficha);
        },$dados);

        foreach ($todosOsDados as $dado) {
            if ($dado->getId() == $id) {
                return $dado;
            }
        }
    }

    public function adicionarClasse($ficha_id, $classe_id, $nivel, $inicial, $pdo){
        $sql = "INSERT INTO ficha_classes (ficha_id, classe_ou_kit_id, nivel, inicial)
        VALUES (:ficha_id, :classe_id, :nivel, :inicial)";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
        $statement->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
        $statement->bindParam(':nivel', $nivel, PDO::PARAM_INT);
        $statement->bindParam(':inicial', $inicial, PDO::PARAM_INT);
        return $statement->execute();
    }

    public function adicionarOrigem($ficha_id, $origem_id, $pdo){
        $sql = "INSERT INTO fichas (ficha_id, origem_id)
        VALUES (:ficha_id, :origem_id)";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
        $statement->bindParam(':origem_id', $origem_id, PDO::PARAM_INT);
        return $statement->execute();
    }

    function excluirClasseDeFichaComSeguranca(PDO $pdo, int $ficha_id, int $classe_id, int $usuario_id): bool 
    {
        // Primeiro, verifica se a ficha pertence ao usuário
        $verificaSql = "SELECT COUNT(*) FROM fichas WHERE id = :ficha_id AND usuario_id = :usuario_id";
        $verificaStmt = $pdo->prepare($verificaSql);
        $verificaStmt->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
        $verificaStmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $verificaStmt->execute();

        if ($verificaStmt->fetchColumn() == 0) {
            // Ficha não pertence ao usuário
            return false;
        }

        // Agora pode remover a classe
        $sql = "DELETE FROM ficha_classes 
                WHERE ficha_id = :ficha_id AND classe_ou_kit_id = :classe_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ficha_id', $ficha_id, PDO::PARAM_INT);
        $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function definirAtributos($id_sistema)
    {
        if ($id_sistema == "1") {
        $atributos = ['FOR', 'DES', 'CON', 'INT', 'SAB', 'CAR'];
        } elseif ($id_sistema == "2") {
            $atributos = ['FOR', 'DES', 'CON', 'INT', 'SAB', 'CAR'];
        }elseif ($id_sistema == 3) {
            $atributos = ['POD', 'HAB', 'RES'];
        }elseif ($id_sistema == 4) {
            $atributos = ['AGL', 'FOR', 'INT', 'PRE', 'VIG'];
        }
        return $atributos;
    }

    public function definirPericias($id_sistema)
    {
        if ($id_sistema == "1") {
        $pericias = ['Acrobacia', 'Arcanismo', 'Atletismo', 'Atuacao', 'Blefar', 'Furtividade', 'História', 'Intimidacao', 'Intuicao',
              'Investigacao', 'Lidar com Animais', 'Medicina', 'Natureza', 'Percepcao', 'Persuasão', 'Prestidigitação', 'Religiao', 'Sobrevivencia'];
        } elseif ($id_sistema == "2") {
            $pericias = ['Acrobacia', 'Adestramento', 'Atletismo', 'Atuacao', 'Cavalgar', 'Conhecimento', 'Cura',
             'Diplomacia', 'Enganacao', 'Fortitude', 'Furtividade', 'Guerra', 'Iniciativa', 'Intimidacao', 'Intuicao',
              'Investigacao', 'Jogatina', 'Ladinagem', 'Luta', 'Misticismo', 'Nobreza', 'Oficio', 'Percepcao', 'Pilotagem',
               'Pontaria', 'Reflexos', 'Religiao', 'Sobrevivencia', 'Vontade'];
        }elseif ($id_sistema == 3) {
            $pericias = ['Animais', 'Arte', 'Esporte', 'Ifluência', 'Luta', 'Manha', 'Máquinas', 'Medicina', 'Mística', 'Percepção', 'Saber', 'Sobrevivência'];
        }elseif ($id_sistema == 4) {
            $pericias = ['Acrobacia', 'Adestramento', 'Artes', 'Atletismo', 'Atualidades', 'Ciências', 'Crime',
             'Diplomacia', 'Enganacao', 'Fortitude', 'Furtividade', 'Iniciativa', 'Intimidacao', 'Intuicao',
              'Investigacao', 'Luta', 'Medicina', 'Ocultismo', 'Percepcao', 'Pilotagem',
               'Pontaria', 'Profissão', 'Reflexos', 'Religiao', 'Sobrevivencia', 'Tática', 'Tecnologia', 'Vontade'];
        }
        return $pericias;
    }

    public function definirAtributoPericias($pericia)
    {
        switch ($pericia) {
            case 'Acrobacia':
            case 'Furtividade':
            case 'Iniciativa':
            case 'Ladinagem':
            case 'Pilotagem':
            case 'Prestidigitação':
            case 'Reflexos':
                return 'DES'; // Destreza

            case 'Atletismo':
            case 'Cavalgar':
            case 'Luta':
            case 'Pontaria':
                return 'FOR'; // Força

            case 'Cura':
            case 'Intuicao':
            case 'Lidar com Animais':
            case 'Medicina':
            case 'Percepcao':
            case 'Sobrevivencia':
            case 'Vontade':
                return 'SAB'; // Sabedoria

            case 'Adestramento':
            case 'Atuacao':
            case 'Blefar':
            case 'Diplomacia':
            case 'Enganacao':
            case 'Intimidacao':
            case 'Jogatina':
            case 'Persuasão':
                return 'CAR'; // Carisma

            case 'Arcanismo':
            case 'Conhecimento':
            case 'História':
            case 'Investigacao':
            case 'Misticismo':
            case 'Natureza':
            case 'Nobreza':
            case 'Religiao':
            case 'Oficio':
            case 'Guerra':
                return 'INT'; // Inteligência

            case 'Fortitude':
                return 'CON'; // Constituição

            default:
                return null; // Perícia não reconhecida
        }
    }

    public function buscarPMPorClasseENivel(PDO $pdo, int $classe_id, int $nivel): ?int 
    {
        $sql = "SELECT pm
                FROM classe_pm_progressao
                WHERE classe_id = :classe_id AND nivel = :nivel";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
        $stmt->bindParam(':nivel', $nivel, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? (int)$resultado['pm'] : null;
}

    public function calcularValorPericiaT20($pericia, $ficha_id)
    {
        $resultadoFinal= 0;
        $nivelTotal = 0;
        $atributos = $this->buscarAtributos($ficha_id,$this->pdo);
        $atributoPadrao = $this->definirAtributoPericias($pericia);
        foreach ($atributos as $atributo) {
            if ($atributo['nome'] == $atributoPadrao) {
                $modificador = floor(($atributo['valor'])-10)/2;
            }
        }
        $classesDaFicha = $this->buscarClassesDaFicha($ficha_id,$this->pdo);
        foreach ($classesDaFicha as $classe) {
            $nivelTotal += $classe['nivel'];
        }
        $bonusTreino = $this->calcularBonusDeTrei($ficha_id);


        $resultadoFinal += floor($nivelTotal/2) + $modificador;
        return $resultadoFinal;
    }

    public function calcularValorPericiaDD($pericia, $ficha_id)
    {
        $resultadoFinal= 0;
        $atributos = $this->buscarAtributos($ficha_id,$this->pdo);
        $atributoPadrao = $this->definirAtributoPericias($pericia);
        foreach ($atributos as $atributo) {
            if ($atributo['nome'] == $atributoPadrao) {
                $modificador = floor(($atributo['valor']-10)/2);
            }
        }

        $resultadoFinal += $modificador;
        return $resultadoFinal;
    }

    public function AtualizarFicha($ficha_id, $nome, $origem, $pv_atual, $pm_atual, $periciasTexto, $data_nova, $pdo) {
        $sqlFicha = "UPDATE fichas SET nome = :nome, origem_id = :origem, pv_atual = :pv_atual, pm_atual = :pm_atual, pericias = :pericias, data_ultimo_acesso = :data_nova  
        WHERE id = :ficha_id";
        $stmtFicha = $pdo->prepare($sqlFicha);
        $stmtFicha->bindParam(':nome',$nome,PDO::PARAM_STR);
        $stmtFicha->bindParam(':origem',$origem,PDO::PARAM_INT);
        $stmtFicha->bindParam(':pv_atual',$pv_atual,PDO::PARAM_INT);
        $stmtFicha->bindParam(':pm_atual',$pm_atual,PDO::PARAM_INT);
        $stmtFicha->bindParam(':pericias',$periciasTexto,PDO::PARAM_STR);
        $stmtFicha->bindParam(':ficha_id',$ficha_id,PDO::PARAM_INT);
        $stmtFicha->bindParam(':data_nova',$data_nova,PDO::PARAM_STR);
        return $stmtFicha->execute();
    }

    public function atualizarAtributos($ficha_id, $atributos, $pdo) {
        $sqlFicha = "UPDATE ficha_atributos SET valor = :valor 
        WHERE nome = :nome AND ficha_id = :ficha_id";
        $stmtFicha = $pdo->prepare($sqlFicha);
        foreach ($atributos as $nome => $valor) {
            $valor = isset($valor) ? (int)$valor : 0;
            
            $stmtFicha->bindValue(':nome',$nome,PDO::PARAM_STR);
            $stmtFicha->bindValue(':valor',$valor,PDO::PARAM_INT);
            $stmtFicha->bindValue(':ficha_id',$ficha_id,PDO::PARAM_INT);

            if (!$stmtFicha->execute()) {
                return false;
            }
        }
        return true;
    }
}