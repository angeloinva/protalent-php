# Guia de Instalação - ProTalent

## Pré-requisitos

Antes de começar, certifique-se de ter instalado:

1. **XAMPP** (versão 8.0 ou superior)
   - Download: https://www.apachefriends.org/
   - Inclui: Apache, MySQL, PHP, phpMyAdmin

2. **Navegador web** (Chrome, Firefox, Edge, etc.)

## Passo a Passo da Instalação

### 1. Preparar o XAMPP

1. **Instale o XAMPP** seguindo as instruções do site oficial
2. **Inicie o XAMPP Control Panel**
3. **Inicie os serviços**:
   - Apache (clique em "Start")
   - MySQL (clique em "Start")
4. **Verifique se ambos estão rodando** (status verde)

### 2. Configurar o Projeto

1. **Baixe ou clone o projeto** para a pasta:
   ```
   C:\xampp\htdocs\protalent-php\
   ```

2. **Verifique se a estrutura está correta**:
   ```
   C:\xampp\htdocs\protalent-php\
   ├── config\
   ├── database\
   ├── includes\
   ├── models\
   ├── index.php
   └── outros arquivos...
   ```

### 3. Configurar o Banco de Dados

#### Opção A: Via phpMyAdmin (Recomendado)

1. **Abra o phpMyAdmin**:
   - Navegador: http://localhost/phpmyadmin
   - Usuário: `root`
   - Senha: (deixe em branco ou use a senha configurada)

2. **Crie o banco de dados**:
   - Clique em "Novo" no menu lateral
   - Nome do banco: `protalent`
   - Collation: `utf8mb4_unicode_ci`
   - Clique em "Criar"

3. **Execute o script de configuração**:
   - Abra o terminal/PowerShell
   - Navegue até a pasta do projeto:
   ```powershell
   cd C:\xampp\htdocs\protalent-php
   ```
   - Execute o script:
   ```powershell
   C:\xampp\php\php.exe setup-database.php
   ```

#### Opção B: Via Linha de Comando

1. **Abra o terminal/PowerShell**
2. **Navegue até a pasta do projeto**:
   ```powershell
   cd C:\xampp\htdocs\protalent-php
   ```
3. **Execute o script**:
   ```powershell
   C:\xampp\php\php.exe setup-database.php
   ```

### 4. Verificar a Configuração

1. **Teste a conexão**:
   - Acesse: http://localhost/protalent-php
   - Você deve ver a tela inicial do ProTalent

2. **Verifique o banco de dados**:
   - Acesse: http://localhost/phpmyadmin
   - Clique no banco `protalent`
   - Verifique se as tabelas foram criadas:
     - `empresas`
     - `professores`
     - `desafios`
     - `users`
     - `talents`

### 5. Configurações Opcionais

#### Personalizar Credenciais do Banco

Se necessário, edite o arquivo `config/database.php`:

```php
private $host = 'localhost';
private $db_name = 'protalent';
private $username = 'root';        // Seu usuário MySQL
private $password = 'sua_senha';   // Sua senha MySQL
```

#### Configurar Virtual Host (Opcional)

Para usar um domínio personalizado:

1. **Edite o arquivo** `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. **Adicione**:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/protalent-php"
       ServerName protalent.local
       <Directory "C:/xampp/htdocs/protalent-php">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
3. **Edite o arquivo hosts** (`C:\Windows\System32\drivers\etc\hosts`):
   ```
   127.0.0.1 protalent.local
   ```
4. **Reinicie o Apache**

## Testando o Sistema

### 1. Teste Básico

1. **Acesse**: http://localhost/protalent-php
2. **Verifique se aparece**:
   - Logo do ProTalent
   - Slogan "Conectando talentos"
   - Três opções: Empresa, Professor, Prestador de Serviço

### 2. Teste do Cadastro de Empresa

1. **Clique em "Empresa"**
2. **Preencha o formulário**:
   - CNPJ: `00.000.000/0000-00` (para teste)
   - Razão Social: `Empresa Teste LTDA`
   - Nome Fantasia: `Empresa Teste`
   - Nome do Mentor: `João Silva`
   - Email: `teste@empresa.com`
   - WhatsApp: `(11) 99999-9999`
3. **Clique em "Cadastrar Empresa"**
4. **Verifique se redireciona** para a tela de boas-vindas

### 3. Teste do Dashboard da Empresa

1. **Após o cadastro**, você será redirecionado para o dashboard
2. **Verifique se aparece**:
   - Nome da empresa
   - Estatísticas (0 desafios)
   - Botão "Novo Desafio"

### 4. Teste do Cadastro de Desafio

1. **Clique em "Novo Desafio"**
2. **Preencha o formulário**:
   - Título: `Sistema de Gestão de Estoque`
   - Descrição: `Precisamos de um sistema para controlar nosso estoque...`
   - Marque "Requisitos Específicos" e preencha
3. **Clique em "Cadastrar Desafio"**
4. **Verifique se aparece** a tela de confirmação

### 5. Teste do Dashboard do Professor

1. **Volte para a página inicial**
2. **Clique em "Professor"**
3. **Verifique se aparece**:
   - Estatísticas dos desafios
   - Cronograma do projeto
   - Lista de desafios disponíveis
   - Formulário de interesse

## Solução de Problemas

### Erro de Conexão com Banco

**Sintoma**: Erro "Erro de conexão" ao acessar o sistema

**Solução**:
1. Verifique se o MySQL está rodando no XAMPP
2. Confirme as credenciais em `config/database.php`
3. Teste a conexão no phpMyAdmin

### Página não Encontrada

**Sintoma**: Erro 404 ao acessar http://localhost/protalent-php

**Solução**:
1. Verifique se o Apache está rodando
2. Confirme se os arquivos estão em `C:\xampp\htdocs\protalent-php\`
3. Verifique se o nome da pasta está correto

### Erro de Permissão

**Sintoma**: Erro ao executar `setup-database.php`

**Solução**:
1. Execute o PowerShell como Administrador
2. Verifique se o PHP está no PATH ou use o caminho completo
3. Confirme se o MySQL está rodando

### Tabelas não Criadas

**Sintoma**: Script executa mas tabelas não aparecem

**Solução**:
1. Verifique se o banco `protalent` existe
2. Execute o script novamente
3. Verifique os logs de erro do MySQL

## Próximos Passos

Após a instalação bem-sucedida:

1. **Personalize o sistema** conforme suas necessidades
2. **Configure um domínio** se necessário
3. **Implemente melhorias** de segurança
4. **Adicione funcionalidades** específicas

## Suporte

Se encontrar problemas:

1. **Verifique os logs** do Apache e MySQL
2. **Consulte a documentação** do XAMPP
3. **Abra uma issue** no repositório do projeto
4. **Entre em contato** com a equipe de desenvolvimento

---

**ProTalent** - Sistema instalado com sucesso! 🎉 