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

## 🧑‍💻 Instalação

1. Clone este repositório:

   ```bash
   git clone https://github.com/seu-usuario/seu-repositorio.git
   cd seu-repositorio
   ```

2. Crie o banco de dados:

   - Execute o script `criar_banco.sql` no seu MySQL:

     ```sql
     source ./database/criar_banco.sql;
     ```

3. Configure a conexão com o banco:

   - Edite `config/conexao.php` com os dados do seu MySQL.

4. Inicie um servidor local:

   - Com PHP instalado:

     ```bash
     php -S localhost:8000 -t public
     ```

   - Acesse `http://localhost:8000/login.php`

---

## 🔐 Credenciais padrão

- **Usuário Admin**
  - Email: `admin@admin.com`
  - Senha: `Admin123!`

---

## 📂 Estrutura do Projeto

```text
userregister/
├── config/
│   └── conexao.php
├── database/
│   └── criar_banco.sql
├── public/
│   ├── login.php
│   ├── cadastro.php
│   ├── painel.php
│   └── criar_usuario.php
├── src/
│   └── processa.php
├── assets/
│   ├── style.css
│   └── cookies.js
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

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

Desenvolvido com 💻 por [Seu Nome].
