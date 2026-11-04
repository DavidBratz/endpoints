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

$sql = 'select count(*) as total_produtos,
        coalesce(sum(estoque), 0) as total_unidades,
        coalesce(sum(preco * estoque), 0) as valor_estoque
        from produto where ativo = 1';

$stmt = $conexao->prepare($sql);
$stmt->execute();

$resultado = $stmt->get_result();
$dados = $resultado->fetch_assoc();

$retorno['status'] = 'ok';
$retorno['mensagem'] = 'Consulta efetuada.';
$retorno['data'][] = $dados;

$stmt->close();
$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);

?>
