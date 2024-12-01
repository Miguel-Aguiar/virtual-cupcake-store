<?php
class Database {
    private $host = "localhost";
    private $usuario = "root";
    private $senha = "";
    private $banco = "cupcakesstore";
    private $conn;

    public function connect() {
        
        if ($this->conn == null) {
            $this->conn = new mysqli($this->host, $this->usuario, $this->senha, $this->banco);
            
            if ($this->conn->connect_error) {
                die("Conexão falhou: " . $this->conn->connect_error);
            }
        }

        return $this->conn;
    }

    public function close() {
        if ($this->conn != null) {
            $this->conn->close();
            $this->conn = null;
        }
    }
}

?>
