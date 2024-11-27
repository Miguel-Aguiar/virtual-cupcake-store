<?php
    require_once 'Database.php';
    require_once 'CarrinhoModel.php';

    class Pedido {
        private $db;
        private $conn;
        private $table = 'pedido';

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        public function readAll($idCliente) {

            $sql = 
                "SELECT 
                    p.idPedido,
                    p.idCliente,
                    p.status,
                    
                    -- Agrega os dados dos cupcakes do pedido em uma string
                    GROUP_CONCAT(DISTINCT CONCAT(c.sabor, ' ', pc.quantidade, ' ', c.preco) SEPARATOR '|') AS cupcakes,
                    
                    -- Agrega os dados dos combos do pedido em uma string
                    GROUP_CONCAT(DISTINCT CONCAT(co.tamanho, ' ', pco.quantidade, ' ', co.preco) SEPARATOR '|') AS combos

                FROM 
                    pedido AS p

                -- Join com pedido_cupcake e cupcake
                LEFT JOIN pedido_cupcake AS pc ON p.idPedido = pc.idPedido
                LEFT JOIN cupcake AS c ON pc.idCupcake = c.idCupcake

                -- Join com pedido_combo e combo
                LEFT JOIN pedido_combo AS pco ON p.idPedido = pco.idPedido
                LEFT JOIN combo AS co ON pco.idCombo = co.idCombo

                WHERE 
                    p.idCliente = ?

                GROUP BY 
                    p.idPedido

                ORDER BY 
    				p.idPedido DESC;
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $idCliente);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Verifica se a consulta foi bem-sucedida
            if ($result && $result->num_rows > 0) {
                // Cria um array para armazenar os resultados
                $pedidos = [];
    
                // Usa fetch_assoc para obter cada linha como um array associativo
                while ($row = $result->fetch_assoc()) {
                    $pedidos[] = $row;
                }
    
                return $pedidos;
            } else {
                return []; // Retorna um array vazio se não houver resultados
            }
        }

        public function adicionarPedido($usuario){
            
            $carrinhoController = new Carrinho();
            $carrinho = $carrinhoController->exibirCarrinho($usuario);

            try{

                $db = new Database();
                $conn = $db->connect();

                $sqlPedido = "INSERT INTO pedido (idCliente) VALUES (?)";
                $stmtPedido = $conn->prepare($sqlPedido);
                $stmtPedido->bind_param("i", $usuario);
                $stmtPedido->execute();

                $idPedido = $conn->insert_id;

                foreach ($carrinho as $item) {
                    if ($item['tipo'] === 'cupcake') {
                        $sqlCupcake = "INSERT INTO pedido_cupcake (idPedido, idCupcake, quantidade) VALUES (?, ?, ?)";
                        $stmtCupcake = $conn->prepare($sqlCupcake);
                        $stmtCupcake->bind_param("iii", $idPedido, $item['id'], $item['quantidade']);
                        $stmtCupcake->execute();
                    } elseif ($item['tipo'] === 'combo') {
                        $sqlCombo = "INSERT INTO pedido_combo (idPedido, idCombo, quantidade) VALUES (?, ?, ?)";
                        $stmtCombo = $conn->prepare($sqlCombo);
                        $stmtCombo->bind_param("iii", $idPedido, $item['id'], $item['quantidade']);
                        $stmtCombo->execute();
                    }
                }

                $carrinhoController->esvaziarCarrinho($usuario);
                echo json_encode(['success' => true, 'message' => 'Pedido realizado com sucesso']);
                exit;
            } catch (Exception $e) {
                echo "Erro: " . $e->getMessage();
            }
        }

        public function pedidoEntregue($usuario) {
            // Inicia uma transação para garantir a consistência da operação
            $this->conn->begin_transaction();
            
            try {
                // Primeiro, seleciona o idPedido do último pedido do cliente
                $sqlSelect = "SELECT MAX(idPedido) AS idPedido FROM pedido WHERE idCliente = ?";
                if ($stmtSelect = $this->conn->prepare($sqlSelect)) {
                    $stmtSelect->bind_param("i", $usuario);
                    $stmtSelect->execute();
                    $result = $stmtSelect->get_result();
                    
                    // Verifica se o pedido foi encontrado
                    if ($result && $row = $result->fetch_assoc()) {
                        $idPedido = $row['idPedido']; // Recupera o idPedido
                        
                        // Se um pedido foi encontrado, atualiza o status
                        if ($idPedido) {
                            $sqlUpdate = "UPDATE pedido SET status = 'concluido' WHERE idPedido = ?";
                            if ($stmtUpdate = $this->conn->prepare($sqlUpdate)) {
                                $stmtUpdate->bind_param("i", $idPedido);
                                $stmtUpdate->execute();
                                
                                // Se a atualização for bem-sucedida, retorna o idPedido
                                echo json_encode([
                                    'success' => true, 
                                    'message' => 'Pedido entregue com sucesso', 
                                    'idPedido' => $idPedido
                                ]);
                            } else {
                                throw new Exception('Erro ao preparar a query de atualização.');
                            }
                        } else {
                            throw new Exception('Nenhum pedido encontrado para o cliente.');
                        }
                    } else {
                        throw new Exception('Erro ao recuperar o id do pedido.');
                    }
                    
                    $stmtSelect->close();
                } else {
                    throw new Exception('Erro ao preparar a query de seleção.');
                }
        
                // Commit da transação, garantindo que tudo seja aplicado corretamente
                $this->conn->commit();
                
            } catch (Exception $e) {
                // Em caso de erro, realiza o rollback
                $this->conn->rollback();
                
                // Retorna a mensagem de erro
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        

        public function closeConnection() {
            $this->db->close();
        }

    }

    $pedidos = new Pedido();
    $pedidos->closeConnection();
?>