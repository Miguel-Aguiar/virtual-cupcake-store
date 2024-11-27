<?php
    require_once 'Database.php';

    class Carrinho {
        private $db;
        private $conn;
        private $table = 'carrinho';

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        public function exibirCarrinho($userId) {
            $carrinhoId = $this->getCarrinho($userId);
        
            // Consulta cupcakes no carrinho
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
        
            // Consulta combos no carrinho
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
        
            // Combina os resultados de cupcakes e combos
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
                // Validações básicas
                if (!in_array($tipo, ['cupcake', 'combo'])) {
                    throw new Exception("Tipo inválido: $tipo");
                }
        
                if (!in_array($operacao, ['adicionar', 'diminuir'])) {
                    throw new Exception("Operação inválida: $operacao");
                }
        
                // Obtém o ID do carrinho
                $carrinhoId = $this->getCarrinho($userId);
                if (!$carrinhoId) {
                    throw new Exception("Carrinho não encontrado para o usuário: $userId");
                }
        
                // Determina a tabela e a coluna com base no tipo
                $tabela = $tipo === 'cupcake' ? 'carrinho_cupcake' : 'carrinho_combo';
                $colunaId = $tipo === 'cupcake' ? 'idCupcake' : 'idCombo';
        
                // Verifica se o item já existe no carrinho
                $sql = "SELECT $colunaId, quantidade FROM $tabela WHERE idCarrinho = ? AND $colunaId = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $carrinhoId, $itemId);
                $stmt->execute();
                $result = $stmt->get_result();
        
                if ($result->num_rows > 0) {
                    // Item já está no carrinho
                    $row = $result->fetch_assoc();
                    $quantidadeAtual = $row['quantidade'];
        
                    if ($operacao === 'adicionar') {
                        $novaQuantidade = $quantidadeAtual + 1;
                    } else {
                        $novaQuantidade = max(0, $quantidadeAtual - 1);
                    }
        
                    if ($novaQuantidade > 0) {
                        // Atualiza a quantidade
                        $sqlUpdate = "UPDATE $tabela SET quantidade = ? WHERE idCarrinho = ? AND $colunaId = ?";
                        $stmtUpdate = $this->conn->prepare($sqlUpdate);
                        $stmtUpdate->bind_param("iii", $novaQuantidade, $carrinhoId, $itemId);
                        $stmtUpdate->execute();
                    } else {
                        // Remove o item do carrinho
                        $sqlDelete = "DELETE FROM $tabela WHERE idCarrinho = ? AND $colunaId = ?";
                        $stmtDelete = $this->conn->prepare($sqlDelete);
                        $stmtDelete->bind_param("ii", $carrinhoId, $itemId);
                        $stmtDelete->execute();
                    }
                } else {
                    if ($operacao === 'adicionar') {
                        // Adiciona o item ao carrinho
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
            
                // Iniciar transação (opcional para garantir consistência)
                $conn->begin_transaction();
            
                // Deletar cupcakes do carrinho
                $sqlCupcake = "DELETE FROM carrinho_cupcake WHERE idCarrinho IN (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)";
                $stmtCupcake = $conn->prepare($sqlCupcake);
                $stmtCupcake->bind_param("i", $usuario);
                $stmtCupcake->execute();
            
                // Deletar combos do carrinho
                $sqlCombo = "DELETE FROM carrinho_combo WHERE idCarrinho IN (SELECT idCarrinho FROM carrinho WHERE idCliente = ?)";
                $stmtCombo = $conn->prepare($sqlCombo);
                $stmtCombo->bind_param("i", $usuario);
                $stmtCombo->execute();
            
                // Confirmar transação
                $conn->commit();
            
                echo json_encode(['success' => true, 'message' => 'Carrinho foi limpo com sucesso!']);
                exit;
            } catch (Exception $e) {
                // Reverter transação em caso de erro
                $conn->rollback();
                echo "Erro ao limpar o carrinho: " . $e->getMessage();
            }
        }

        public function closeConnection() {
            $this->db->close();
        }
    }


    $carrinho = new Carrinho();
    // $resultado = $cupcakes->readAll();

    // foreach ($resultado as $r) {
    //     echo "Id: " . $r['idCupcake'] . '<br>';
    //     echo "Sabor: " . $r['sabor'] . '<br>';
    //     echo "Descricao: " . $r['descricao'] . '<br>';
    //     echo "Preco: " . $r['preco'] . '<br>';
    // }

    $carrinho->closeConnection();
?>