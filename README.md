# Sistema de Cadastro e Login com Painel Administrativo

Este é um sistema simples de cadastro e login de usuários com painel administrativo, feito em PHP com MySQL. O projeto inclui controle de acesso por tipo de usuário (`admin` e `user`), proteção contra múltiplas tentativas de login e uma janela de consentimento de cookies.

---

## ⚙️ Funcionalidades

- Cadastro de novos usuários
- Login com verificação de senha (com hash)
- Sistema de autenticação por sessão
- Painel administrativo com listagem, edição e remoção de usuários
- Controle de tentativas de login com bloqueio temporário
- Consentimento de cookies
- Proteção CSRF no formulário de login

---

## 🛠️ Tecnologias

- **Linguagem:** PHP (sem frameworks)
- **Banco de Dados:** MySQL
- **Frontend:** HTML + CSS + JavaScript básico
- **Outros:** Sessões PHP, `password_hash()`, Prepared Statements (MySQLi)

---

## 🔐 Credenciais padrão

- **Usuário Admin**
  - Email: `adm@adm.com`
  - Senha: `Admin123!`

---

## 📂 Estrutura do Projeto

```text
userregister/
├── config/
│   └── conexao.php
├── controllers/
│   └── processa.php
├── database/
│   └── criar_banco.sql
│   └── criar_usuario.php
│   └── deletar_usuario.php
│   └── editar_usuario.php
├── logs/
│   └── cadastros.log
│   └── erros.log
│   └── logins.log
│   └── tentativas.log
├── public/
│   └── login.php
│   └── cadastro.php
│   └── painel.php
│   └── sair.php
│   └── style.css

```

---

## 🍪 Consentimento de Cookies

O sistema exibe uma janela de confirmação de cookies, solicitando o aceite do usuário antes de iniciar uma sessão de forma completa. Isso melhora a experiência do usuário e respeita boas práticas de privacidade.

---

## ✅ Melhorias Futuras

- Reset de senha por e-mail
- Validação avançada de formulário
- Logs de login
- Testes automatizados


---

Desenvolvido por Gabriel Francisco, Pamella Sotomayer e Renan Ewbank.
