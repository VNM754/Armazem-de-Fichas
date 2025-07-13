<?php


class Equipamento{

    private int $id;
    private string $tipo;
    private string $nome;
    private string $descricao;
    private string $detalhes;


    public function __construct(?int $id, string $tipo, string $nome, string $descricao, string $detalhes) {
        $this->id = $id;
        $this->tipo = $tipo;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->detalhes = $detalhes;
    }

}