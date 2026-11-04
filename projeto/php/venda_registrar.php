<?php
session_start();

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

if (!isset($_SESSION['usuario_id'])) {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Acesso negado. Faca login.';

    header('Content-type: application/json; charset=utf-8');
    echo json_encode($retorno);
    exit;
}

include_once('conexao.php');

$produto_id = $_POST['produto_id'] ?? '';
$quantidade = $_POST['quantidade'] ?? '';

if ($produto_id == '' || !ctype_digit((string)$produto_id) ||
    $quantidade == '' || !ctype_digit((string)$quantidade) ||
    $quantidade <= 0) {

    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Dados invalidos.';

} else {

    $conexao->begin_transaction();

    try {
        $sql = 'select id, preco, estoque from produto
                where id = ? and ativo = 1 for update';

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('i', $produto_id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $produto = $resultado->fetch_assoc();
        $stmt->close();

        if (!$produto) {
            throw new Exception('Produto nao encontrado.');
        }

        if ($quantidade > $produto['estoque']) {
            throw new Exception('Estoque insuficiente.');
        }

        $valor_total = $produto['preco'] * $quantidade;
        $estoque_restante = $produto['estoque'] - $quantidade;
        $usuario_id = $_SESSION['usuario_id'];

        $sql = 'update produto set estoque = estoque - ?
                where id = ?';

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('ii', $quantidade, $produto_id);
        $stmt->execute();
        $stmt->close();

        $sql = 'insert into venda
                (produto_id, usuario_id, quantidade, valor_total)
                values (?, ?, ?, ?)';

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param('iiid', $produto_id, $usuario_id, $quantidade, $valor_total);
        $stmt->execute();
        $stmt->close();

        $venda_id = $conexao->insert_id;

        $conexao->commit();

        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Venda registrada.';
        $retorno['data'][] = [
            'venda_id' => $venda_id,
            'valor_total' => $valor_total,
            'estoque_restante' => $estoque_restante
        ];

    } catch (Exception $erro) {
        $conexao->rollback();

        $retorno['status'] = 'nok';
        $retorno['mensagem'] = $erro->getMessage();
    }
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);
?>
