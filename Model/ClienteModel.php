<?php
    require_once 'Database.php';

    class Cliente {
        private $db;
        private $conn;

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        public function __destruct() {
            $this->db->close();
        }

        function getEndereco($idCliente) {
            $sql = "SELECT endereco FROM cliente WHERE idCliente = ?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $idCliente);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Endereço recuperado com sucesso.',
                    'endereco' => $row['endereco']
                ]);
            } else {
                return null;
            }
        }

        public function redefinirSenha($email, $telefone) {
            $sql = "SELECT idCliente FROM cliente WHERE email = ? AND numeroContato = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $email, $telefone);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($row = $result->fetch_assoc()) {
                return $row['idCliente'];
            } else {
                header('Location: ../View/redefinirSenha.html?acao=redefinir_erro');  
            }
        }

        public function atualizarSenha($nova_senha, $user_id){
            $query = $this->conn->prepare("UPDATE cliente SET senha = ? WHERE idCliente = ?");
            $query->execute([$nova_senha, $user_id]);

            header('Location: ../View/login.html?acao=senha_alterada');
        }
        

        public function closeConnection() {
            $this->db->close();
        }

    }
?>