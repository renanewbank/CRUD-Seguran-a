<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: painel.php");
    exit;
}

// Gera um cookie seguro, se ainda não existir
if (!isset($_COOKIE['user_session'])) {
    $token = bin2hex(random_bytes(32));
    setcookie(
        'user_session',
        $token,
        [
            'expires' => time() + 3600, // 1 hora
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']), // Garante HTTPS
            'httponly' => true,
            'samesite' => 'Strict'
        ]
    );
}

// CSRF Token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../public/style.css">
    <title>Faça login</title>
</head>
<body class="bg-dark d-flex justify-content-center align-items-center min-vh-100">
    <div class="content card shadow p-4">
        <h2 class="text-center mb-4">Login</h2>
        <?php
        if (isset($_GET['erro'])) {
            if ($_GET['erro'] == 1) {
                echo '<div class="alert alert-danger">E-mail ou senha incorretos.</div>';
            } elseif ($_GET['erro'] == 4 && isset($_GET['msg'])) {
                echo '<div class="alert alert-warning">' . htmlspecialchars($_GET['msg']) . '</div>';
            }
        }
        ?>
        <form method="POST" action="../controllers/processa.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="acao" value="login">

            <div class="mb-3">
                <label for="email" class="form-label">E-mail:</label>
                <input type="text" name="email" class="form-control" id="email" required>
            </div>
            <div class="mb-5">
                <label for="senha" class="form-label">Senha:</label>
                <div class="input-group">
                    <input type="password" name="senha" class="form-control" id="senha" required>
                    <button type="button" class="btn btn-secondary" onclick="toggleSenha()"><i class="password-icon fa-solid fa-eye"></i></button>
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Entrar</button>
                <a href="cadastro.php" class="btn btn-secondary">Cadastrar nova conta</a>
            </div>
        </form>
    </div>
    

    <div id="cookie-consent" class="cookie-consent bg-light text-dark border p-3 rounded shadow position-fixed bottom-0 start-0 m-4" style="z-index:9999; display:none; max-width: 350px;">
        <p>Este site usa cookies essenciais para oferecer uma melhor experiência. Você aceita?</p>
        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-sm btn-secondary" onclick="rejectCookies()">Rejeitar</button>
            <button class="btn btn-sm btn-primary" onclick="acceptCookies()">Aceitar</button>
        </div>
    </div>

</body>
</html>

<script>
    function toggleSenha() {
        const senhaInput = document.querySelector('#senha');
        const eyeIcon = document.querySelector('.password-icon');
        if (senhaInput.type === 'password') {
            senhaInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            senhaInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

<script>
function acceptCookies() {
    localStorage.setItem('cookieConsent', 'accepted');
    document.getElementById('cookie-consent').style.display = 'none';

    // Define o cookie essencial de segurança (exemplo fictício aqui)
    document.cookie = "user_session=" + crypto.randomUUID() + "; path=/; Secure; SameSite=Strict; HttpOnly";
}

function rejectCookies() {
    localStorage.setItem('cookieConsent', 'rejected');
    document.getElementById('cookie-consent').style.display = 'none';
}

window.addEventListener('load', () => {
    const consent = localStorage.getItem('cookieConsent');
    if (!consent) {
        document.getElementById('cookie-consent').style.display = 'block';
    }
});
</script>
