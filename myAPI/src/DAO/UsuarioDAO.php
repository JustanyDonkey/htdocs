<?php
require_once "myAPI/src/db/Database.php";
require_once "myAPI/src/models/Usuario.php";

class UsuarioDAO
{
    /**
     * Busca um usuário pelo email
     */
    public function buscarPorEmail(string $email): ?stdClass
{
    try {
        error_log('=== BUSCAR POR EMAIL ===');
        error_log('Email: ' . $email);
        
        $query = 'SELECT 
                    idUsuario, 
                    nome, 
                    email, 
                    senha, 
                    role, 
                    data_criacao, 
                    ultimo_login 
                  FROM Usuario 
                  WHERE email = :email';
        
        error_log('Query: ' . $query);
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        
        error_log('Executando query...');
        $statement->execute();
        
        $usuario = $statement->fetch(PDO::FETCH_OBJ);
        
        error_log('Resultado: ' . ($usuario ? 'ENCONTRADO' : 'NÃO ENCONTRADO'));
        if ($usuario) {
            error_log('Dados usuário: ' . print_r($usuario, true));
        }
        
        return $usuario ?: null;
        
    } catch (PDOException $e) {
        error_log('ERRO PDO no buscarPorEmail: ' . $e->getMessage());
        error_log('Código erro: ' . $e->getCode());
        throw new Exception('Erro ao buscar usuário no banco: ' . $e->getMessage());
    }
}
    
    /**
     * Busca um usuário pelo ID
     */
    public function buscarPorId(int $idUsuario): ?stdClass
    {
        $query = 'SELECT 
                    idUsuario, 
                    nome, 
                    email, 
                    senha, 
                    role, 
                    data_criacao, 
                    ultimo_login 
                  FROM Usuario 
                  WHERE idUsuario = :id';
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        $statement->execute();
        
        $usuario = $statement->fetch(PDO::FETCH_OBJ);
        return $usuario ?: null;
    }
    
    /**
     * Atualiza a data do último login
     */
    public function atualizarUltimoLogin(int $idUsuario): bool
    {
        $query = 'UPDATE Usuario 
                  SET ultimo_login = NOW() 
                  WHERE idUsuario = :id';
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        
        return $statement->execute();
    }
    
    /**
     * Cria um novo usuário
     */
    public function criar(stdClass $usuarioData): stdClass
    {
        $query = 'INSERT INTO Usuario 
                    (nome, email, senha, role) 
                  VALUES 
                    (:nome, :email, :senha, :role)';
        
        $statement = Database::getConnection()->prepare($query);
        
        $statement->bindValue(':nome', $usuarioData->nome, PDO::PARAM_STR);
        $statement->bindValue(':email', $usuarioData->email, PDO::PARAM_STR);
        $statement->bindValue(':senha', $usuarioData->senha, PDO::PARAM_STR);
        $statement->bindValue(':role', $usuarioData->role, PDO::PARAM_STR);
        
        $statement->execute();
        
        // Obtém o ID do usuário criado
        $idUsuario = (int) Database::getConnection()->lastInsertId();
        
        // Retorna o usuário criado (sem a senha)
        $usuarioCriado = new stdClass();
        $usuarioCriado->idUsuario = $idUsuario;
        $usuarioCriado->nome = $usuarioData->nome;
        $usuarioCriado->email = $usuarioData->email;
        $usuarioCriado->role = $usuarioData->role;
        $usuarioCriado->data_criacao = date('Y-m-d H:i:s');
        $usuarioCriado->ultimo_login = null;
        
        return $usuarioCriado;
    }
    
    /**
     * Lista todos os usuários (para admin)
     */
    public function listarTodos(): array
    {
        $query = 'SELECT 
                    idUsuario, 
                    nome, 
                    email, 
                    role, 
                    data_criacao, 
                    ultimo_login 
                  FROM Usuario 
                  ORDER BY nome ASC';
        
        $statement = Database::getConnection()->query($query);
        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
    
    /**
     * Atualiza dados do usuário
     */
    public function atualizar(int $idUsuario, stdClass $dados): bool
    {
        $query = 'UPDATE Usuario 
                  SET nome = :nome, email = :email, role = :role 
                  WHERE idUsuario = :id';
        
        $statement = Database::getConnection()->prepare($query);
        
        $statement->bindValue(':nome', $dados->nome, PDO::PARAM_STR);
        $statement->bindValue(':email', $dados->email, PDO::PARAM_STR);
        $statement->bindValue(':role', $dados->role, PDO::PARAM_STR);
        $statement->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        
        return $statement->execute();
    }
    
    /**
     * Atualiza a senha do usuário
     */
    public function atualizarSenha(int $idUsuario, string $novaSenhaHash): bool
    {
        $query = 'UPDATE Usuario 
                  SET senha = :senha 
                  WHERE idUsuario = :id';
        
        $statement = Database::getConnection()->prepare($query);
        
        $statement->bindValue(':senha', $novaSenhaHash, PDO::PARAM_STR);
        $statement->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        
        return $statement->execute();
    }
    
    /**
     * Exclui um usuário
     */
    public function excluir(int $idUsuario): bool
    {
        $query = 'DELETE FROM Usuario 
                  WHERE idUsuario = :id';
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':id', $idUsuario, PDO::PARAM_INT);
        
        return $statement->execute();
    }
    
    /**
     * Verifica se email já existe (para validação)
     */
    public function emailExiste(string $email, ?int $idUsuarioIgnorar = null): bool
    {
        $query = 'SELECT COUNT(*) as total 
                  FROM Usuario 
                  WHERE email = :email';
        
        if ($idUsuarioIgnorar !== null) {
            $query .= ' AND idUsuario != :id';
        }
        
        $statement = Database::getConnection()->prepare($query);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        
        if ($idUsuarioIgnorar !== null) {
            $statement->bindValue(':id', $idUsuarioIgnorar, PDO::PARAM_INT);
        }
        
        $statement->execute();
        $resultado = $statement->fetch(PDO::FETCH_OBJ);
        
        return $resultado->total > 0;
    }
}