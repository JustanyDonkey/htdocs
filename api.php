<?php
header('Access-Control-Allow-Origin: http://localhost');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Responder imediatamente para requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}
// Caminho CORRETO - vendor está na raiz do myAPI
require_once __DIR__ . "/myAPI/vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/myAPI/src/routes/Roteador.php";

(new Roteador())->start();