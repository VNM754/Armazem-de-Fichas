<?php

class Classe{
    
    private int $id;
    private int $sistema_id;
    private string $nome;
    private string $descricao;
    private string $dado_vida;

    public function __construct(?int $id, int $sistema_id, string $nome, string $descricao, string $dado_vida) {
        $this->id = $id;
        $this->sistema_id = $sistema_id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->dado_vida = $dado_vida;
    }

    public function getId()
    {
        return $this->id;    
    }

    public function getSistema()
    {
        return $this->sistema_id;    
    }

    public function getNome()
    {
        return $this->nome;    
    }

    public function getDescricao()
    {
        return $this->descricao;    
    }

    public function getDado_Vida()
    {
        return $this->dado_vida;    
    }
}