<?php
    require_once 'Database.php';

    class Carrinho {
        private $db;
        private $conn;

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        public function __destruct() {
            $this->db->close();
        }

        public function exibirCarrinho($userId) {
            $carrinhoId = $this->getCarrinho($userId);
        
            
            $sqlCupcakes = "
                SELECT
                    'cupcake' AS tipo,
                    carrinho_cupcake.idCupcake AS id,
                    carrinho_cupcake.quantidade,
                    cupcake.preco,
                    cupcake.sabor,
                    cupcake.restricoes
                FROM
                    carrinho_cupcake
                JOIN
                    cupcake
                ON
                    carrinho_cupcake.idCupcake = cupcake.idCupcake
                WHERE
                    carrinho_cupcake.idCarrinho = $carrinhoId;
            ";
        
            $sqlCombos = "
                SELECT
                    'combo' AS tipo,
                    carrinho_combo.idCombo AS id,
                    carrinho_combo.quantidade,
                    combo.preco,
                    combo.tamanho
                FROM
                    carrinho_combo
                JOIN
                    combo
                ON
                    carrinho_combo.idCombo = combo.idCombo
                WHERE
                    carrinho_combo.idCarrinho = $carrinhoId;
            ";
        
            $resultCupcakes = $this->conn->query($sqlCupcakes);
            $resultCombos = $this->conn->query($sqlCombos);
        
            
            $itens = [];
        
            if ($resultCupcakes && $resultCupcakes->num_rows > 0) {
                while ($row = $resultCupcakes->fetch_assoc()) {
                    $itens[] = $row;
                }
            }
        
            if ($resultCombos && $resultCombos->num_rows > 0) {
                while ($row = $resultCombos->fetch_assoc()) {
                    $itens[] = $row;
                }
            }
        
            return $itens;
        }
        

        public function adicionarAoCarrinho($userId, $itemId, $tipo, $operacao = 'adicionar') {
            try {
                
                if (!in_array($tipo, ['cupcake', 'combo'])) {
                    throw new Exception("Tipo inválido: $tipo");
                }
        
                if (!in_array($operacao, ['adicionar', 'diminuir'])) {
                    throw new Exception("Operação inválida: $operacao");
                }
                
                
                $carrinhoId = $this->getCarrinho($userId);
                if (!$carrinhoId) {
                    throw new Exception("Carrinho não encontrado para o usuário: $userId");
                }
        
                
                $tabela = $tipo === 'cupcake' ? 'carrinho_cupcake' : 'carrinho_combo';
                $colunaId = $tipo === 'cupcake' ? 'idCupcake' : 'idCombo';
        
                
                $sql = "SELECT $colunaId, quantidade FROM $tabela WHERE idCarrinho = ? AND $colunaId = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $carrinhoId, $itemId);
                $stmt->execute();
                $result = $stmt->get_result();
        
                if ($result->num_rows > 0) {
                    
                    $row = $result->fetch_assoc();
                    $quantidadeAtual = $row['quantidade'];
        
                    if ($operacao === 'adicionar') {
                        $novaQuantidade = $quantidadeAtual + 1;
                    } else {
                        $novaQuantidade = max(0, $quantidadeAtual - 1);
                    }
        
                    if ($novaQuantidade > 0) {
                        
                        $sqlUpdate = "UPDATE $tabela SET quantidade = ? WHERE idCarrinho = ? AND $colunaId = ?";
                        $stmtUpdate = $this->conn->prepare($sqlUpdate);
                        $stmtUpdate->bind_param("iii", $novaQuantidade, $carrinhoId, $itemId);
                        $stmtUpdate->execute();
                    } else {
                        
                        $sqlDelete = "DELETE FROM $tabela WHERE idCarrinho = ? AND $colunaId = ?";
                        $stmtDelete = $this->conn->prepare($sqlDelete);
                        $stmtDelete->bind_param("ii", $carrinhoId, $itemId);
                        $stmtDelete->execute();
                    }
                } else {
                    if ($operacao === 'adicionar') {
                        
                        $sqlInsert = "INSERT INTO $tabela (idCarrinho, $colunaId, quantidade) VALUES (?, ?, 1)";
                        $stmtInsert = $this->conn->prepare($sqlInsert);
                        $stmtInsert->bind_param("ii", $carrinhoId, $itemId);
                        $stmtInsert->execute();
                    }
                }
        
                return [
                    "success" => true,
                    "message" => "Operação '$operacao' realizada com sucesso para o $tipo $itemId."
                ];
            } catch (Exception $e) {
                return [
                    "success" => false,
                    "message" => $e->getMessage()
                ];
            }
        }

        public function repetirPedido($pedido){
            $sql = "
                SELECT 
                    'cupcake' AS tipo,
                    pc.idCupcake AS idItem,
                    pc.quantidade
                FROM 
                    pedido_cupcake pc
                WHERE 
                    pc.idPedido = ?

                UNION ALL

                SELECT 
                    'combo' AS tipo,
                    pco.idCombo AS idItem,
                    pco.quantidade
                FROM 
                    pedido_combo pco
                WHERE 
                    pco.idPedido = ?
            ";

            $stmt = $this->conn->prepare($sql);
            
            if(!$stmt){
                die(json_encode([
                    "success" => false,
                    "message" => "Erro ao preparar query: " . $this->conn->error
                ]));
            }

            $stmt->bind_param("ii", $pedido, $pedido);

            if (!$stmt->execute()) {
                die(json_encode([
                    "success" => false,
                    "message" => "Erro ao executar query: " . $stmt->error
                ]));
            }

            $result = $stmt->get_result();

            $itens = [];
            while($row = $result->fetch_assoc()){
                $itens[] = $row;
            }

            echo json_encode([
                "success" => true,
                "data" => $itens
            ]);

        }

        public function criarCarrinho($email) {
            $sql = "INSERT INTO carrinho (idCliente)
                SELECT idCliente
                FROM cliente
                WHERE email = ?";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $email);

            $stmt->execute();
        }

        public function getCarrinho($userId){
            $sql = "SELECT idCarrinho FROM carrinho WHERE idCliente = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return $row ? $row['idCarrinho'] : null;
        }

        public function esvaziarCarrinho($usuario){
            try {
                $db = new Database();
                $conn = $db->connect();
            
                $conn->begin_transaction();
            
                $sqlCupcake = "DELETE FROM carrinho_cupcake WHERE idCarrinho IN (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)";
                $stmtCupcake = $conn->prepare($sqlCupcake);
                $stmtCupcake->bind_param("i", $usuario);
                $stmtCupcake->execute();
            
                $sqlCombo = "DELETE FROM carrinho_combo WHERE idCarrinho IN (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)";
                $stmtCombo = $conn->prepare($sqlCombo);
                $stmtCombo->bind_param("i", $usuario);
                $stmtCombo->execute();
            
                $conn->commit();
            
                echo json_encode(['success' => true, 'message' => 'Carrinho foi limpo com sucesso!']);
                exit;
            } catch (Exception $e) {
                
                $conn->rollback();
                echo "Erro ao limpar o carrinho: " . $e->getMessage();
            }
        }

        public function getCart($userId){
            
            $sql = "SELECT
                'cupcake' AS tipo,                      
                c.idCupcake AS id,                      
                c.quantidade,                           
                cp.sabor AS descricao,                  
                cp.preco,                               
                NULL AS sabores                         
            FROM
                carrinho_cupcake c
            JOIN
                cupcake cp
            ON
                c.idCupcake = cp.idCupcake
            WHERE
                c.idCarrinho = (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)

            UNION ALL

            SELECT
                'combo' AS tipo,                        
                cb.idCombo AS id,                       
                cb.quantidade,                          
                CONCAT('Combo ', co.tamanho) AS descricao, 
                co.preco,                               
                GROUP_CONCAT(cup.sabor SEPARATOR ', ') AS sabores 
            FROM
                carrinho_combo cb
            JOIN
                combo co
            ON
                cb.idCombo = co.idCombo
            JOIN
                combo_cupcake cc                        
            ON
                cc.idCombo = cb.idCombo
            JOIN
                cupcake cup
            ON
                cc.idCupcake = cup.idCupcake
            WHERE
                cb.idCarrinho = (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)
            GROUP BY
                cb.idCombo, cb.quantidade, co.tamanho, co.preco;
            ";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $userId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $itens = [];
            while ($row = $result->fetch_assoc()) {
                $itens[] = $row;
            }

            echo json_encode(['success' => true, 'itens' => $itens]);
            exit;
        }

        public function closeConnection() {
            $this->db->close();
        }
    }
?>