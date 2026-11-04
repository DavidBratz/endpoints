<?php

include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$id = $_GET['id'] ?? '';

if ($id == '') {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'E necessario informar um ID para exclusao.';
} else {

    $sql = 'select estoque from produto where id = ?';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $produto = $resultado->fetch_assoc();

    $stmt->close();

    if (!$produto) {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Produto nao encontrado.';
    } elseif ($produto['estoque'] > 0) {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Produto com estoque nao pode ser excluido.';
    } else {

        $sql = 'delete from produto where id = ? and estoque = 0';

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $retorno['status'] = 'ok';
            $retorno['mensagem'] = 'Produto excluido.';
        } else {
            $retorno['status'] = 'nok';
            $retorno['mensagem'] = 'Produto nao encontrado.';
        }

        $stmt->close();
    }
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);

?>
