<?php
include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$termo = $_GET['termo'] ?? '';
$somente_estoque = $_GET['somente_estoque'] ?? '';

if ($termo == '') {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Informe um termo de busca.';
} else {
    $busca = '%' . $termo . '%';

    if ($somente_estoque == 1) {
        $sql = 'select id, nome, preco, estoque from produto
                where ativo = 1 and nome like ? and estoque > 0
                order by nome';
    } else {
        $sql = 'select id, nome, preco, estoque from produto
                where ativo = 1 and nome like ?
                order by nome';
    }

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('s', $busca);
    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($produto = $resultado->fetch_assoc()) {
        $retorno['data'][] = $produto;
    }

    if (count($retorno['data']) > 0) {
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Consulta efetuada.';
    } else {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Nenhum produto encontrado.';
    }

    $stmt->close();
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);
?>
