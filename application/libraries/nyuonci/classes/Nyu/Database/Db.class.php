<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Database;
/**
 * Classe de conexão à banco de dados do Nyu
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 6.1.2
 * @uses PDO
 */
class Db extends \Nyu\Core\CI{
    /**
     * Objeto de conexão PDO utilizado para as operações em banco de dados
     * @var PDO
     */
    protected $con;
    
    /**
     * Se true, espera para fazer o commit manual
     * @var boolean
     * @since 5.0
     */
    protected $manualCommitActive;
    
    /**
     * Objeto NyuDb utilizado para criar apenas uma conexão de banco de dados
     * @var \Nyu\Database\Db
     */
    protected static $instance;
    
    /**
     * Guarda a última consulta sql efetuada
     * @var string
     */
    protected static $lastQuery;
 
    /**
     * Método singleton da classe NyuDb
     * @return \Nyu\Database\Db
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
     * Método construtor da classe Db
     */
    public function __construct() {
        parent::__construct();
        $this->start();
    }
    
    /**
     * Inicia a transação
     * Para carregar uma nova configuração de banco de dados, antes de utilizar
     * os métodos de acesso a banco de dados da classe Model, chamar o método
     * NyuCore::setDatabaseConfig() para alterar a configuração
     */
    public function start() {
        global $nyu__database_config;
        
        // Configuração padrão de acesso a banco de dados
        $database_config_name = 'default';
        
        // Se está especificando uma conexão, carrega a configuração
        if($nyu__database_config){
            $database_config_name = $nyu__database_config;
        }
        
        // Carrega as configurações de banco de dados
        $database_config = \Nyu\Core\Config::getConfig('database', $database_config_name);
        
        // Se é mysql ou não especificou, carrega no padrão mysql
        if($database_config['driver'] == 'mysql' || !$database_config['driver']){
            // Trata o host:porta
            $hosttmp = $database_config['host'];
            $hosttmp = explode('//', $hosttmp);
            $hosttmp = implode($hosttmp);
            $hosttmp = explode(':',$hosttmp);
            $hostqtde = count($hosttmp);
            if($hostqtde > 2){
                $host = $hosttmp[$hostqtde - 2];
                $port = $hosttmp[$hostqtde - 1];
            }else{
                $host = $hosttmp[0];
                $port = @$hosttmp[1];
            }
            $dsn = "mysql:dbname={$database_config['name']};host={$host}".($port ? ";port={$port}" : '');
        // Se é sql server
        }elseif($database_config['driver'] == 'mssql'){
            $dsn = "dblib:host={$database_config['host']};dbname={$database_config['name']}";
        // Se é oracle
        }elseif($database_config['driver'] == 'oracle'){
            $dsn = "oci:dbname={$database_config['tns']}";
        // Se o driver é sqlite
        }elseif($database_config['driver'] == 'sqlite'){
            $dsn = "sqlite:{$database_config['path']}";
        //Se o driver é odbc
        }elseif($database_config['driver'] == 'odbc'){
            $dsn = $database_config['path'];
        // Se o driver é custom, carrega a string informada
        }elseif($database_config['driver'] == 'custom'){
            $dsn = $database_config['custom'];
        }
        // Cria a conexão, informando que deve retornar exceptions
        $this->con = new \PDO($dsn, @$database_config['user'], @$database_config['password'], array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ));
        
        // Inicia uma transação
        $this->con->beginTransaction();
    }
    
    /**
     * Inicia uma nova transação, para quando é feito um commit. É utilizado 
     * dentro da classe model automaticamente, mas pode ser chamado manualmente
     * se necessário
     */
    public function beginTransaction(){
        if(!$this->con->inTransaction()){
            $this->con->beginTransaction();
        }
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
     * Faz commit da transação atual, dentro do processamento da classe Model
     * Para forçar o commit manualmente, utilizar o método Db::commit();
     */
    public function _nyuModelCommit(){
        if(!$this->manualCommitActive){
            $this->commit();
        }
    }

    /**
     * Faz commit da transação atual
     */
    public function commit() {
        $this->con->commit();
    }

    /**
     * Faz rollback da transação atual
     */
    public function rollback() {
        $this->con->rollBack();
    }

    /**
     * Método estático que ativa a transação manual
     * Aguarda a chamada do método Db::dbCommit() para persistir a operação
     * @since 6.0
     */
    public static function dbTransaction(){
        $db = self::getInstance(); // Carrega a instância já existente do objeto
        $db->manualCommit(); // Força o commit manual, para que o controle não seja feito automaticamente dentro da Model
        $db->beginTransaction(); // Inicia uma nova transação, se já não estiver em uma
    }
    
    /**
     * Método estático que persiste a transação atual, iniciada com 
     * NyuDb::dbTransaction()
     * @since 6.0
     */
    public static function dbCommit(){
        $db = self::getInstance();
        $db->commit();
    }
    
    /**
     * Método estático que cancela a transação atual, iniciada com
     * NyuDb::dbTransaction()
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
                $getPk = "get{$var}";
            }
        }
        $pk_value = $values[$searchField];
        if ($this->regExists($pk_value, $table, $searchField)) {
            $values = array_values($values);
            $values[] = $pk_value;
            $sql = "update {$table} set " . implode(" = ?, ", $cols) . " = ? where {$searchField} = ?";
        } else {
            $sql = "insert into {$table} (" . implode(", ", $cols) . ") values (" . implode(", ", $cols_bind) . ")";
            // Para PDO, remove os índices do array de valores para bind
            $tmp_values = array();
            foreach($values as $val){
                $tmp_values[] = $val;
            }
            $values = $tmp_values;
        }
        
        $stmt = $this->con->prepare($sql);
        $ret = $stmt->execute($values);
        
        self::setLastQuery($sql);

        if ($ret) {
            if(!$obj->$getPk()){ // se não possui id, atualiza o objeto
                $id = $this->con->lastInsertId();
                \Nyu\Core\Core::saveInSess("lastInsertId", $id);
                $obj->$setPk((($id) ? $id : $pk_value));
            }
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
        
        $stmt = $this->con->prepare($sql);
        $res = $stmt->execute(array($value));
        
        self::setLastQuery($sql);

        $qtde = $stmt->fetchAll();
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
        
        $stmt = $this->con->prepare($sql);
        $r = $res = $stmt->execute(array($value));
        
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
     * @param string|array $where (Opcional) Texto com a condição da busca ou 
     * array com a condição da busca como índice e o valor que será utilizado 
     * no bind como valor
     * Ex 1:
     * " id = 1 " ou array(" id = ? " => 1)
     * Ex 2:
     * " id_1 = 1 and id_2 = 2 " ou array(" id_1 = ? " => 1, " and id_2 = ?" => 2)
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
        
        if(is_array($where)){
            $bindWhere = array_values($where);
            $where = array_keys($where);
            $where = implode(" ", $where);
        }
        
        $sql = "select " . implode(", ", $cols) . " from {$table}" . ($where ? " where " . $where : '') . (($order) ? " order by " . implode(", ", $order) : "");
        
        if(!is_null($iniReg) && !is_null($limit)){
            $sql .= " limit {$iniReg}, {$limit}";
        }elseif(is_null($iniReg) && !is_null($limit)){
            $sql .= " limit {$limit}";
        }

        $stmt = $this->con->prepare($sql);
        if(isset($bindWhere)){
            $ret = $res = $stmt->execute($bindWhere);
        }else{
            $ret = $res = $stmt->execute();
        }
        
        self::setLastQuery($sql);

        if (!$ret) {
            return false;
        }
        $ret = $stmt->fetchAll();
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

        $stmt = $this->con->prepare($sql);

        $params[] = $obj->$getMethod();

        if ($extraSearchField) {
            for ($i = 0; $i < count($extraGetMethod); $i++) {
                $params[] = $obj->$extraGetMethod[$i]();
            }
        }

        $ret = $res = $stmt->execute($params);
        
        self::setLastQuery($sql);

        if (!$ret) {
            return false;
        }
        $ret = $stmt->fetchAll();
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
     * @param string $where (Opcional) Texto com a condição da busca extra
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
        
        $stmt = $this->con->prepare($sql);
        $ret = $res = $stmt->execute(array($key));
        
        self::setLastQuery($sql);

        if (!$ret) {
            return false;
        }
        $ret = $stmt->fetchAll();
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
        
        $res = $this->con->prepare($sql);
        if ($bind) {
            $ret = $res->execute($bind);
        } else {
            $ret = $res->execute();
        }
        
        self::setLastQuery($sql);
        
        if (!$ret) {
            return false;
        }
        $ret = $res->fetchAll();
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
                
        $res = $this->con->prepare($sql);
        if($key){
            $ret = $res->execute(array($key));
        }else{
            $ret = $res->execute();
        }
        
        self::setLastQuery($sql);

        $qtde = $res->fetchAll();
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

        $res = $this->con->prepare($sql);
        if ($bind) {
            $ret = $res->execute($bind);
        } else {
            $ret = $res->execute();
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

    /**
     * Executa um comando sql ou ddl no banco de dados e retorna o número de 
     * linhas afetadas ou false em caso de erro. É necessário executar o método
     * commit() após o fim das transações, para persistir as alterações
     * @param string $sql Comando a ser executado
     * @param boolean $commit Padrão true, faz ou não o commit da operação após
     * executar
     * @return int
     * @since 6.0
     */
    public function quickexec($sql, $commit=true){
        $ret = $this->con->exec($sql);
        self::setLastQuery($sql);
        if($commit){
            $this->commit();
        }
        return $ret;
    }
}