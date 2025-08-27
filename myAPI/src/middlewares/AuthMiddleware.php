<?php
namespace App\Middlewares;
require_once __DIR__ . "/MeuTokenJWT.php";
require_once "myAPI/src/http/Response.php";
use Exception;
use Throwable;
class AuthMiddleware
{
    /**
     * Valida o JSON de login e retorna os dados
     */
    public function validarJsonLogin(string $json): stdClass
{
    try {
        error_log('Validando JSON login: ' . $json);
        
        $data = json_decode($json);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON inválido: ' . json_last_error_msg());
        }
        
        if (!isset($data->email) || !isset($data->senha)) {
            throw new Exception('Campos obrigatórios faltando: email e senha são necessários');
        }
        
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Formato de email inválido');
        }
        
        error_log('JSON validado com sucesso');
        return $data;
        
    } catch (Exception $e) {
        error_log('Erro na validação JSON: ' . $e->getMessage());
        throw new Exception('Erro na validação dos dados de login: ' . $e->getMessage());
    }
}
    
    /**
     * Verifica e valida o token JWT
     * Retorna os dados do usuário se o token for válido
     */
   public function verificarToken(): \stdClass
    {
        try {
            error_log('Verificando token...');
            
            $headers = getallheaders();
            error_log('Headers: ' . print_r($headers, true));
            
            // Verifica se o token existe
            if (!isset($headers['Authorization'])) {
                error_log('Token não encontrado nos headers');
                throw new Exception('Token não fornecido', 401);
            }
            
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            error_log('Token recebido: ' . $token);
            
            // ✅ AGORA DEVE ENCONTRAR A CLASSE
            $meuToken = new MeuTokenJWT();
            error_log('Instância MeuTokenJWT criada');
            
            if (!$meuToken->validateToken($token)) {
                error_log('Token inválido');
                throw new Exception('Token inválido ou expirado', 401);
            }
            
            error_log('Token válido!');
            return $meuToken->getUsuarioFromToken();
            
        } catch (Throwable $e) {
            error_log('ERRO no verificarToken: ' . $e->getMessage());
            throw new Exception('Erro na verificação do token: ' . $e->getMessage(), 401);
        }
    }

    public function verificarAdmin(): stdClass
    {
        $usuario = $this->verificarToken();
        
        if ($usuario->role !== 'admin') {
            (new Response(
                success: false,
                message: 'Acesso não autorizado',
                error: [
                    'code' => 'insufficient_permissions',
                    'message' => 'Permissões de administrador necessárias'
                ],
                httpCode: 403
            ))->send();
            exit();
        }
        
        return $usuario;
    }
    
    /**
     * Extrai o token do cabeçalho Authorization
     */
    private function getTokenFromHeader(): string
    {
        $headers = null;
        
        // Obtém todos os cabeçalhos
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }
        
        // Busca o cabeçalho Authorization
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        // Remove "Bearer " do token
        return str_replace(["Bearer ", " "], "", $authHeader);
    }
    
    /**
     * Valida dados de registro de usuário
     */
    public function validarRegistro($requestBody): stdClass
    {
        $stdRegistro = json_decode($requestBody);
        
        // Verifica se o JSON é válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            (new Response(
                success: false,
                message: 'Erro no formato JSON',
                error: [
                    'code' => 'invalid_json',
                    'message' => 'O corpo da requisição deve ser um JSON válido'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        // Valida nome
        if (!isset($stdRegistro->nome) || empty(trim($stdRegistro->nome))) {
            (new Response(
                success: false,
                message: 'Nome obrigatório',
                error: [
                    'code' => 'missing_name',
                    'message' => 'O campo nome é obrigatório'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        if (strlen(trim($stdRegistro->nome)) < 2) {
            (new Response(
                success: false,
                message: 'Nome muito curto',
                error: [
                    'code' => 'short_name',
                    'message' => 'O nome deve ter pelo menos 2 caracteres'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        // Valida email
        if (!isset($stdRegistro->email) || empty(trim($stdRegistro->email))) {
            (new Response(
                success: false,
                message: 'Email obrigatório',
                error: [
                    'code' => 'missing_email',
                    'message' => 'O campo email é obrigatório'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        if (!filter_var($stdRegistro->email, FILTER_VALIDATE_EMAIL)) {
            (new Response(
                success: false,
                message: 'Email inválido',
                error: [
                    'code' => 'invalid_email',
                    'message' => 'O formato do email é inválido'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        // Valida senha
        if (!isset($stdRegistro->senha) || empty(trim($stdRegistro->senha))) {
            (new Response(
                success: false,
                message: 'Senha obrigatória',
                error: [
                    'code' => 'missing_password',
                    'message' => 'O campo senha é obrigatório'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        if (strlen($stdRegistro->senha) < 6) {
            (new Response(
                success: false,
                message: 'Senha muito curta',
                error: [
                    'code' => 'short_password',
                    'message' => 'A senha deve ter pelo menos 6 caracteres'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        // Valida role se fornecido
        if (isset($stdRegistro->role) && !in_array($stdRegistro->role, ['admin', 'user'])) {
            (new Response(
                success: false,
                message: 'Tipo de usuário inválido',
                error: [
                    'code' => 'invalid_role',
                    'message' => 'O tipo de usuário deve ser admin ou user'
                ],
                httpCode: 400
            ))->send();
            exit();
        }
        
        return $stdRegistro;
    }
}