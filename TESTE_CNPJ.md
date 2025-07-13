# Teste do Preenchimento Automático CNPJ

## Como Testar

1. Acesse: `http://localhost/protalent-php/cadastro-empresa.php`

2. Abra o Console do navegador (F12 → Console)

3. Teste com os seguintes CNPJs:

### CNPJs de Teste:

- **00000000000000** → Empresa Exemplo LTDA
- **12345678000190** → Tech Solutions LTDA  
- **98765432000110** → Inovação Digital ME
- **Qualquer CNPJ válido** → Dados genéricos

### Passos para Teste:

1. Digite um dos CNPJs acima no campo CNPJ
2. Clique fora do campo (evento blur)
3. Aguarde 1 segundo
4. Verifique se os campos foram preenchidos automaticamente
5. Verifique os logs no console

### O que Deve Acontecer:

- Campo CNPJ deve aplicar máscara automaticamente
- Loading deve aparecer por 1 segundo
- Campos devem ser preenchidos:
  - Razão Social
  - Nome Fantasia
  - CEP
  - Endereço
  - Bairro
  - Cidade
  - Estado

### Logs no Console:

O sistema agora tem logs detalhados que mostram:
- Quando o script é carregado
- Quando o campo CNPJ é encontrado
- Quando o evento blur é disparado
- Quais dados estão sendo preenchidos
- Se cada campo foi encontrado e preenchido

### Se Não Funcionar:

1. Verifique se há erros no console
2. Verifique se o campo CNPJ tem o ID correto: `cnpj`
3. Verifique se todos os campos têm os IDs corretos
4. Teste com diferentes CNPJs 