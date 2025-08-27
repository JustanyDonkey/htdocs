<?php
// Caminho CORRETO - vendor está na raiz do myAPI
require_once __DIR__ . "/myAPI/vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/myAPI/src/routes/Roteador.php";

(new Roteador())->start();