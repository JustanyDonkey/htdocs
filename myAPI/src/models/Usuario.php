<?php
class Usuario implements JsonSerializable
{
    public function __construct(
        private ?int $idUsuario = null,
        private string $nome = "",
        private string $email = "",
        private string $senha = "",
        private string $role = "user",
        private ?string $dataCriacao = null,
        private ?string $ultimoLogin = null
    ) {}
    
    public function jsonSerialize(): array
    {
        return [
            'idUsuario' => $this->idUsuario,
            'nome' => $this->nome,
            'email' => $this->email,
            'role' => $this->role,
            'dataCriacao' => $this->dataCriacao,
            'ultimoLogin' => $this->ultimoLogin
        ];
    }
    
    // Getters
    public function getIdUsuario(): ?int { return $this->idUsuario; }
    public function getNome(): string { return $this->nome; }
    public function getEmail(): string { return $this->email; }
    public function getSenha(): string { return $this->senha; }
    public function getRole(): string { return $this->role; }
    public function getDataCriacao(): ?string { return $this->dataCriacao; }
    public function getUltimoLogin(): ?string { return $this->ultimoLogin; }
    
    // Setters
    public function setIdUsuario(int $idUsuario): self { 
        $this->idUsuario = $idUsuario; 
        return $this; 
    }
    
    public function setNome(string $nome): self { 
        $this->nome = $nome; 
        return $this; 
    }
    
    public function setEmail(string $email): self { 
        $this->email = $email; 
        return $this; 
    }
    
    public function setSenha(string $senha): self { 
        $this->senha = password_hash($senha, PASSWORD_DEFAULT); 
        return $this; 
    }
    
    public function setRole(string $role): self { 
        $this->role = $role; 
        return $this; 
    }
    
    public function setDataCriacao(string $dataCriacao): self { 
        $this->dataCriacao = $dataCriacao; 
        return $this; 
    }
    
    public function setUltimoLogin(string $ultimoLogin): self { 
        $this->ultimoLogin = $ultimoLogin; 
        return $this; 
    }
    
    // Método para verificar senha
    public function verificarSenha(string $senha): bool
    {
        return password_verify($senha, $this->senha);
    }
}