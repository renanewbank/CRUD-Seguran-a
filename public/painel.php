<?php
session_start();
// Verificação de login e tipo
if (!isset($_SESSION['login']) || $_SESSION['tipo'] !== 'admin') {
    die("Acesso negado.");
}

// Verifica inatividade (5 minutos)
$tempoInatividade = 300; // 5 minutos

if (isset($_SESSION['ultimo_acesso'])) {
    if (time() - $_SESSION['ultimo_acesso'] > $tempoInatividade) {
        session_unset();
        session_destroy();
        header("Location: login.php?expirado=1");
        exit;
    }
}

$_SESSION['ultimo_acesso'] = time();

require __DIR__ . '/../config/conexao.php';

$res = $conn->query("SELECT id, nome, email, tipo FROM usuario ORDER BY nome");

$msg = $_GET['msg'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/style.css">
    <title>Painel</title>
</head>
<body class="bg-dark text-white p-4">
    <div class="container">
        <?php
            require __DIR__ . '/../config/conexao.php';
            // Buscar nome do usuário logado
            $stmt = $conn->prepare("SELECT nome FROM usuario WHERE email = ?");
            $stmt->bind_param("s", $_SESSION['login']);
            $stmt->execute();
            $res_nome = $stmt->get_result();
            $dados = $res_nome->fetch_assoc();
            $nome_usuario = $dados['nome'] ?? $_SESSION['login'];

            $result = $conn->query("SELECT COUNT(*) AS total FROM usuario");
            $row = $result->fetch_assoc();
            $totalUsuarios = $row['total'];
        ?>
        <h2>Bem-vindo, <?= htmlspecialchars($nome_usuario) ?></h2>
        <h4><?= $totalUsuarios ?> usuários cadastrados no sistema</h4>

        <?php if ($msg): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <table class="table table-dark table-striped text-center">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($user = $res->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($user['nome']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['tipo']) ?></td>
                    <td>
                        <a href="../database/editar_usuario.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                        <a href="#" class="btn btn-sm btn-danger" onclick="removerUsuario(<?= $user['id'] ?>, this); return false;">Remover</a>

                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <a href="../database/criar_usuario.php" class="btn btn-primary">Criar novo usuário</a>
        <form action="sair.php" method="post" class="mt-4">
            <button type="submit" class="btn btn-danger">Sair</button>
        </form>

        
        
    </div>
</body>
</html>

<script>
function removerUsuario(id, botao) {
    if (!confirm('Tem certeza que deseja excluir este usuário?')) return;

    fetch(`../database/deletar_usuario.php?id=${id}`, {
        method: 'GET',
    })
    .then(response => response.text())
    .then(data => {
        if (data.trim() === "OK") {
            const linha = botao.closest('tr');
            linha.remove(); // remove a linha da tabela
        } else {
            alert("Erro ao remover: " + data);
        }
    })
    .catch(error => {
        alert("Erro na requisição: " + error);
    });
}
</script>
