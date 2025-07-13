<?php


class Poder{

    private int $id;
    private int $sistema_id;
    private string $nome;
    private string $descricao;


    public function __construct(?int $id, int $sistema_id, string $nome, string $descricao) {
        $this->id = $id;
        $this->sistema_id = $sistema_id;
        $this->nome = $nome;
        $this->descricao = $descricao;
    }

}