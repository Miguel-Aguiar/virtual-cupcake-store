<?php
    require_once 'Database.php';

    class Cliente {
        private $db;
        private $conn;

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        function getEndereco($idCliente) {
            $sql = "SELECT endereco FROM cliente WHERE idCliente = ?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $idCliente);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // return $row['endereco'];
                echo json_encode([
                    'success' => true,
                    'message' => 'Endereço recuperado com sucesso.',
                    'endereco' => $row['endereco']
                ]);
            } else {
                return null;
            }
        }

        public function closeConnection() {
            $this->db->close();
        }

    }

    $cliente = new Cliente();
    $cliente->closeConnection();
?>