<?php
    require_once 'Database.php';

    class Combo {
        private $db;
        private $conn;
        private $table = 'combo';

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        public function readAll() {
            $sql = "
                    SELECT 
                        c.idCombo,
                        CONCAT(UPPER(SUBSTRING(c.tamanho, 1, 1)), LOWER(SUBSTRING(c.tamanho, 2))) AS tamanho,
                        c.preco AS comboPreco,
                        GROUP_CONCAT(cup.idCupcake SEPARATOR ', ') AS idCupcake,
                        GROUP_CONCAT(cup.sabor SEPARATOR ', ') AS sabores,
                        GROUP_CONCAT(cup.preco SEPARATOR ', ') AS precosCupcakes,
                        GROUP_CONCAT(cup.descricao SEPARATOR ' ') AS descricoes,
                        GROUP_CONCAT(cup.restricoes SEPARATOR ' ') AS restricoes
                    FROM 
                        combo AS c
                    LEFT JOIN 
                        combo_cupcake AS cc ON c.idCombo = cc.idCombo
                    LEFT JOIN 
                        cupcake AS cup ON cc.idCupcake = cup.idCupcake
                    GROUP BY 
                        c.idCombo;
            ";
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

        public function readCombo($idCombo){
            $sql = "SELECT 
                    c.idCombo,
                    CONCAT(UPPER(SUBSTRING(c.tamanho, 1, 1)), LOWER(SUBSTRING(c.tamanho, 2))) AS tamanho,
                    c.preco AS comboPreco,
                    GROUP_CONCAT(cup.idCupcake SEPARATOR ', ') AS idCupcake,
                    GROUP_CONCAT(cup.sabor SEPARATOR ', ') AS sabores,
                    GROUP_CONCAT(cup.preco SEPARATOR ', ') AS precosCupcakes,
                    GROUP_CONCAT(cup.descricao SEPARATOR ' ') AS descricoes,
                    GROUP_CONCAT(cup.restricoes SEPARATOR ' ') AS restricoes
                FROM 
                    combo AS c
                LEFT JOIN 
                    combo_cupcake AS cc ON c.idCombo = cc.idCombo
                LEFT JOIN 
                    cupcake AS cup ON cc.idCupcake = cup.idCupcake
                WHERE
                    c.idCombo = ?
                GROUP BY 
                    c.idCombo;
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $idCombo);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }

        public function closeConnection() {
            $this->db->close();
        }

    }

    $combos = new Combo();
    $resultado = $combos->readAll();

    // foreach ($resultado as $r) {
        // echo '<pre>';
        // print_r($r);
        // echo '</pre>';
    //     echo "Id: " . $r['idCupcake'] . '<br>';
    //     echo "Sabor: " . $r['sabor'] . '<br>';
    //     echo "Descricao: " . $r['descricao'] . '<br>';
    //     echo "Preco: " . $r['preco'] . '<br>';
    // }

    $combos->closeConnection();
?>