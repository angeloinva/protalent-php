# 🔐 Solução de Problemas de Login - ProTalent

## Problema: Não consigo fazer login com admin@protalent.com / admin123

### 🔍 Diagnóstico Rápido

1. **Acesse o script de verificação:**
   - Abra no navegador: `http://localhost:8000/verificar-admin.php`
   - Este script irá verificar se o usuário admin existe e se a senha está correta

2. **Se o usuário não existir ou a senha estiver incorreta:**
   - Acesse: `http://localhost:8000/recriar-admin.php`
   - Este script irá recriar/atualizar o usuário admin com a senha correta

### 🛠️ Soluções Passo a Passo

#### Opção 1: Recriar Usuário Admin (Recomendado)
```
1. Abra: http://localhost:8000/recriar-admin.php
2. Aguarde a execução do script
3. Confirme que aparece "✅ Autenticação funcionando perfeitamente!"
4. Tente fazer login novamente em: http://localhost:8000/login.php
```

#### Opção 2: Reset Completo do Sistema
```
1. Abra: http://localhost:8000/reset-db.php
2. Confirme o reset do banco
3. Abra: http://localhost:8000/setup-data.php
4. Aguarde a configuração dos dados
5. Tente fazer login
```

#### Opção 3: Reinstalação Completa
```
1. Abra: http://localhost:8000/install.php
2. Siga o processo de instalação
3. Aguarde a criação das tabelas e dados
4. Tente fazer login
```

### 🔑 Credenciais Padrão
- **Email:** admin@protalent.com
- **Senha:** admin123

### ❌ Possíveis Causas do Problema

1. **Usuário não foi criado corretamente**
   - Solução: Execute `recriar-admin.php`

2. **Senha foi alterada ou corrompida**
   - Solução: Execute `recriar-admin.php`

3. **Banco de dados não está funcionando**
   - Solução: Execute `diagnostico.php`

4. **Tabelas não foram criadas**
   - Solução: Execute `install.php`

### 🔍 Verificações Adicionais

#### Verificar Conexão com Banco
- Acesse: `http://localhost:8000/test-db.php`
- Deve mostrar "✅ Conexão com banco estabelecida"

#### Verificar Status do Sistema
- Acesse: `http://localhost:8000/status.php`
- Deve mostrar todas as verificações como "✅"

#### Verificação Rápida
- Acesse: `http://localhost:8000/check.php`
- Deve mostrar "✅ Sistema funcionando corretamente"

### 📞 Se Nada Funcionar

1. **Verifique se o servidor está rodando:**
   - Abra o terminal na pasta do projeto
   - Execute: `python -m http.server 8000`
   - Acesse: `http://localhost:8000`

2. **Verifique se o MySQL está rodando:**
   - Confirme que o XAMPP/WAMP está ativo
   - Verifique se o MySQL está iniciado

3. **Verifique as credenciais do banco:**
   - Abra: `config/database.php`
   - Confirme host, username, password e dbname

### 🎯 Scripts de Ajuda

- `verificar-admin.php` - Verifica se o admin existe e testa a senha
- `recriar-admin.php` - Recria/atualiza o usuário admin
- `reset-db.php` - Reseta completamente o banco
- `setup-data.php` - Configura dados iniciais
- `install.php` - Instalação completa do sistema
- `diagnostico.php` - Diagnóstico completo do sistema
- `check.php` - Verificação rápida do sistema
- `status.php` - Status detalhado do sistema

### ✅ Ordem Recomendada de Execução

1. `verificar-admin.php` - Para diagnosticar
2. `recriar-admin.php` - Para corrigir o problema
3. `login.php` - Para testar o login

Se ainda não funcionar:
4. `reset-db.php` - Para resetar tudo
5. `setup-data.php` - Para recriar os dados
6. `login.php` - Para testar novamente 