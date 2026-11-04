<?php
include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$id = $_GET['id'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$quantidade = $_POST['quantidade'] ?? '';

if ($id == '' || !ctype_digit((string)$id) || $id <= 0) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'ID nao informado.';
} elseif (($tipo != 'entrada' && $tipo != 'saida') || !ctype_digit((string)$quantidade) || $quantidade <= 0) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Dados invalidos.';
} else {
    if ($tipo == 'entrada') {
        $sql = 'update produto set estoque = estoque + ? where id = ?';
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('ii', $quantidade, $id);
    } else {
        $sql = 'update produto set estoque = estoque - ?
                where id = ? and estoque >= ?';
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('iii', $quantidade, $id, $quantidade);
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();

        $sql = 'select estoque from produto where id = ?';
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $dados = $resultado->fetch_assoc();

        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Estoque atualizado.';
        $retorno['data'][] = $dados;
    } else {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Estoque insuficiente.';
    }

    $stmt->close();
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);
?>
