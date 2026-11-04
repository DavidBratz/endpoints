<?php

include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$nome = $_POST['nome'] ?? '';
$preco = $_POST['preco'] ?? '';
$estoque = $_POST['estoque'] ?? 0;
$ativo = $_POST['ativo'] ?? 1;

if ($nome == '' || !is_numeric($preco) || $preco <= 0) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Nome e preco sao obrigatorios.';
} else {

    $sql = 'insert into produto (nome, preco, estoque, ativo) values (?, ?, ?, ?)';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('sdii', $nome, $preco, $estoque, $ativo);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Produto cadastrado com sucesso.';
    } else {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Falha ao cadastrar o produto.';
    }

    $stmt->close();
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);

?>
