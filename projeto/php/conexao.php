<?php

$servidor = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'projeto';

$conexao = new mysqli($servidor, $usuario, $senha, $banco, 3306);

if ($conexao->connect_error) {
    die('Erro na conexao: ' . $conexao->connect_error);
}

?>
