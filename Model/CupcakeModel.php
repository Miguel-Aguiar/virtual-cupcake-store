<?php
    require_once 'Database.php';

    class Cupcake {
        private $db;
        private $conn;
        private $table = 'cupcake';

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        public function __destruct() {
            $this->db->close();
        }

        public function readAll() {
            $sql = "SELECT * FROM " . $this->table . " ORDER BY sabor ASC";
            $result = $this->conn->query($sql);
    
            
            if ($result && $result->num_rows > 0) {
                
                $cupcakes = [];
    
                while ($row = $result->fetch_assoc()) {
                    $cupcakes[] = $row;
                }
    
                return $cupcakes;
            } else {
                return [];
            }
        }

        public function getCupcake($id) {
            $sql = "SELECT * FROM cupcake WHERE idCupcake IN (?);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            return $result->fetch_assoc();
        }

        public function getDescricao($id){
            $sql = "SELECT descricao FROM cupcake WHERE idCupcake = ?;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($row = $result->fetch_assoc()) {
                return $row['descricao'];
            }
        
            return null;
        }

        public function adicionarPersonalizado($massa, $recheio, $cobertura) {
            try {
                
                $descricao = "Cupcake de $massa com recheio de $recheio e cobertura de $cobertura";
        
                
                $sqlVerificacao = "
                    SELECT idCupcake 
                    FROM cupcake 
                    WHERE sabor = 'Personalizado' AND descricao = ?
                ";
                $stmtVerificacao = $this->conn->prepare($sqlVerificacao);
                $stmtVerificacao->bind_param("s", $descricao);
                $stmtVerificacao->execute();
                $resultVerificacao = $stmtVerificacao->get_result();
        
                if ($resultVerificacao && $row = $resultVerificacao->fetch_assoc()) {
                    
                    echo json_encode([
                        'success' => false,
                        'message' => 'Cupcake já existe no banco de dados.',
                        'idCupcake' => $row['idCupcake']
                    ]);
                    exit;
                }
        
                $sqlInsercao = "
                    INSERT INTO cupcake (sabor, descricao, preco) 
                    VALUES ('Personalizado', ?, 6.5)
                ";
                $stmtInsercao = $this->conn->prepare($sqlInsercao);
                $stmtInsercao->bind_param("s", $descricao);
        
                if (!$stmtInsercao->execute()) {
                    throw new Exception("Erro ao executar a query de inserção: " . $stmtInsercao->error);
                }
        
                $idCupcake = $this->conn->insert_id;
        
                echo json_encode([
                    'success' => true,
                    'message' => 'Cupcake personalizado adicionado com sucesso!',
                    'idCupcake' => $idCupcake
                ]);
                exit;
        
            } catch (Exception $e) {

                echo json_encode([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage()
                ]);
                exit;
            }
        }

        function verificarRestricoes($restricoesCupcake, $restricoesUsuario) {
            
            $restricoesCupcakeArray = explode(',', $restricoesCupcake);
            $restricoesUsuarioArray = explode(',', $restricoesUsuario);
        
            $restricoesCupcakeArray = array_map('trim', $restricoesCupcakeArray);
            $restricoesUsuarioArray = array_map('trim', $restricoesUsuarioArray);
        
            $restricoesComuns = array_intersect($restricoesUsuarioArray, $restricoesCupcakeArray);

            return !empty($restricoesComuns) ? implode(', ', $restricoesComuns) : null;
        }
        

        public function closeConnection() {
            $this->db->close();
        }

    }
?>