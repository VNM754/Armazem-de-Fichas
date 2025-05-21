<?php


class Atributo{

    private int $id;
    private int $ficha_id;
    private string $nome;
    private int $valor;


    public function __construct(?int $id, int $ficha_id, string $nome, int $valor) {
        $this->id = $id;
        $this->ficha_id = $ficha_id;
        $this->nome = $nome;
        $this->valor = $valor;
    }

}