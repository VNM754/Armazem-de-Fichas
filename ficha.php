<?php

class Ficha{
    private ?int $id;
    private int $usuario_id;
    private string $nome;
    private int $sistema_id;
    private string $imagem_url;
    private int $raca_id;
    private int $origem_id;
    private string $pericias;


    public function __construct(?int $id, 
    int $usuario_id, 
    string $nome, 
    int $sistema_id,
    string $imagem_url,
    int $raca_id,
    int $origem_id,
    string $pericias) {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->nome = $nome;
        $this->sistema_id = $sistema_id;
        $this->imagem_url = $imagem_url;
        $this->raca_id = $raca_id;
        $this->origem_id = $origem_id;
        $this->pericias = $pericias;
    }

    public function getId() : ?int {
        return $this->id;
    }

    public function getIdUsuario() : ?int {
        return $this->usuario_id;
    }

    public function getNome() : string {
        return $this->nome;
    }

    public function getSistema() : int {
        return $this->sistema_id;
    }
    
    public function getRaca() : int {
        return $this->raca_id;
    }

    public function getOrigem() : int {
        return $this->origem_id;
    }

    public function getImagem(): string
    {
        return $this->imagem_url;
    }

    public function getPericias(): string
    {
        return $this->pericias;
    }

    public function getImagemDiretorio(): string
    {
        return "\images\\".$this->imagem_url;
    }


}
