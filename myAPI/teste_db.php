<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost');

try {
    $pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM categorias');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'message' => 'Conexão com banco OK'
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro de banco: ' . $e->getMessage()
    ]);
}