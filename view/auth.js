
    class AuthService {
    constructor() {
        // Altere para a URL e porta corretas
        this.apiBaseUrl = 'http://localhost:8000'; // ou a porta que você está usando
        this.tokenKey = 'jwt_token';
        this.userKey = 'user_data';
    }

    async login(email, senha) {
        try {
            console.log('Tentando login em:', `${this.apiBaseUrl}/auth/login`);
            
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

            console.log('Status da resposta:', response.status);
            
            const data = await response.json();
            console.log('Resposta completa:', data);

            if (data.success && data.data && data.data.token) {
                this.setToken(data.data.token);
                this.setUser(data.data.usuario);
                
                return {
                    success: true,
                    message: 'Login realizado com sucesso! ✅'
                };
            } else {
                return {
                    success: false,
                    message: data.message || 'Erro no login. Verifique suas credenciais. ❌'
                };
            }
        } catch (error) {
            console.error('Erro completo:', error);
            return {
                success: false,
                message: 'Erro de conexão. Verifique se a API está rodando. 🌐'
            };
        }
    }

    setToken(token) {
        localStorage.setItem(this.tokenKey, token);
        console.log('Token salvo no localStorage');
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
    messageDiv.style.display = 'block';
}

function setLoading(loading) {
    const btn = document.getElementById('loginBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');
    
    btn.disabled = loading;
    if (btnText) btnText.style.display = loading ? 'none' : 'inline';
    if (btnLoading) btnLoading.style.display = loading ? 'inline-block' : 'none';
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página carregada - verificando login...');
    
    // Verifica se já está logado
    if (authService.isLoggedIn()) {
        console.log('Usuário já logado, redirecionando...');
        window.location.href = 'dashboard.html';
        return;
    }

    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const senha = document.getElementById('senha').value;

        console.log('Tentando login com:', { email, senha });
        
        setLoading(true);
        showMessage('Autenticando...', '');

        const result = await authService.login(email, senha);
        console.log('Resultado do login:', result);

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