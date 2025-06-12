<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['login']) || $_SESSION['tipo'] !== 'admin') {
    die("Acesso negado.");
}

require __DIR__ . '/../config/conexao.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $tipo  = $_POST['tipo'] ?? 'user';

    if (!$nome || !$email || !$senha) {
        $msg = "Preencha todos os campos.";
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO usuario (nome, email, senha_hash, tipo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senha_hash, $tipo);
        $executou = $stmt->execute();

        if ($executou) {
            $msg = "Usuário '$nome' criado com sucesso!";
        } else {
            $msg = "Erro ao criar usuário. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../public/style.css" />
    <title>Criar Usuário</title>
</head>
<body class="bg-dark text-white p-4">
    <div class="container">
        <h2>Criar novo usuário</h2>
        
        <?php if ($msg): ?>
            <div class="alert alert-info text-center">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do usuário:</label>
                <input type="text" name="nome" class="form-control" id="nome" required value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" name="email" class="form-control" id="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" name="senha" class="form-control" id="senha" required>
            </div>
            <div class="mb-5">
                <label for="tipo" class="form-label">Tipo de conta:</label>
                <select name="tipo" class="form-select" id="tipo">
                    <option value="user" <?= (($_POST['tipo'] ?? '') === 'user') ? 'selected' : '' ?>>Usuário</option>
                    <option value="admin" <?= (($_POST['tipo'] ?? '') === 'admin') ? 'selected' : '' ?>>Administrador</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Criar</button>
            <a href="../public/painel.php" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</body>
</html>