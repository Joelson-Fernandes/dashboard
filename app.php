<?php

    //classe Dashboard
    class Dashboard {

        public $data_inicio;
        public $data_fim;
        public $numeroVendas;
        public $totalVendas;
        public $clientesAtivos;
        public $clientesInativos;
        public $totalReclamacoes;
        public $totalElogios;
        public $totalSugestoes;
        public $totalDespesas;

        public function __get($attr) {
            return $this->$attr;
        }

        public function __set($attr, $value) {
            $this->$attr = $value;
            return $this;
        }
    }

    //classe conexão db
    class Conexao {

        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';

        public function conectar() {

            try {

                $conexao = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname",
                    "$this->user",
                    "$this->pass"
                );

                $conexao->exec('set charset utf8');

                return $conexao;

            } catch (PDOException $e) {
                echo '<p>'.$e->getMessage().'</p>'; 
            }
        }
    }

    // classe (model)
    class Bd {
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao, Dashboard $dashboard) {
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
            
        }

        public function __getNumeroVendas() {
            $query = "
                SELECT
                    COUNT(*) as numero_vendas 
                FROM 
                    tb_vendas 
                WHERE 
                    data_venda 
                BETWEEN :data_inicial AND :data_final";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicial', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_final', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
        }

        public function __getTotalVendas() {
            $query = "
                SELECT
                    SUM(total) as total_vendas 
                FROM 
                    tb_vendas 
                WHERE 
                    data_venda 
                BETWEEN :data_inicial AND :data_final";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicial', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_final', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }

        public function __getClientesAtivos() {
            $query = "
                SELECT
                    SUM(cliente_ativo) as clientes_ativos
                FROM 
                    tb_clientes
                WHERE 
                    cliente_ativo = :ativo";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':ativo', 1);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
        }

        public function __getClientesInativos() {
            $query = "
                SELECT
                    COUNT(cliente_ativo) as clientes_inativos
                FROM 
                    tb_clientes
                WHERE 
                    cliente_ativo = :inativo";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':inativo', 0);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
        }

        public function __getTotalReclamacoes() {
            $query = "
                SELECT
                    COUNT(tipo_contato) as total_reclamacoes
                FROM 
                    tb_contatos
                WHERE 
                    tipo_contato = :reclamacao";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':reclamacao', 1);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;
        }

        public function __getTotalElogios() {
            $query = "
                SELECT
                    COUNT(tipo_contato) as total_elogios
                FROM 
                    tb_contatos
                WHERE 
                    tipo_contato = :elogio";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':elogio', 3);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;
        }

        public function __getTotalSugestoes() {
            $query = "
                SELECT
                    COUNT(tipo_contato) as total_sugestoes
                FROM 
                    tb_contatos
                WHERE 
                    tipo_contato = :sugestoes";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':sugestoes', 2);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;
        }

        public function __getTotalDespesas() {
            $query = "
                SELECT
                    SUM(total) as total_despesas 
                FROM 
                    tb_despesas 
                WHERE 
                    data_despesa 
                BETWEEN :data_inicial AND :data_final";

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicial', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_final', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
        }
    }

    //Lógica do script
    $dashboard = new Dashboard();
    $conexao = new Conexao();


    $competencia = explode("-", $_GET['competencia']);
    $ano = $competencia[0];
    $mes = $competencia[1];
    $dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

    
    $dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
    $dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dias_do_mes);

    
    $bd = new Bd($conexao, $dashboard);

    $dashboard->__set('numeroVendas', $bd->__getNumeroVendas());
    $dashboard->__set('totalVendas', $bd->__gettotalVendas());
    $dashboard->__set('clientesAtivos', $bd->__getClientesAtivos());
    $dashboard->__set('clientesInativos', $bd->__getClientesInativos());
    $dashboard->__set('totalReclamacoes', $bd->__getTotalReclamacoes());
    $dashboard->__set('totalElogios', $bd->__getTotalElogios());
    $dashboard->__set('totalSugestoes', $bd->__getTotalSugestoes());
    $dashboard->__set('totalDespesas', $bd->__getTotalDespesas());
   
    echo json_encode($dashboard);

?>