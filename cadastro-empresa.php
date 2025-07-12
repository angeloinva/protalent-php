<?php
session_start();
require_once 'config/database.php';
require_once 'models/Empresa.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $empresa = new Empresa($db);
    
    // Validar CNPJ
    $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']);
    if (strlen($cnpj) != 14) {
        $error = 'CNPJ inválido!';
    } else {
        // Verificar se empresa já existe
        $existing = $empresa->readByEmail($_POST['email']);
        if ($existing) {
            $error = 'Já existe uma empresa cadastrada com este email!';
        } else {
            $empresa->cnpj = $_POST['cnpj'];
            $empresa->razao_social = $_POST['razao_social'];
            $empresa->nome_fantasia = $_POST['nome_fantasia'];
            $empresa->nome_mentor = $_POST['nome_mentor'];
            $empresa->email = $_POST['email'];
            $empresa->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $empresa->whatsapp = $_POST['whatsapp'];
            $empresa->cep = $_POST['cep'];
            $empresa->endereco = $_POST['endereco'];
            $empresa->numero = $_POST['numero'];
            $empresa->complemento = $_POST['complemento'];
            $empresa->bairro = $_POST['bairro'];
            $empresa->cidade = $_POST['cidade'];
            $empresa->estado = $_POST['estado'];
            
            if ($empresa->create()) {
                $_SESSION['empresa_id'] = $empresa->id;
                $_SESSION['empresa_nome'] = $empresa->nome_fantasia ?: $empresa->razao_social;
                $_SESSION['user_type'] = 'empresa';
                header("Location: boas-vindas-empresa.php");
                exit();
            } else {
                $error = 'Erro ao cadastrar empresa!';
            }
        }
    }
}

include 'includes/header.php';
?>

<style>
.cadastro-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    text-align: center;
}

.form-section {
    padding: 40px 0;
}

.form-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.form-title {
    color: #333;
    margin-bottom: 30px;
    text-align: center;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #555;
    margin-bottom: 8px;
}

.form-control {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-cadastrar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-cadastrar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    color: white;
}

.address-fields {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-top: 20px;
}

.loading {
    display: none;
    color: #667eea;
    font-size: 0.9rem;
    margin-top: 5px;
}
</style>

<div class="cadastro-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Cadastro de Empresa</h1>
                <p class="lead">Preencha os dados da sua empresa para começar a cadastrar desafios</p>
            </div>
        </div>
    </div>
</div>

<div class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-card">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" id="empresaForm">
                        <h3 class="form-title">Dados da Empresa</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cnpj" class="form-label">CNPJ *</label>
                                    <input type="text" class="form-control" id="cnpj" name="cnpj" 
                                           placeholder="00.000.000/0000-00" required>
                                    <div class="loading" id="cnpjLoading">
                                        <i class="fas fa-spinner fa-spin"></i> Buscando dados do CNPJ...
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="razao_social" class="form-label">Razão Social *</label>
                                    <input type="text" class="form-control" id="razao_social" name="razao_social" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                                    <input type="text" class="form-control" id="nome_fantasia" name="nome_fantasia">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome_mentor" class="form-label">Nome do Mentor *</label>
                                    <input type="text" class="form-control" id="nome_mentor" name="nome_mentor" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Senha *</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Mínimo 6 caracteres" required minlength="6">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">Confirmar Senha *</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Digite a senha novamente" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                                           placeholder="(11) 99999-9999">
                                </div>
                            </div>
                        </div>
                        
                        <div class="address-fields">
                            <h5 class="mb-3">Endereço da Empresa</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cep" class="form-label">CEP</label>
                                        <input type="text" class="form-control" id="cep" name="cep" 
                                               placeholder="00000-000">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="endereco" class="form-label">Endereço</label>
                                        <input type="text" class="form-control" id="endereco" name="endereco">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="numero" class="form-label">Número</label>
                                        <input type="text" class="form-control" id="numero" name="numero">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="complemento" class="form-label">Complemento</label>
                                        <input type="text" class="form-control" id="complemento" name="complemento">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bairro" class="form-label">Bairro</label>
                                        <input type="text" class="form-control" id="bairro" name="bairro">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="cidade" class="form-label">Cidade</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-control" id="estado" name="estado">
                                            <option value="">Selecione...</option>
                                            <option value="AC">Acre</option>
                                            <option value="AL">Alagoas</option>
                                            <option value="AP">Amapá</option>
                                            <option value="AM">Amazonas</option>
                                            <option value="BA">Bahia</option>
                                            <option value="CE">Ceará</option>
                                            <option value="DF">Distrito Federal</option>
                                            <option value="ES">Espírito Santo</option>
                                            <option value="GO">Goiás</option>
                                            <option value="MA">Maranhão</option>
                                            <option value="MT">Mato Grosso</option>
                                            <option value="MS">Mato Grosso do Sul</option>
                                            <option value="MG">Minas Gerais</option>
                                            <option value="PA">Pará</option>
                                            <option value="PB">Paraíba</option>
                                            <option value="PR">Paraná</option>
                                            <option value="PE">Pernambuco</option>
                                            <option value="PI">Piauí</option>
                                            <option value="RJ">Rio de Janeiro</option>
                                            <option value="RN">Rio Grande do Norte</option>
                                            <option value="RS">Rio Grande do Sul</option>
                                            <option value="RO">Rondônia</option>
                                            <option value="RR">Roraima</option>
                                            <option value="SC">Santa Catarina</option>
                                            <option value="SP">São Paulo</option>
                                            <option value="SE">Sergipe</option>
                                            <option value="TO">Tocantins</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-cadastrar btn-lg">
                                <i class="fas fa-check me-2"></i>Cadastrar Empresa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Máscara para CNPJ
document.getElementById('cnpj').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/, '$1.$2');
    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
    value = value.replace(/(\d{4})(\d)/, '$1-$2');
    e.target.value = value;
});

// Máscara para WhatsApp
document.getElementById('whatsapp').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{2})(\d)/, '($1) $2');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    e.target.value = value;
});

// Máscara para CEP
document.getElementById('cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    value = value.replace(/^(\d{5})(\d)/, '$1-$2');
    e.target.value = value;
});

// Buscar dados do CNPJ
document.getElementById('cnpj').addEventListener('blur', function(e) {
    const cnpj = e.target.value.replace(/\D/g, '');
    if (cnpj.length === 14) {
        document.getElementById('cnpjLoading').style.display = 'block';
        
        // Simular busca de CNPJ (em produção, usar API real)
        setTimeout(() => {
            // Aqui você pode integrar com uma API real de consulta de CNPJ
            // Por enquanto, vamos apenas simular
            document.getElementById('cnpjLoading').style.display = 'none';
            
            // Exemplo de preenchimento automático (remover em produção)
            if (cnpj === '00000000000000') {
                document.getElementById('razao_social').value = 'Empresa Exemplo LTDA';
                document.getElementById('nome_fantasia').value = 'Empresa Exemplo';
                document.getElementById('cep').value = '01234-567';
                document.getElementById('endereco').value = 'Rua Exemplo, 123';
                document.getElementById('bairro').value = 'Centro';
                document.getElementById('cidade').value = 'São Paulo';
                document.getElementById('estado').value = 'SP';
            }
        }, 2000);
    }
});

// Validação de senha
document.getElementById('confirm_password').addEventListener('input', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = e.target.value;
    
    if (password !== confirmPassword) {
        e.target.setCustomValidity('As senhas não coincidem');
    } else {
        e.target.setCustomValidity('');
    }
});

// Validação do formulário
document.getElementById('empresaForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('As senhas não coincidem!');
        document.getElementById('confirm_password').focus();
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('A senha deve ter pelo menos 6 caracteres!');
        document.getElementById('password').focus();
        return false;
    }
});
</script>

<?php include 'includes/footer.php'; ?> 