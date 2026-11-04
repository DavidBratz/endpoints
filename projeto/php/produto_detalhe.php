<?php

include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$id = $_GET['id'] ?? '';

if ($id == '') {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'ID nao informado.';
} elseif (!ctype_digit($id)) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Produto nao encontrado.';
} else {

    $sql = 'select id, nome, preco, estoque from produto where id = ? and ativo = 1';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $produto = $resultado->fetch_assoc();

    if ($produto) {
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Consulta efetuada.';
        $retorno['data'][] = $produto;
    } else {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Produto nao encontrado.';
    }

    $stmt->close();
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);

?>