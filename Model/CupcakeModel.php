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

        public function readAll() {
            $sql = "SELECT * FROM " . $this->table . " ORDER BY sabor ASC";
            $result = $this->conn->query($sql);
    
            // Verifica se a consulta foi bem-sucedida
            if ($result && $result->num_rows > 0) {
                // Cria um array para armazenar os resultados
                $cupcakes = [];
    
                // Usa fetch_assoc para obter cada linha como um array associativo
                while ($row = $result->fetch_assoc()) {
                    $cupcakes[] = $row;
                }
    
                return $cupcakes;
            } else {
                return []; // Retorna um array vazio se não houver resultados
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
                // Descrição do cupcake
                $descricao = "Cupcake de $massa com recheio de $recheio e cobertura de $cobertura";
        
                // Verificar se o cupcake já existe
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
                    // Cupcake já existe, retorna o id do cupcake encontrado
                    echo json_encode([
                        'success' => false,
                        'message' => 'Cupcake já existe no banco de dados.',
                        'idCupcake' => $row['idCupcake']
                    ]);
                    exit;
                }
        
                // Inserir o cupcake personalizado
                $sqlInsercao = "
                    INSERT INTO cupcake (sabor, descricao, preco) 
                    VALUES ('Personalizado', ?, 6.5)
                ";
                $stmtInsercao = $this->conn->prepare($sqlInsercao);
                $stmtInsercao->bind_param("s", $descricao);
        
                if (!$stmtInsercao->execute()) {
                    throw new Exception("Erro ao executar a query de inserção: " . $stmtInsercao->error);
                }
        
                // Pega o ID do último registro inserido
                $idCupcake = $this->conn->insert_id;
        
                // Retorna JSON de sucesso com o idCupcake
                echo json_encode([
                    'success' => true,
                    'message' => 'Cupcake personalizado adicionado com sucesso!',
                    'idCupcake' => $idCupcake
                ]);
                exit;
        
            } catch (Exception $e) {
                // Retorna JSON de erro
                echo json_encode([
                    'success' => false,
                    'message' => 'Erro: ' . $e->getMessage()
                ]);
                exit;
            }
        }

        function verificarRestricoes($restricoesCupcake, $restricoesUsuario) {
            // Converte as restrições em arrays
            $restricoesCupcakeArray = explode(',', $restricoesCupcake);
            $restricoesUsuarioArray = explode(',', $restricoesUsuario);
        
            // Remove espaços extras de cada item
            $restricoesCupcakeArray = array_map('trim', $restricoesCupcakeArray);
            $restricoesUsuarioArray = array_map('trim', $restricoesUsuarioArray);
        
            // Encontra as restrições em comum
            $restricoesComuns = array_intersect($restricoesUsuarioArray, $restricoesCupcakeArray);

            // Retorna as restrições em comum como string ou null se não houver nenhuma
            return !empty($restricoesComuns) ? implode(', ', $restricoesComuns) : null;
        }
        

        public function closeConnection() {
            $this->db->close();
        }

    }

    $cupcakes = new Cupcake();
    // $resultado = $cupcakes->readAll();

    // foreach ($resultado as $r) {
    //     echo "Id: " . $r['idCupcake'] . '<br>';
    //     echo "Sabor: " . $r['sabor'] . '<br>';
    //     echo "Descricao: " . $r['descricao'] . '<br>';
    //     echo "Preco: " . $r['preco'] . '<br>';
    // }

    $cupcakes->closeConnection();
?>