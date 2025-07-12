# 🔧 Solução de Problemas - ProTalent

## ❌ Problema: "Nada acontece" no instalador

### Possíveis Causas e Soluções:

#### 1. **MySQL não está rodando**
**Sintomas**: Erro de conexão ou timeout
**Solução**:
```bash
# Windows (XAMPP)
# Abra o XAMPP Control Panel e clique em "Start" no MySQL

# Windows (WAMP)
# Clique no ícone do WAMP na bandeja e aguarde ficar verde

# Linux
sudo systemctl start mysql
sudo systemctl status mysql
```

#### 2. **Credenciais incorretas**
**Sintomas**: "Access denied" ou "Authentication failed"
**Solução**:
- Verifique se o usuário e senha estão corretos
- Teste a conexão manualmente:
```bash
mysql -u root -p
# Digite a senha quando solicitado
```

#### 3. **Usuário sem permissões**
**Sintomas**: "Access denied for user"
**Solução**:
```sql
-- Conecte como root e execute:
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

#### 4. **Extensões PHP faltando**
**Sintomas**: Erro "Class 'PDO' not found"
**Solução**:
- **XAMPP**: As extensões já vêm habilitadas
- **WAMP**: Clique no ícone → PHP → Extensions → pdo_mysql
- **Manual**: Edite php.ini e descomente:
```ini
extension=pdo
extension=pdo_mysql
extension=mysqli
```

#### 5. **Arquivo schema.sql não encontrado**
**Sintomas**: "File not found" ou erro ao executar SQL
**Solução**:
- Verifique se o arquivo `database/schema.sql` existe
- Verifique permissões de leitura

## 🔍 Diagnóstico Passo a Passo

### Passo 1: Execute o Diagnóstico
```
http://localhost/protalent-php/diagnostico.php
```

### Passo 2: Teste a Conexão
```
http://localhost/protalent-php/test-db.php
```

### Passo 3: Verifique Manualmente
```bash
# Teste conexão MySQL
mysql -u root -p

# Se conectar, teste criar o banco
CREATE DATABASE protalent;
USE protalent;
SHOW TABLES;
```

## 🛠️ Soluções Específicas por Ambiente

### Windows com XAMPP

1. **Inicie os serviços**:
   - Abra XAMPP Control Panel
   - Clique "Start" em Apache e MySQL
   - Aguarde ambos ficarem verdes

2. **Verifique a porta**:
   - MySQL deve estar na porta 3306
   - Apache deve estar na porta 80 ou 8080

3. **Teste a conexão**:
```bash
C:\xampp\mysql\bin\mysql.exe -u root -p
```

### Windows com WAMP

1. **Inicie o WAMP**:
   - Clique no ícone na bandeja do sistema
   - Aguarde ficar verde

2. **Verifique PHP**:
   - Clique no ícone → PHP → Version
   - Escolha PHP 7.4 ou superior

3. **Habilite extensões**:
   - Clique no ícone → PHP → Extensions
   - Marque: pdo, pdo_mysql, mysqli

### Linux/Ubuntu

1. **Instale dependências**:
```bash
sudo apt update
sudo apt install php mysql-server php-mysql php-pdo
```

2. **Configure MySQL**:
```bash
sudo mysql_secure_installation
```

3. **Inicie serviços**:
```bash
sudo systemctl start mysql
sudo systemctl enable mysql
```

## 📝 Logs de Erro

### Verificar Logs do PHP
```bash
# Windows (XAMPP)
C:\xampp\php\logs\php_error_log

# Windows (WAMP)
C:\wamp64\logs\php_error.log

# Linux
sudo tail -f /var/log/apache2/error.log
```

### Verificar Logs do MySQL
```bash
# Windows (XAMPP)
C:\xampp\mysql\data\mysql_error.log

# Linux
sudo tail -f /var/log/mysql/error.log
```

## 🔄 Instalação Manual (Alternativa)

Se o instalador automático não funcionar:

### 1. Crie o banco manualmente
```sql
CREATE DATABASE protalent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE protalent;
```

### 2. Execute o script SQL
```bash
mysql -u root -p protalent < database/schema.sql
```

### 3. Configure o arquivo database.php
Edite `config/database.php` com suas credenciais:
```php
private $host = 'localhost';
private $db_name = 'protalent';
private $username = 'root';
private $password = 'sua_senha';
```

### 4. Teste o sistema
```
http://localhost/protalent-php/test.php
```

## 🚨 Problemas Comuns

### "Connection refused"
- MySQL não está rodando
- Porta incorreta (deve ser 3306)

### "Access denied for user"
- Senha incorreta
- Usuário não existe
- Host não permitido

### "Class 'PDO' not found"
- Extensão PDO não habilitada
- PHP não reiniciado após configuração

### "File not found"
- Arquivo schema.sql não existe
- Permissões de leitura insuficientes

### "Permission denied"
- Diretório sem permissão de escrita
- Usuário do servidor sem privilégios

## 📞 Suporte Adicional

Se ainda tiver problemas:

1. **Execute o diagnóstico completo**: `diagnostico.php`
2. **Verifique os logs de erro**
3. **Teste a conexão manualmente**
4. **Confirme as versões**: PHP 7.4+, MySQL 5.7+

### Informações para Suporte
- Sistema operacional
- Versão do PHP
- Versão do MySQL
- Servidor web (Apache/Nginx)
- Logs de erro completos
- Resultado do diagnóstico

---

**Dica**: Sempre execute o `diagnostico.php` primeiro para identificar problemas específicos! 