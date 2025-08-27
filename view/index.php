<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema</title>
    <style>
        /* Reset e Estilos Gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Container Principal */
        .login-container {
            width: 100%;
            max-width: 400px;
        }

        /* Caixa de Login */
        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        /* Formulário */
        .login-form {
            margin-bottom: 1.5rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #666;
        }

        /* Botão */
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .login-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Mensagens */
        .message {
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 1rem;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        /* Links */
        .login-links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-links a {
            color: #667eea;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .login-links a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }

        /* Loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsivo */
        @media (max-width: 480px) {
            .login-box {
                padding: 1.5rem;
            }
            
            .login-header h2 {
                font-size: 24px;
            }
            
            .input-group input {
                padding: 12px;
            }
            
            .login-btn {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h2>🔐 Acesso ao Sistema</h2>
                <p>Entre com suas credenciais para continuar</p>
            </div>

            <form id="loginForm" class="login-form">
                <div class="input-group">
                    <label for="email">📧 E-mail</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        placeholder="seu@email.com"
                        autocomplete="email"
                    >
                </div>
                
                <div class="input-group">
                    <label for="senha">🔒 Senha</label>
                    <div class="password-container">
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            required 
                            placeholder="Sua senha"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            👁️
                        </button>
                    </div>
                </div>
                
                <button type="submit" id="loginBtn" class="login-btn">
                    <span id="btnText">Entrar no Sistema</span>
                    <span id="btnLoading" class="loading" style="display: none;"></span>
                </button>
            </form>
            
            <div id="message" class="message"></div>
            
            <div class="login-links">
                <a href="#" id="registerLink">📝 Criar conta</a>
                <a href="#" id="forgotLink">❓ Esqueci a senha</a>
            </div>
        </div>
    </div>

    <script>
        class AuthService {
            constructor() {
                this.apiBaseUrl = 'http://localhost';
                this.tokenKey = 'jwt_token';
                this.userKey = 'user_data';
            }

            async login(email, senha) {
                try {
                    console.log('Tentando login...');
                    const response = await fetch(`${this.apiBaseUrl}/auth/login`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            email: email,
                            senha: senha
                        })
                    });

                    const data = await response.json();
                    console.log('Resposta da API:', data);

                    if (data.success) {
                        this.setToken(data.data.token);
                        this.setUser(data.data.usuario);
                        
                        return {
                            success: true,
                            message: 'Login realizado com sucesso! ✅'
                        };
                    } else {
                        return {
                            success: false,
                            message: data.message || 'Erro no login ❌'
                        };
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    return {
                        success: false,
                        message: 'Erro de conexão. Verifique a API. 🌐'
                    };
                }
            }

            setToken(token) {
                localStorage.setItem(this.tokenKey, token);
                console.log('Token salvo:', token);
            }

            getToken() {
                return localStorage.getItem(this.tokenKey);
            }

            setUser(user) {
                localStorage.setItem(this.userKey, JSON.stringify(user));
            }

            getUser() {
                const user = localStorage.getItem(this.userKey);
                return user ? JSON.parse(user) : null;
            }

            isLoggedIn() {
                return this.getToken() !== null;
            }

            logout() {
                localStorage.removeItem(this.tokenKey);
                localStorage.removeItem(this.userKey);
                window.location.href = 'index.html';
            }
        }

        const authService = new AuthService();

        function togglePassword() {
            const senhaInput = document.getElementById('senha');
            const toggleBtn = document.querySelector('.toggle-password');
            
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                toggleBtn.textContent = '🙈';
            } else {
                senhaInput.type = 'password';
                toggleBtn.textContent = '👁️';
            }
        }

        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
        }

        function setLoading(loading) {
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            
            btn.disabled = loading;
            btnText.style.display = loading ? 'none' : 'inline';
            btnLoading.style.display = loading ? 'inline-block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const messageDiv = document.getElementById('message');

            if (authService.isLoggedIn()) {
                window.location.href = 'dashboard.html';
                return;
            }

            loginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const email = document.getElementById('email').value;
                const senha = document.getElementById('senha').value;

                setLoading(true);
                showMessage('', '');

                const result = await authService.login(email, senha);

                if (result.success) {
                    showMessage(result.message, 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'dashboard.html';
                    }, 1500);
                } else {
                    showMessage(result.message, 'error');
                    setLoading(false);
                }
            });

            // Enter para submit
            document.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    loginForm.dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</body>
</html>