<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['tipo'] !== 'admin') {
    die("Acesso negado.");
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../config/conexao.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: editar_usuarios.php?msg=ID do usuário não fornecido.");
    exit;
}

$msg = '';
// Buscar dados atuais do usuário
$stmt = $conn->prepare("SELECT nome, email, tipo FROM usuario WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Usuário não encontrado.");
}

$user = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo  = $_POST['tipo'] ?? 'user';

    if (!$nome || !$email) {
        $msg = "Nome e email são obrigatórios.";
    } else {
        if ($senha) {
            // Atualizar com senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, email=?, senha_hash=?, tipo=? WHERE id=?");
            $stmt->bind_param("ssssi", $nome, $email, $senha_hash, $tipo, $id);
        } else {
            // Atualizar sem alterar a senha
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, email=?, tipo=? WHERE id=?");
            $stmt->bind_param("sssi", $nome, $email, $tipo, $id);
            if (!$stmt) {
                die("Erro no prepare: " . $conn->error);
            }
        }
        if ($stmt->execute()) {
            header("Location: ../public/painel.php?msg=Usuário atualizado com sucesso.");
            exit;
        } else {
            $msg = "Erro ao atualizar usuário.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/style.css">
<title>Editar Usuário</title>
</head>
<body class="bg-dark text-white p-4">
    <div class="container">
        <h2>Editar usuário</h2>

        <?php if ($msg): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do usuário:</label>
                <input type="text" name="nome" class="form-control" id="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? $user['nome']) ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" name="email" class="form-control" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>">
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Nova senha (deixe em branco para manter a atual):</label>
                <input type="password" name="senha" class="form-control" id="senha">
            </div>
            <div class="mb-5">
                <label for="tipo" class="form-label">Tipo de conta:</label>
                <select name="tipo" class="form-select" id="tipo">
                    <option value="user" <?= (($_POST['tipo'] ?? $user['tipo']) === 'user') ? 'selected' : '' ?>>Usuário</option>
                    <option value="admin" <?= (($_POST['tipo'] ?? $user['tipo']) === 'admin') ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
            <a href="../public/painel.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
