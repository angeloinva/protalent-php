# ProTalent - Sistema de Conectividade entre Empresas e Talentos

## Descrição

O ProTalent é uma plataforma que conecta empresas com talentos (professores e alunos) para resolver desafios reais através de projetos inovadores. O sistema permite que empresas cadastrem seus problemas e que professores explorem essas oportunidades para desenvolver soluções com seus alunos.

## Funcionalidades

### Para Empresas
- **Cadastro de Empresa**: Formulário completo com CNPJ, dados da empresa e endereço
- **Dashboard Empresarial**: Visualização de estatísticas e gerenciamento de desafios
- **Cadastro de Desafios**: Interface intuitiva para descrever problemas e requisitos
- **Gestão de Projetos**: Acompanhamento do status dos desafios cadastrados

### Para Professores
- **Exploração de Desafios**: Lista completa de desafios disponíveis
- **Sistema de Pesquisa**: Filtros por empresa, cidade e palavras-chave
- **Dashboard com Estatísticas**: Visão geral dos desafios e empresas participantes
- **Cronograma de Projetos**: Timeline visual do processo de desenvolvimento
- **Cadastro de Interesse**: Notificação para edições futuras

### Características Gerais
- Interface moderna e responsiva
- Sistema de busca avançado
- Integração com WhatsApp para contato direto
- Validação de formulários
- Máscaras de entrada para CNPJ, telefone e CEP

## Estrutura do Sistema

### Páginas Principais
- `index.php` - Tela inicial com escolha de perfil
- `cadastro-empresa.php` - Cadastro de empresas
- `boas-vindas-empresa.php` - Confirmação de cadastro
- `empresa-dashboard.php` - Dashboard das empresas
- `cadastrar-desafio.php` - Formulário de cadastro de desafios
- `desafio-cadastrado.php` - Confirmação de desafio
- `professor-dashboard.php` - Dashboard dos professores
- `visualizar-desafio.php` - Detalhes de um desafio

### Modelos (Models)
- `Empresa.php` - Gerenciamento de empresas
- `Professor.php` - Gerenciamento de professores
- `Desafio.php` - Gerenciamento de desafios
- `User.php` - Sistema de usuários (legado)
- `Talent.php` - Sistema de talentos (legado)

### Banco de Dados
- `empresas` - Dados das empresas cadastradas
- `professores` - Dados dos professores
- `desafios` - Desafios cadastrados pelas empresas
- `users` - Usuários do sistema (legado)
- `talents` - Talentos (legado)

## Instalação

### Pré-requisitos
- XAMPP (Apache + MySQL + PHP)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior

### Passos para Instalação

1. **Clone ou baixe o projeto**
   ```bash
   git clone [url-do-repositorio]
   cd protalent-php
   ```

2. **Configure o banco de dados**
   - Abra o phpMyAdmin (http://localhost/phpmyadmin)
   - Crie um banco de dados chamado `protalent`
   - Execute o script de configuração:
   ```bash
   C:\xampp\php\php.exe setup-database.php
   ```

3. **Configure a conexão**
   - Edite `config/database.php` se necessário
   - Verifique as credenciais do banco de dados

4. **Acesse o sistema**
   - Inicie o Apache no XAMPP
   - Acesse: http://localhost/protalent-php

## Uso do Sistema

### Fluxo para Empresas
1. Acesse a página inicial
2. Clique em "Empresa"
3. Preencha o cadastro da empresa
4. Acesse o dashboard
5. Cadastre seus desafios
6. Aguarde contato dos talentos

### Fluxo para Professores
1. Acesse a página inicial
2. Clique em "Professor"
3. Explore os desafios disponíveis
4. Use os filtros de pesquisa
5. Visualize detalhes dos desafios
6. Entre em contato via WhatsApp

## Campos dos Formulários

### Cadastro de Empresa
- **CNPJ**: Validação automática e busca de dados
- **Razão Social**: Nome legal da empresa
- **Nome Fantasia**: Nome comercial (opcional)
- **Nome do Mentor**: Responsável pelo projeto
- **Email**: Contato principal
- **WhatsApp**: Contato para dúvidas
- **Endereço**: Preenchimento automático via CNPJ

### Cadastro de Desafio
- **Empresa**: Preenchido automaticamente
- **Mentor**: Sugerido do cadastro da empresa
- **WhatsApp**: Sugerido do cadastro da empresa
- **Título**: Nome do desafio
- **Descrição do Problema**: Detalhamento da situação atual
- **Pesquisado**: Se já há pesquisa prévia
- **Requisitos Específicos**: Campos condicionais

## Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5
- **Ícones**: Font Awesome
- **Validação**: JavaScript + PHP

## Estrutura de Arquivos

```
protalent-php/
├── config/
│   └── database.php
├── database/
│   └── schema.sql
├── includes/
│   ├── header.php
│   └── footer.php
├── models/
│   ├── Empresa.php
│   ├── Professor.php
│   ├── Desafio.php
│   ├── User.php
│   └── Talent.php
├── index.php
├── cadastro-empresa.php
├── boas-vindas-empresa.php
├── empresa-dashboard.php
├── cadastrar-desafio.php
├── desafio-cadastrado.php
├── professor-dashboard.php
├── visualizar-desafio.php
├── setup-database.php
└── README.md
```

## Contribuição

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature
3. Implemente suas mudanças
4. Teste o sistema
5. Envie um pull request

## Suporte

Para suporte técnico ou dúvidas:
- Abra uma issue no repositório
- Entre em contato com a equipe de desenvolvimento

## Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.

---

**ProTalent** - Conectando talentos com oportunidades reais.