<?php

include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$id = $_GET['id'] ?? '';
$nome = $_POST['nome'] ?? '';
$preco = $_POST['preco'] ?? '';
$ativo = $_POST['ativo'] ?? '';

if ($id == '' || !ctype_digit((string)$id) || $id <= 0) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'ID nao informado.';
} elseif ($nome == '' || !is_numeric($preco) || $preco <= 0 || ($ativo != '0' && $ativo != '1'))  {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Preco invalido.';
} else {

    $sql = 'select id from produto where id = ?';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $produto = $resultado->fetch_assoc();

    $stmt->close();

    if (!$produto) {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Produto nao encontrado.';
    } else {

        $sql = 'update produto set nome = ?, preco = ?, ativo = ? where id = ?';

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('sdii', $nome, $preco, $ativo, $id);
        $stmt->execute();

        // Se affected_rows for 0, nao significa erro, pois o produto existe e os dados podem ser iguais.
        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Produto alterado com sucesso.';

        $stmt->close();
    }
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);

?>
