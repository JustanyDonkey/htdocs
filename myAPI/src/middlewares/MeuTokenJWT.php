<?php
namespace App\Middlewares; // ou o namespace que você preferir

use stdClass;
use InvalidArgumentException;
use UnexpectedValueException;
$firebasePath = __DIR__ . '/../../vendor/firebase/php-jwt/src/';
// Verifica e inclui cada arquivo necessário
if (file_exists($firebasePath . 'JWT.php')) {
    require_once $firebasePath . 'JWT.php';
    require_once $firebasePath . 'Key.php';
    require_once $firebasePath . 'SignatureInvalidException.php';
    require_once $firebasePath . 'ExpiredException.php';
    require_once $firebasePath . 'BeforeValidException.php';
} else {
    // Fallback: tenta caminho alternativo
    $firebasePath = __DIR__ . '/../vendor/firebase/php-jwt/src/';
    if (file_exists($firebasePath . 'JWT.php')) {
        require_once $firebasePath . 'JWT.php';
        require_once $firebasePath . 'Key.php';
        require_once $firebasePath . 'SignatureInvalidException.php';
        require_once $firebasePath . 'ExpiredException.php';
        require_once $firebasePath . 'BeforeValidException.php';
    } else {
        throw new Exception('Arquivos Firebase JWT não encontrados em: ' . $firebasePath);
    }
}
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use DomainException;


class MeuTokenJWT
{
    private const KEY = "x9S4q0v+V0IjvHkG20uAxaHx1ijj+q1HWjHKv+ohxp/oK+77qyXkVj/l4QYHHTF3";
    private const ALGORITHM = 'HS256';
    private const TYPE = 'JWT';
    
    public function __construct(
        private stdClass $payload = new stdClass(),
        private string $iss = 'http://localhost',
        private string $aud = 'http://localhost',
        private string $sub = 'acesso_sistema',
        private int $duration = 3600 * 24 * 30 // 30 dias
    ) {}

    public function gerarToken(stdClass $usuario): string
{
    try {
        error_log('Gerando token para usuário: ' . $usuario->email);
        
        $headers = [
            'alg' => self::ALGORITHM,
            'typ' => self::TYPE
        ];

        $payload = [
            'iss' => $this->iss,
            'aud' => $this->aud,
            'sub' => $this->sub,
            'iat' => time(),
            'exp' => time() + $this->duration,
            'nbf' => time(),
            'jti' => bin2hex(random_bytes(16)),
            // Dados do usuário
            'id' => $usuario->idUsuario,
            'name' => $usuario->nome,
            'email' => $usuario->email,
            'role' => $usuario->role
        ];

        error_log('Payload: ' . print_r($payload, true));
        
        $token = JWT::encode($payload, self::KEY, self::ALGORITHM, null, $headers);
        
        error_log('Token gerado com sucesso: ' . $token);
        
        return $token;
        
    } catch (Exception $e) {
        error_log('ERRO ao gerar token: ' . $e->getMessage());
        throw new Exception('Falha ao gerar token: ' . $e->getMessage());
    }
}

    public function validateToken(string $stringToken): bool
    {
        if (empty($stringToken)) {
            return false;
        }

        // Remove "Bearer " se presente
        $token = str_replace(["Bearer ", " "], "", $stringToken);

        // Verifica padrão básico do JWT
        $padrao = '/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/';
        if (!preg_match($padrao, $token)) {
            return false;
        }

        try {
            $payloadValido = JWT::decode($token, new Key(self::KEY, self::ALGORITHM));
            $this->setPayload($payloadValido);
            return true;
        } catch (
            SignatureInvalidException |
            ExpiredException |
            InvalidArgumentException |
            DomainException |
            UnexpectedValueException $e
        ) {
            error_log('Erro JWT: ' . $e->getMessage());
            return false;
        }
    }

    public function getPayload(): stdClass
    {
        return $this->payload;
    }

    public function setPayload(stdClass $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function getUsuarioFromToken(): stdClass
    {
        $usuario = new stdClass();
        $payload = $this->getPayload();
        
        $usuario->idUsuario = $payload->id ?? null;
        $usuario->nome = $payload->name ?? '';
        $usuario->email = $payload->email ?? '';
        $usuario->role = $payload->role ?? 'user';
        
        return $usuario;
    }
}