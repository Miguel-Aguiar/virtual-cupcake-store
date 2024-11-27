<?php
class Database {
    private $host = "localhost";
    private $usuario = "root";
    private $senha = "";
    private $banco = "cupcakesstore";
    private $conn;

    // Método para obter a conexão com o banco de dados
    public function connect() {
        // Verifica se já existe uma conexão ativa
        if ($this->conn == null) {
            $this->conn = new mysqli($this->host, $this->usuario, $this->senha, $this->banco);
            // Testa a conexão
            if ($this->conn->connect_error) {
                die("Conexão falhou: " . $this->conn->connect_error);
            }
        }

        return $this->conn;
    }

    // Método para fechar a conexão
    public function close() {
        if ($this->conn != null) {
            $this->conn->close();
            $this->conn = null;
        }
    }
}

?>