<?php

include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$sql = 'select id, nome, preco, estoque from produto where ativo = 1 order by preco';

$stmt = $conexao->prepare($sql);
$stmt->execute();

$resultado = $stmt->get_result();

while ($linha = $resultado->fetch_assoc()) {
    $retorno['data'][] = $linha;
}

if (count($retorno['data']) > 0) {
    $retorno['status'] = 'ok';
    $retorno['mensagem'] = 'Produtos encontrados.';
} else {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Nao ha produtos disponiveis.';
}

$stmt->close();
$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);

?>