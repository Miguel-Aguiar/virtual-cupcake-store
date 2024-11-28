<?php
    require_once 'Database.php';

    class Combo {
        private $db;
        private $conn;

        public function __construct() {
            $this->db = new Database();
            $this->conn = $this->db->connect();
        }

        public function __destruct() {
            $this->db->close();
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
?>