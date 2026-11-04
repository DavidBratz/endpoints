<?php

include_once('conexao.php');

$retorno = ['status' => '', 'mensagem' => '', 'data' => []];

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($usuario == '' || $senha == '') {
    $retorno['status'] = 'nok';
    $retorno['mensagem'] = 'Informe usuario e senha.';
} else {

    $sql = 'select id, nome, senha from usuario where usuario = ? and ativo = 1';

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('s', $usuario);
    $stmt->execute();

    $resultado = $stmt->get_result();
    $dados = $resultado->fetch_assoc();

    if ($dados && password_verify($senha, $dados['senha'])) {
        session_start();

        $_SESSION['usuario_id'] = $dados['id'];
        $_SESSION['usuario_nome'] = $dados['nome'];

        $retorno['status'] = 'ok';
        $retorno['mensagem'] = 'Login efetuado com sucesso.';
        $retorno['data'][] = [
            'id' => $dados['id'],
            'nome' => $dados['nome']
        ];
    } else {
        $retorno['status'] = 'nok';
        $retorno['mensagem'] = 'Credenciais invalidas.';
    }

    $stmt->close();
}

$conexao->close();

header('Content-type: application/json; charset=utf-8');
echo json_encode($retorno);

?>
