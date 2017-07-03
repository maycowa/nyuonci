<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Antiga Classe de conexão à banco de dados do Nyu
 * Substituida pela nova classe NyuDb, que utiliza a biblioteca PDO, permitindo
 * acessar qualquer banco de dados
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 5.0
 * @uses ADOdb
 * @deprecated
 */
class NyuDbAdo {
    /**
     * Objeto de conexão ADOdb utilizado para as operações em banco de dados
     * @var ADOdb
     */
    protected $con;
    
    /**
     * Se true, espera para fazer o commit manual
     * @var boolean
     * @since 5.0
     */
    protected $manualCommitActive;
    
    /**
     * Objeto NyuDbAdo utilizado para criar apenas uma conexão de banco de dados
     * @var NyuDbAdo
     */
    protected static $instance;
    
    /**
     * Guarda a última consulta sql efetuada
     * @var string
     */
    protected static $lastQuery;
 
    /**
     * Método singleton da classe NyuDbAdo
     * @return NyuDbAdo
     */
    public static function getInstance(){
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;     
    }
    
    /**
     * Reinicia a instância de singleton, para forçar a criação de um novo
     * objeto
     */
    public static function resetInstance(){
        self::$instance = null;
    }
    
    /**
     * Seta o valor da última consulta a ser efetuada
     * @param string $lastQuery
     */
    protected static function setLastQuery($lastQuery){
        self::$lastQuery = $lastQuery;
    }
    
    /**
     * Retorna o valor da última consulta efetuada
     * @return string
     */
    public static function getLastQuery(){
        return self::$lastQuery;
    }

    /**
     * Método construtor da classe NyuDbAdo
     */
    public function __construct() {
        $this->start();
    }
    
    /**
     * Inicia a transação
     * Para carregar uma nova configuração de banco de dados, antes de utilizar
     * os métodos de acesso a banco de dados da classe NyuModel, setar a 
     * variável $nyu__database com o nome da configuração a carregar.
     */
    public function start() {
        global $nyu__database_config;
        
        $this->con = NewADOConnection("mysqli");
        if($nyu__database_config && $nyu__database_config != "default"){
            $database_config = \NyuConfig::getConfig(NYU_CONFIG_DATABASE, $nyu__database_config);
            $this->con->Connect($database_config['host'], $database_config['user'], $database_config['password'], $database_config['name']);
        }else{
            $this->con->Connect(_DB_HOST_, _DB_USR_, _DB_PSW_, _DB_NAME_);
        }
        $this->con->BeginTrans();
    }
    
    /**
     * Força a classe a esperar o commit manual
     * Utilizado para manter a mesma transação entre a gravação de objetos 
     * diferentes
     */
    public function manualCommit(){
        $this->manualCommitActive = true;
    }
    
    /**
     * Faz commit da transação atual, dentro do processamento da classe NyuModel
     * Para forçar o commit manualmente, utilizar o método NyuDbAdo::commit();
     */
    public function _nyuModelCommit(){
        if(!$this->manualCommitActive){
            $this->con->CommitTrans();
        }
    }

    /**
     * Faz commit da transação atual
     */
    public function commit() {
        $this->con->CommitTrans();
    }

    /**
     * Faz rollback da transação atual
     */
    public function rollback() {
        $this->con->RollbackTrans();
    }

    /**
     * Método estático que ativa a transação manual
     * Aguarda a chamada do método NyuDbAdo::dbCommit() para persistir a operação
     * @since 6.0
     */
    public static function dbTransaction(){
        $db = self::getInstance();
        $db->manualCommit();
    }
    
    /**
     * Método estático que persiste a transação atual, iniciada com 
     * NyuDbAdo::dbTransaction()
     * @since 6.0
     */
    public static function dbCommit(){
        $db = self::getInstance();
        $db->commit();
    }
    
    /**
     * Método estático que cancela a transação atual, iniciada com
     * NyuDbAdo::dbTransaction()
     * @since 6.0
     */
    public static function dbRollback(){
        $db = self::getInstance();
        $db->rollback();
    }

    /**
     * Salva um objeto no banco de dados
     * @param object $obj Objeto a gravar
     * @param string $table Tabela do banco de dados
     * @param array $fields Array com a referência atributo-coluna do objeto 
     * à tabela
     * @param string $searchField (Opcional) Campo chave primária para fazer a  
     * alteração (se nulo, irá considerar o nome da tabela como nome do campo 
     * chave primária)
     * @return boolean
     */
    public function save(&$obj, $table, $fields, $searchField=null) {
        $searchField = (($searchField) ? $searchField : $table);

        foreach ($fields as $var => $col) {
            $method = "get{$var}";
            $values[$col] = $obj->$method(); // Valor do atributo no objeto
            $cols[] = $col; // Colunas da tabela
            $cols_bind[] = " ? ";
            if ($col == $searchField) {
                $setPk = "set{$var}";
            }
        }
        $pk_value = $values[$searchField];
        if ($this->regExists($pk_value, $table, $searchField)) {
            $values = array_values($values);
            $values[] = $pk_value;
            $sql = "update {$table} set " . implode(" = ?, ", $cols) . " = ? where {$searchField} = ?";
        } else {
            $sql = "insert into {$table} (" . implode(", ", $cols) . ") values (" . implode(", ", $cols_bind) . ")";
        }
        
        $stmt = $this->con->Prepare($sql);
        $ret = $this->con->Execute($stmt, $values);
        
        self::setLastQuery($sql);

        if ($ret) {
            $id = $this->con->Insert_ID();
            \NyuCore::saveInSess("lastInsertId", $id);
            $obj->$setPk((($id) ? $id : $pk_value));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica se um registro existe na tabela
     * @param string $value Valor da chave primária do registro a verificar
     * @param string $table Tabela do registro a verificar
     * @param string $searchField (Opcional) Campo chave primária para fazer a 
     * alteração (se nulo, irá considerar o nome da tabela como nome do campo 
     * chave primária)
     * @return boolean
     */
    public function regExists($value, $table, $searchField=null) {
        $searchField = (($searchField) ? $searchField : $table);
        $sql = "select count(*) as q from {$table} where {$searchField} = ?";
        
        $res = $this->con->Prepare($sql);
        $res = $this->con->Execute($res, array($value));
        
        self::setLastQuery($sql);

        $qtde = $res->GetAll();
        if ((integer) $qtde[0]['q'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove um registro do banco de dados
     * @param string $value Valor da chave primária do registro a apagar
     * @param string $table Tabela do registro a apagar
     * @param string $searchField (Opcional) Campo chave primária para fazer a 
     * alteração (se nulo, irá considerar o nome da tabela como nome do campo 
     * chave primária)
     * @return boolean
     */
    public function delete($value, $table, $searchField=null) {
        $searchField = (($searchField) ? $searchField : $table);
        $sql = "delete from {$table} where {$searchField} = ? ";
        
        $res = $this->con->Prepare($sql);
        $r = $res = $this->con->Execute($res, array($value));
        
        self::setLastQuery($sql);

        return $r;
    }

    /**
     * Lista os registros de uma tabela
     * @param object $defClass Objeto vazio para definição da classe
     * @param string $table Tabela a buscar os registros
     * @param array $fields Array com a referência atributo-coluna do objeto
     * à tabela
     * @param array $order (Opcional) Array com os campos a ordenar a colsulta
     * @param string $where (Opcional) Texto com a condição da busca
     * @param int $iniReg (Opcional) Número mínimo do registro - para limit
     * @param int $limit (Opcional) Quantidade máxima de registros retornados
     * @return boolean|array
     */
    public function listAll($defClass, $table, $fields, $order=null, $where=null, $iniReg = null, $limit = null) {
        foreach ($fields as $var => $col) {
            $cols[] = $col;
            $method = "set{$var}";
            $methods[$col] = $method;
        }

        $class = ((is_object($defClass)) ? get_class($defClass) : $defClass);
        $sql = "select " . implode(", ", $cols) . " from {$table}" . ($where ? " where " . $where : '') . (($order) ? " order by " . implode(", ", $order) : "");
        
        if(!is_null($iniReg) && !is_null($limit)){
            $sql .= " limit {$iniReg}, {$limit}";
        }elseif(is_null($iniReg) && !is_null($limit)){
            $sql .= " limit {$limit}";
        }

        $res = $this->con->Prepare($sql);
        $ret = $res = $this->con->Execute($res);
        
        self::setLastQuery($sql);

        if (!$ret) {
            return false;
        }
        $ret = $res->GetRows();
        if ($ret) {
            foreach ($ret as $val) {
                $o = new $class();
                foreach ($cols as $col) {
                    $o->$methods[$col]($val[$col]);
                }
                $l[] = $o;
            }
            return $l;
        } else {
            return false;
        }
    }

    /**
     * Carrega os dados de um objeto
     * @param object $obj Objeto a gravar
     * @param string $table Tabela do banco de dados
     * @param array $fields Array com a referência atributo-coluna do objeto
     * à tabela
     * @param string $searchField (Opcional) Campo chave primária para fazer a 
     * alteração (se nulo, irá considerar o nome da tabela como nome do campo 
     * chave primária)
     * @param array $extraSearchField (Opcional) Outros campos a incluir na busca
     * @return boolean|object
     */
    public function load(&$obj, $table, $fields, $searchField=null, $extraSearchField=null) {
        $searchField = (($searchField) ? $searchField : $table);
        foreach ($fields as $var => $col) {
            $cols[] = $col;
            $method = "set{$var}";
            $methods[$col] = $method;
            if ($col == $searchField) {
                $getMethod = "get{$var}";
            }
            if ($extraSearchField) {
                foreach ($extraSearchField as $esf) {
                    if ($col == $esf) {
                        $extraGetMethod[] = "get{$var}";
                    }
                }
            }
        }

        //$class = get_class($obj);
        $sql = "select " . implode(", ", $cols) . " from {$table} where {$searchField} = ?";

        if ($extraSearchField) {
            foreach ($extraSearchField as $esf) {
                $sql .= " and {$esf} = ? ";
            }
        }

        $res = $this->con->Prepare($sql);

        $params[] = $obj->$getMethod();

        if ($extraSearchField) {
            for ($i = 0; $i < count($extraGetMethod); $i++) {
                $params[] = $obj->$extraGetMethod[$i]();
            }
        }

        $ret = $res = $this->con->Execute($res, $params);
        
        self::setLastQuery($sql);

        if (!$ret) {
            return false;
        }
        $ret = $res->GetRows();
        if ($ret) {

            //$obj = false;
            foreach ($ret as $val) {
                //$obj = new $class();
                foreach ($cols as $col) {
                    $obj->$methods[$col]($val[$col]);
                }
            }
            return $obj;
        } else {
            return false;
        }
    }

    /**
     * Lista os registros de uma tabela a partir de um campo chave
     * @param object $defClass Objeto vazio para definição da classe
     * @param string $table Tabela a buscar os registros
     * @param array $fields Array com a referência atributo-coluna do objeto
     * à tabela
     * @param string $key valor da chave a buscar os dados
     * @param string $searchField (Opcional) Nome do campo chave para fazer a 
     * busca (se nulo, irá considerar o nome da tabela como nome do campo chave 
     * primária)
     * @param array $order (Opcional) Array com os campos a ordenar a colsulta
     * @param string $where (Opcional) Texto com a condição da busca
     * @return boolean|array
     */
    public function listByKey($defClass, $table, $fields, $key, $searchField=null, $order=null, $where=null) {
        $searchField = (($searchField) ? $searchField : $table);
        foreach ($fields as $var => $col) {
            $cols[] = $col;
            $method = "set{$var}";
            $methods[$col] = $method;
        }

        $class = ((is_object($defClass)) ? get_class($defClass) : $defClass);
        $sql = "select " . implode(", ", $cols) . " from {$table} where {$searchField} = ? " .
                (($where) ? " and (" . $where . ") " : "") . 
                (($order) ? " order by " . implode(", ", $order) : "");
        
        $res = $this->con->Prepare($sql);
        $ret = $res = $this->con->Execute($res, array($key));
        
        self::setLastQuery($sql);

        if (!$ret) {
            return false;
        }
        $ret = $res->GetRows();
        if ($ret) {
            foreach ($ret as $val) {
                $o = new $class();
                foreach ($cols as $col) {
                    $o->$methods[$col]($val[$col]);
                }
                $l[] = $o;
            }
            return $l;
        } else {
            return false;
        }
    }

    /**
     * Executa uma consulta no banco de dados
     * @param string $sql Consulta a ser executada
     * @param array $bind (Opcional) Array com as variáveis que serão vinculadas
     * @return boolean|array
     */
    public function query($sql, $bind=null) {
        
        $res = $this->con->Prepare($sql);
        if ($bind) {
            $ret = $this->con->Execute($res, $bind);
        } else {
            $ret = $this->con->Execute($res);
        }
        
        self::setLastQuery($sql);
        
        if (!$ret) {
            return false;
        }
        $ret = $ret->GetRows();
        if ($ret) {
            return $ret;
        } else {
            return false;
        }
    }
    
    /**
     * Conta quantos registros existem em uma tabela
     * @param string $table Tabela a ser feita a busca
     * @param string $key (Opcional) Valor da chave para incluir na busca
     * @param string $searchField (Opcional) Nome do campo chave que será 
     * utilizado na busca
     * @return boolean
     */
    public function count($table, $key = null, $searchField = null){
        $sql = "select count(*) as q from {$table} " . 
                (($key)?(($searchField)?" where {$searchField} = ? ":" where {$table} = ? "):"");
                
        $res = $this->con->Prepare($sql);
        if($key){
            $res = $this->con->Execute($res, array($key));
        }else{
            $res = $this->con->Execute($res);
        }
        
        self::setLastQuery($sql);

        $qtde = $res->GetRows();
        if($qtde){
            return $qtde[0]['q'];
        }else{
            return false;
        }
    }
    
    /**
     * Executa um comando no banco de dados
     * @param string $sql Comando a ser executado
     * @param array $bind (Opcional) Array com as variáveis que serão vinculadas
     * @return boolean
     */
    public function execute($sql, $bind=null) {

        $res = $this->con->Prepare($sql);
        if ($bind) {
            $ret = $this->con->Execute($res, $bind);
        } else {
            $ret = $this->con->Execute($res);
        }
        
        self::setLastQuery($sql);

        if (!$ret) {
            return false;
        }
        if ($ret) {
            return $ret;
        } else {
            return false;
        }
    }

}