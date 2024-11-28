<?php
    require_once '../Model/ComboModel.php';

    class ComboController {

        private $combo;

        public function __construct() {
            $this->combo = new Combo();
        }

        public function readAll(){
            return $this->combo->readAll();
        }

        public function readCombo($idCombo){
            return $this->combo->readCombo($idCombo);
        }

    }
    
?>