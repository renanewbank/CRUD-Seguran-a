<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: painel.php");
    exit;
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/style.css">
    <title>Cadastro</title>
    <style>
        .password-requirements {
            margin-top: 10px;
            padding: 15px;
            background: rgba(248, 249, 250, 0.8);
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            transition: color 0.3s ease;
        }
        
        .requirement:last-child {
            margin-bottom: 15px;
        }
        
        .requirement-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 11px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .requirement.invalid {
            color: #dc3545;
        }
        
        .requirement.invalid .requirement-icon {
            background: #dc3545;
            color: white;
        }
        
        .requirement.valid {
            color: #198754;
        }
        
        .requirement.valid .requirement-icon {
            background: #198754;
            color: white;
        }
        
        .strength-meter {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .strength-bar {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }
        
        .strength-text {
            text-align: center;
            margin-top: 8px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #6c757d;
            cursor: pointer;
            padding: 5px;
            font-size: 16px;
        }
        
        .password-toggle:hover {
            color: #495057;
        }
        
        .content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-dark d-flex justify-content-center align-items-center min-vh-100">
    <div class="content card shadow p-4">
        <h2 class="text-center mb-4">
            <i class="bi bi-person-plus-fill me-2"></i>
            Cadastrar nova conta
        </h2>
        
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-info text-center alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <?= htmlspecialchars($_GET['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="../controllers/processa.php" id="cadastroForm">
            <input type="hidden" name="acao" value="cadastrar">
            
            <div class="mb-3">
                <label for="nome" class="form-label">
                    <i class="bi bi-person me-1"></i>
                    Nome do usuário:
                </label>
                <input type="text" name="nome" class="form-control" id="nome" 
                       placeholder="Digite seu nome completo" required>
                <div class="invalid-feedback" id="nome-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope me-1"></i>
                    E-mail:
                </label>
                <input type="email" name="email" class="form-control" id="email" 
                       placeholder="seu@email.com" required>
                <div class="invalid-feedback" id="email-feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="senha" class="form-label">
                    <i class="bi bi-lock me-1"></i>
                    Senha:
                </label>
                <div class="position-relative">
                    <input type="password" name="senha" class="form-control" id="senha" 
                           placeholder="Digite uma senha segura" required>
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="senha-feedback"></div>
                <!-- Container para validação será inserido aqui pelo JavaScript -->
            </div>
            
            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-secondary btn-lg" id="submitBtn" disabled>
                    <i class="bi bi-person-check me-2"></i>
                    Cadastrar
                </button>
                <a href="login.php" class="btn btn-outline-secondary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Já tenho uma conta
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class PasswordValidator {
            constructor(passwordInputId) {
                this.passwordInput = document.getElementById(passwordInputId);
                this.submitBtn = document.getElementById('submitBtn');
                this.form = document.getElementById('cadastroForm');
                this.requirementsContainer = null;
                this.strengthBar = null;
                this.strengthText = null;
                
                // Verificar se todos os elementos necessários existem
                if (!this.passwordInput || !this.submitBtn || !this.form) {
                    console.error('Elementos necessários não encontrados no DOM');
                    return;
                }
                
                this.init();
            }

            init() {
                this.createValidationUI();
                this.bindEvents();
                this.setupTogglePassword();
            }

            createValidationUI() {
                const requirementsHTML = `
                    <div class="password-requirements">
                        <h6 class="mb-2 text-muted">
                            <i class="bi bi-shield-check me-1"></i>
                            Requisitos da senha:
                        </h6>
                        <div class="requirement invalid" data-rule="length">
                            <div class="requirement-icon">✗</div>
                            <span>Mínimo de 8 caracteres</span>
                        </div>
                        <div class="requirement invalid" data-rule="uppercase">
                            <div class="requirement-icon">✗</div>
                            <span>Pelo menos 1 letra maiúscula (A-Z)</span>
                        </div>
                        <div class="requirement invalid" data-rule="number">
                            <div class="requirement-icon">✗</div>
                            <span>Pelo menos 1 número (0-9)</span>
                        </div>
                        <div class="requirement invalid" data-rule="special">
                            <div class="requirement-icon">✗</div>
                            <span>Pelo menos 1 caractere especial (!@#$%^&*)</span>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-bar"></div>
                        </div>
                        <div class="strength-text text-muted">Digite uma senha</div>
                    </div>
                `;

                // Inserir após o feedback da senha
                const senhaFeedback = document.getElementById('senha-feedback');
                if (senhaFeedback) {
                    senhaFeedback.insertAdjacentHTML('afterend', requirementsHTML);
                    this.requirementsContainer = senhaFeedback.nextElementSibling;
                    this.strengthBar = this.requirementsContainer.querySelector('.strength-bar');
                    this.strengthText = this.requirementsContainer.querySelector('.strength-text');
                }
            }

            setupTogglePassword() {
                const toggleBtn = document.getElementById('togglePassword');
                if (toggleBtn) {
                    const icon = toggleBtn.querySelector('i');
                    
                    toggleBtn.addEventListener('click', () => {
                        const type = this.passwordInput.type === 'password' ? 'text' : 'password';
                        this.passwordInput.type = type;
                        
                        if (icon) {
                            if (type === 'password') {
                                icon.className = 'bi bi-eye';
                            } else {
                                icon.className = 'bi bi-eye-slash';
                            }
                        }
                    });
                }
            }

            bindEvents() {
                // Validação em tempo real da senha
                this.passwordInput.addEventListener('input', (e) => {
                    this.validatePassword(e.target.value);
                });

                this.passwordInput.addEventListener('blur', (e) => {
                    this.validatePasswordField(e.target.value);
                });

                // Validação dos outros campos
                const nomeField = document.getElementById('nome');
                const emailField = document.getElementById('email');
                
                if (nomeField) {
                    nomeField.addEventListener('blur', (e) => {
                        this.validateNameField(e.target.value);
                    });
                    
                    nomeField.addEventListener('input', () => {
                        this.updateSubmitButton();
                    });
                }

                if (emailField) {
                    emailField.addEventListener('blur', (e) => {
                        this.validateEmailField(e.target.value);
                    });
                    
                    emailField.addEventListener('input', () => {
                        this.updateSubmitButton();
                    });
                }

                // Validação no envio do formulário
                this.form.addEventListener('submit', (e) => {
                    this.handleFormSubmit(e);
                });
            }

            validatePassword(password) {
                const rules = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[!@#$%^&*]/.test(password)
                };

                // Atualizar interface dos requisitos
                if (this.requirementsContainer) {
                    Object.keys(rules).forEach(rule => {
                        const element = this.requirementsContainer.querySelector(`[data-rule="${rule}"]`);
                        if (element) {
                            const icon = element.querySelector('.requirement-icon');
                            
                            if (rules[rule]) {
                                element.className = 'requirement valid';
                                if (icon) icon.textContent = '✓';
                            } else {
                                element.className = 'requirement invalid';
                                if (icon) icon.textContent = '✗';
                            }
                        }
                    });
                }

                // Atualizar medidor de força
                this.updateStrengthMeter(rules, password);

                // Verificar se a senha é válida
                const isValid = Object.values(rules).every(Boolean);
                
                // Atualizar estado do botão
                this.updateSubmitButton();

                return isValid;
            }

            updateStrengthMeter(rules, password) {
                if (!this.strengthBar || !this.strengthText) return;
                
                const validRules = Object.values(rules).filter(Boolean).length;
                let strength = 0;
                
                if (password.length > 0) {
                    strength = validRules;
                    if (password.length >= 12 && validRules >= 3) {
                        strength = Math.min(strength + 1, 4);
                    }
                }
                
                const strengthConfig = {
                    0: { width: '0%', color: '#dee2e6', text: 'Digite uma senha', class: 'text-muted' },
                    1: { width: '25%', color: '#dc3545', text: 'Muito fraca', class: 'text-danger' },
                    2: { width: '50%', color: '#fd7e14', text: 'Fraca', class: 'text-warning' },
                    3: { width: '75%', color: '#20c997', text: 'Boa', class: 'text-info' },
                    4: { width: '100%', color: '#198754', text: 'Forte', class: 'text-success' }
                };

                const config = strengthConfig[Math.min(strength, 4)];
                this.strengthBar.style.width = config.width;
                this.strengthBar.style.background = config.color;
                this.strengthText.textContent = config.text;
                this.strengthText.className = `strength-text ${config.class}`;
            }

            validatePasswordField(password) {
                const isValid = this.validatePassword(password);
                const field = this.passwordInput;
                const feedback = document.getElementById('senha-feedback');
                
                if (password && !isValid && feedback) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    feedback.textContent = 'A senha não atende aos requisitos de segurança.';
                } else if (password && isValid) {
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                    if (feedback) feedback.textContent = '';
                } else {
                    field.classList.remove('is-invalid', 'is-valid');
                    if (feedback) feedback.textContent = '';
                }
            }

            validateNameField(nome) {
                const field = document.getElementById('nome');
                const feedback = document.getElementById('nome-feedback');
                
                if (!field || !feedback) return;
                
                if (nome && nome.trim().length < 2) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    feedback.textContent = 'O nome deve ter pelo menos 2 caracteres.';
                } else if (nome && nome.trim().length >= 2) {
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                    feedback.textContent = '';
                } else {
                    field.classList.remove('is-invalid', 'is-valid');
                    feedback.textContent = '';
                }
                
                this.updateSubmitButton();
            }

            validateEmailField(email) {
                const field = document.getElementById('email');
                const feedback = document.getElementById('email-feedback');
                
                if (!field || !feedback) return;
                
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (email && !emailRegex.test(email)) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    feedback.textContent = 'Por favor, insira um email válido.';
                } else if (email && emailRegex.test(email)) {
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                    feedback.textContent = '';
                } else {
                    field.classList.remove('is-invalid', 'is-valid');
                    feedback.textContent = '';
                }
                
                this.updateSubmitButton();
            }

            updateSubmitButton() {
                const nomeField = document.getElementById('nome');
                const emailField = document.getElementById('email');
                
                if (!nomeField || !emailField || !this.passwordInput || !this.submitBtn) return;
                
                const nome = nomeField.value.trim();
                const email = emailField.value.trim();
                const senha = this.passwordInput.value;
                
                const isNameValid = nome.length >= 2;
                const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                const isPasswordValid = senha.length >= 8 &&
                                      /[A-Z]/.test(senha) &&
                                      /[0-9]/.test(senha) &&
                                      /[!@#$%^&*]/.test(senha);
                
                const allValid = isNameValid && isEmailValid && isPasswordValid;
                
                this.submitBtn.disabled = !allValid;
                
                if (allValid) {
                    this.submitBtn.classList.remove('btn-secondary');
                    this.submitBtn.classList.add('btn-primary');
                } else {
                    this.submitBtn.classList.remove('btn-primary');
                    this.submitBtn.classList.add('btn-secondary');
                }
            }

            handleFormSubmit(e) {
                const nomeField = document.getElementById('nome');
                const emailField = document.getElementById('email');
                
                if (!nomeField || !emailField) {
                    e.preventDefault();
                    return;
                }
                
                const nome = nomeField.value.trim();
                const email = emailField.value.trim();
                const senha = this.passwordInput.value;
                
                // Validações finais
                let hasErrors = false;
                
                if (!nome || nome.length < 2) {
                    this.showFieldError('nome', 'O nome é obrigatório e deve ter pelo menos 2 caracteres.');
                    hasErrors = true;
                }
                
                if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    this.showFieldError('email', 'Por favor, insira um email válido.');
                    hasErrors = true;
                }
                
                if (!this.validatePassword(senha)) {
                    this.showFieldError('senha', 'A senha não atende aos requisitos de segurança.');
                    hasErrors = true;
                }
                
                if (hasErrors) {
                    e.preventDefault();
                    this.form.classList.add('shake');
                    setTimeout(() => this.form.classList.remove('shake'), 500);
                }
            }

            showFieldError(fieldId, message) {
                const field = document.getElementById(fieldId);
                const feedback = document.getElementById(`${fieldId}-feedback`);
                
                if (field && feedback) {
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    feedback.textContent = message;
                }
            }
        }

        // Inicializar quando o DOM estiver carregado
        document.addEventListener('DOMContentLoaded', function() {
            new PasswordValidator('senha');
        });
    </script>
</body>
</html>