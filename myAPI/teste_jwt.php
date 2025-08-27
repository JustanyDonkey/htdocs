<?php
// teste_inclusao_auth.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testando inclusão do MeuTokenJWT no AuthController...\n";

// Simula o mesmo require do AuthController
$tokenPath = "myAPI/src/middlewares/MeuTokenJWT.php";
echo "Caminho: $tokenPath\n";

if (file_exists($tokenPath)) {
    echo "✅ Arquivo MeuTokenJWT.php encontrado!\n";
    require_once $tokenPath;
    
    if (class_exists('App\Middlewares\MeuTokenJWT')) {
        echo "✅ Classe MeuTokenJWT carregada com sucesso!\n";
        
        // Testa instanciar
        $jwt = new App\Middlewares\MeuTokenJWT();
        echo "✅ Instância criada com sucesso!\n";
        
    } else {
        echo "❌ Classe MeuTokenJWT não encontrada após inclusão\n";
        echo "Classes carregadas: " . implode(', ', get_declared_classes()) . "\n";
    }
} else {
    echo "❌ Arquivo MeuTokenJWT.php não encontrado\n";
    echo "Tentando caminho absoluto...\n";
    
    $tokenPath = __DIR__ . "/src/middlewares/MeuTokenJWT.php";
    if (file_exists($tokenPath)) {
        echo "✅ Encontrado em: $tokenPath\n";
        require_once $tokenPath;
    } else {
        echo "❌ Não encontrado em lugar nenhum\n";
    }
}