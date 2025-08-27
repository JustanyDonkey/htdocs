<?php
require_once "myAPI/src/DAO/UsuarioDAO.php";
require_once "myAPI/src/middlewares/MeuTokenJWT.php";
require_once "myAPI/src/http/Response.php";
use App\Middlewares\MeuTokenJWT;
class AuthController
{
public function login(stdClass $stdLogin): never
{
    try {
        error_log('=== AUTH CONTROLLER LOGIN INICIADO ===');
        
        $usuarioDAO = new UsuarioDAO();
        error_log('Buscando usuário: ' . $stdLogin->email);
        
        $usuarioLogado = $usuarioDAO->buscarPorEmail($stdLogin->email);
        error_log('Usuário encontrado: ' . ($usuarioLogado ? 'SIM' : 'NÃO'));
        
        if (empty($usuarioLogado) || !password_verify($stdLogin->senha, $usuarioLogado->senha)) {
            error_log('CREDENCIAIS INVÁLIDAS');
            (new Response(
                success: false,
                message: 'Email ou senha inválidos',
                error: [
                    'code' => 'invalid_credentials',
                    'message' => 'Verifique suas credenciais e tente novamente'
                ],
                httpCode: 401
            ))->send();
            exit();
        }
        
        error_log('Credenciais válidas! Gerando token...');
        
        // Prepara os claims para o token JWT
        $claims = new stdClass();
        $claims->idUsuario = $usuarioLogado->idUsuario;
        $claims->nome = $usuarioLogado->nome;
        $claims->email = $usuarioLogado->email;
        $claims->role = $usuarioLogado->role;
        
        // Gera o token JWT
        $meuToken = new MeuTokenJWT();
        $token = $meuToken->gerarToken($claims);
        
        // Atualiza o último login
        $usuarioDAO->atualizarUltimoLogin($usuarioLogado->idUsuario);
        
        // Prepara os dados do usuário para a resposta (sem a senha)
        $usuarioResponse = new stdClass();
        $usuarioResponse->idUsuario = $usuarioLogado->idUsuario;
        $usuarioResponse->nome = $usuarioLogado->nome;
        $usuarioResponse->email = $usuarioLogado->email;
        $usuarioResponse->role = $usuarioLogado->role;
        $usuarioResponse->dataCriacao = $usuarioLogado->data_criacao;
        $usuarioResponse->ultimoLogin = $usuarioLogado->ultimo_login;
        
        error_log('Token gerado: ' . $token);
        error_log('Enviando resposta de sucesso...');
        
        // RESPOSTA DE SUCESSO - DENTRO DO TRY
        (new Response(
            success: true,
            message: 'Login realizado com sucesso',
            data: [
                'token' => $token,
                'usuario' => $usuarioResponse
            ],
            httpCode: 200
        ))->send();
        exit();
        
    } catch (Throwable $e) {
    error_log('=== ERRO NO LOGIN ===');
    error_log('Mensagem: ' . $e->getMessage());
    error_log('Arquivo: ' . $e->getFile());
    error_log('Linha: ' . $e->getLine());
    error_log('Trace: ' . $e->getTraceAsString());
    
    // ⚠️ ALTERE ESTA LINHA - Mostre o erro real em vez da mensagem genérica
    (new Response(
        success: false,
        message: 'Erro interno no login: ' . $e->getMessage(), // ← MOSTRA O ERRO REAL
        error: [
            'code' => 'internal_error',
            'message' => $e->getMessage() // ← MOSTRA O ERRO REAL
        ],
        httpCode: 500
    ))->send();
    exit();
    }
}
private function sendSuccessResponse($token, $usuario): never
{
    error_log('Enviando resposta de sucesso');
    (new Response(
        success: true,
        message: 'Login realizado com sucesso',
        data: [
            'token' => $token,
            'usuario' => $usuario
        ],
        httpCode: 200
    ))->send();
    exit();
}

private function sendErrorResponse(string $message, string $errorCode, int $httpCode): never
{
    error_log('Enviando resposta de erro: ' . $message);
    (new Response(
        success: false,
        message: $message,
        error: [
            'code' => $errorCode,
            'message' => $message
        ],
        httpCode: $httpCode
    ))->send();
    exit();
}
    
    public function registrar(stdClass $stdRegistro): never
    {
        try {
            $usuarioDAO = new UsuarioDAO();
            
            // Verifica se email já existe
            if ($usuarioDAO->buscarPorEmail($stdRegistro->email)) {
                (new Response(
                    success: false,
                    message: 'Email já cadastrado',
                    error: ['code' => 'email_exists', 'message' => 'Este email já está em uso'],
                    httpCode: 400
                ))->send();
                exit();
            }
            
            // Cria novo usuário
            $novoUsuario = new stdClass();
            $novoUsuario->nome = $stdRegistro->nome;
            $novoUsuario->email = $stdRegistro->email;
            $novoUsuario->senha = password_hash($stdRegistro->senha, PASSWORD_DEFAULT);
            $novoUsuario->role = $stdRegistro->role ?? 'user';
            
            // Salva no banco
            $usuarioSalvo = $usuarioDAO->criar($novoUsuario);
            
            // Gera token automaticamente
            $claims = new stdClass();
            $claims->idUsuario = $usuarioSalvo->idUsuario;
            $claims->nome = $usuarioSalvo->nome;
            $claims->email = $usuarioSalvo->email;
            $claims->role = $usuarioSalvo->role;
            
            $meuToken = new MeuTokenJWT();
            $token = $meuToken->gerarToken($claims);
            
            // Resposta de sucesso
            (new Response(
                success: true,
                message: 'Usuário criado com sucesso',
                data: [
                    'token' => $token,
                    'usuario' => $usuarioSalvo
                ],
                httpCode: 201
            ))->send();
            
        } catch (Throwable $e) {
            (new Response(
                success: false,
                message: 'Erro ao criar usuário',
                error: [
                    'code' => 'internal_error',
                    'message' => $e->getMessage()
                ],
                httpCode: 500
            ))->send();
        }
        exit();
    }
}