<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Antiga Definição de classes do Nyu.
 * Utiliza a classe NyuDbAdo, que utiliza a biblioteca AdoDb.
 * Substituida pelas novas NyuModel e NyuDb que utlizam PDO e podem
 * acessar qualquer banco de dados
 * As classes que irão acessar banco de dados devem extender esta classe,
 * substituindo os atributos estáticos $table e $cols
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 2.1
 * @deprecated
 */
class NyuModelAdo {

    /**
     * Tabela do banco de dados que o objeto referencia. Deve ser sobrescrito 
     * na classe filha
     * @var string 
     */
    public static $table = "table";
    /**
     * Array com os atributos do objeto e seus campos correspondentes na tabela.
     * Deve ser sobrescrito na classe filha
     * @var array 
     */
    public static $cols = array("attribute" => "col");
    
    /**
     * Se setado este atributo, o sistema irá carregar a configuração do banco
     * de dados a partir deste nome - utilizado para carregar instâncias de
     * banco de dados diferentes da padrão
     * @var string
     */
    public $databaseConfig;
    
    /**
     * Seta o valor do atributo $databaseConfig, responsável por carregar
     * a configuração do banco de dados para instâncias diferentes da padrão
     * do sistema
     * @param string $databaseConfig Nome da configuração
     */
    public function setDatabaseConfig($databaseConfig){
        $this->databaseConfig = $databaseConfig;
        \NyuCore::setDatabaseConfig($this->databaseConfig);
    }

    /**
     * Salva o objeto no banco de dados
     * @param string $searchField (Opcional) Se informado, utiliza este campo 
     * como chave para a gravação do registro. Se for nulo, será buscado
     * um campo com o nome da tabela para efetuar a operação
     * @param array $cols (Opcional) Array com o mesmo formato do atributo $cols,
     * se informado, irá gravar apenas os campos indicados neste parâmetro, senão,
     * irá gravar todos os campos do atributo $cols
     * @return boolean
     */
    public function save($searchField = null, $cols = null) {
        if (!$cols) {
            $cols = $this->getCols();
        }
        $db = \NyuDbAdo::getInstance();
        try {
            $this->triggerBeforeSave();
            if ($db->save($this, $this->getTable(), $cols, $searchField)) {
                $this->triggerAfterSaveSuccess();
                $this->triggerAfterSave();
                $db->_nyuModelCommit();
                return true;
            } else {
                $this->triggerAfterSaveFail();
                $this->triggerAfterSave();
                $db->rollback();
                return false;
            }
        } catch (\PDOException $e) {
            \NyuCore::setException($e);
            $db->rollback();
            return false;
        } catch (\MysqlException $e) {
            \NyuCore::setException($e);
            $db->rollback();
            return false;
        } catch (\Exception $e) {
            \NyuCore::setException($e);
            $db->rollback();
            return false;
        }
    }

    /**
     * Apaga um registro do banco de dados
     * @param string $searchField (Opcional) Se informado, utiliza este campo 
     * como chave para a exclusão do registro. Se for nulo, será buscado
     * um campo com o nome da tabela para efetuar a operação
     * @return boolean
     */
    public function delete($searchField = null) {
        $db = \NyuDbAdo::getInstance();
        try {
            $searchField = (($searchField) ? $searchField : $this->getTable());
            $cols = $this->getCols();
            $atributeName = array_search($searchField, $cols);
            $value_method = "get".($atributeName ? $atributeName : $searchField);
            $value = $this->$value_method();
            $this->triggerBeforeDelete();
            if ($db->delete($value, $this->getTable(), $searchField)) {
                $this->triggerAfterDeleteSuccess();
                $this->triggerAfterDelete();
                $db->_nyuModelCommit();
                return true;
            } else {
                $this->triggerAfterDeleteFail();
                $this->triggerAfterDelete();
                $db->rollback();
                return false;
            }
        } catch (\PDOException $e) {
            \NyuCore::setException($e);
            $db->rollback();
            return false;
        } catch (\MysqlException $e) {
            \NyuCore::setException($e);
            $db->rollback();
            return false;
        } catch (\Exception $e) {
            \NyuCore::setException($e);
            $db->rollback();
            return false;
        }
    }

    /**
     * Carrega os dados de um objeto
     * @param string $searchField (Opcional) Se informado, utiliza este campo 
     * como chave para a exclusão do registro. Se for nulo, será buscado
     * um campo com o nome da tabela para efetuar a operação
     * @param array $cols (Opcional)(Desde 4.1) Array com o mesmo formato do 
     * atributo $cols, se informado, irá buscar apenas os campos indicados 
     * neste parâmetro, senão, buscará todos os campos do atributo $cols
     * @return boolean|\NyuModelAdo
     */
    public function load($searchField = null, $cols = false) {
        try {
            $db = \NyuDbAdo::getInstance();
            $class = get_called_class();
            $this->triggerBeforeLoad();
            $db->load($this, $class::getTable(), ($cols ? $cols : $class::getCols()), $searchField);
            $this->triggerAfterLoad();
            return $this;
        } catch (\PDOException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\MysqlException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\Exception $e) {
            \NyuCore::setException($e);
            return false;
        }
    }

    /**
     * Lista todos os registros da tabela, retorna um array de objetos
     * @param array $orderBy (Opcional) Array com os campos a ordenar a consulta
     * @param array $where (Opcional) Texto com a condição da busca
     * @param array $cols (Opcional)(Desde 4.1) Array com o mesmo formato do 
     * atributo $cols, se informado, irá buscar apenas os campos indicados 
     * neste parâmetro, senão, buscará todos os campos do atributo $cols
     * @param int $iniReg (Opcional) Número mínimo do registro - para limit
     * @param int $limit (Opcional) Quantidade máxima de registros retornados
     * @return array|boolean
     */
    public static function listAll($orderBy = null, $where = null, $cols = false, $iniReg = null, $limit = null) {
        try {
            $db = \NyuDbAdo::getInstance();
            $class = get_called_class();
            return $db->listAll(new $class(), $class::getTable(), ($cols ? $cols : $class::getCols()), $orderBy, $where, $iniReg, $limit);
        } catch (\PDOException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\MysqlException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\Exception $e) {
            \NyuCore::setException($e);
            return false;
        }
    }

    /**
     * Lista os registros da tabela a partir de um campo chave
     * @param string $key valor da chave a buscar os dados
     * @param string $searchField (Opcional) Nome do campo chave para fazer a 
     * busca (se nulo, irá considerar o nome da tabela como nome do campo chave 
     * primária)
     * @param array $order (Opcional) Array com os campos a ordenar a colsulta
     * @param string $where (Opcional) Texto com a condição da busca
     * @param array $cols (Opcional)(Desde 4.1) Array com o mesmo formato do 
     * atributo $cols, se informado, irá buscar apenas os campos indicados 
     * neste parâmetro, senão, buscará todos os campos do atributo $cols
     * @return array|boolean
     */
    public static function listByKey($key, $searchField = null, $order = null, $where = null, $cols = false) {
        try {
            
            $db = \NyuDbAdo::getInstance();
            $class = get_called_class();
            return $db->listByKey(new $class(), $class::getTable(), ($cols ? $cols : $class::getCols()), $key, $searchField, $order, $where);
        } catch (\PDOException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\MysqlException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\Exception $e) {
            \NyuCore::setException($e);
            return false;
        }
    }

    /**
     * Retorna o atributo $table com o nome da tabela da classe
     * @return string
     */
    public function getTable() {
        return static::$table;
    }
    /**
     * Retorna o atributo $cols com os atributos do objeto e seus campos 
     * correspondentes na tabela
     * @return array
     */
    public function getCols() {
        return static::$cols;
    }
    
    /**
     * Conta quantos registros existem na tabela do objeto
     * @param string $key (Opcional) Valor da chave para incluir na busca
     * @param string $searchField (Opcional) Nome do campo chave que será 
     * utilizado na busca
     * @return boolean
     */
    public function count($key = null, $searchField = null){
        try {
            
            $db = \NyuDbAdo::getInstance();
            $class = get_called_class();
            $c = $db->count($class::getTable(), $key, $searchField);
            return $c;
        } catch (\PDOException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\MysqlException $e) {
            \NyuCore::setException($e);
            return false;
        } catch (\Exception $e) {
            \NyuCore::setException($e);
            return false;
        }
    }
    
    /**
     * Executa um código antes de gravar o objeto. Deve ser sobrescrito na 
     * classe filha
     */
    public function triggerBeforeSave(){}
    
    /**
     * Executa um código depois de tentar gravar o objeto. É executado tanto 
     * se houver sucesso quanto se houver falha, após a chamada de 
     * triggerAfterSaveSuccess() e triggerAfterSaveFail(), respectivamente. 
     * Deve ser sobrescrito na classe filha
     */
    public function triggerAfterSave(){}
    
    /**
     * Executa um código depois de gravar com sucesso o objeto. Deve ser 
     * sobrescrito na classe filha
     */
    public function triggerAfterSaveSuccess(){}
    
    /**
     * Executa um código no caso de falha ao salvar o objeto. Deve ser 
     * sobrescrito na classe filha
     */
    public function triggerAfterSaveFail(){}
 
    /**
     * Executa um código antes de carregar o objeto. Deve ser sobrescrito na 
     * classe filha
     */
    public function triggerBeforeLoad(){}
    
    /**
     * Executa um código depois de carregar o objeto. Deve ser sobrescrito na 
     * classe filha
     */
    public function triggerAfterLoad(){}
    
    /**
     * Executa um código antes de apagar o objeto. Deve ser sobrescrito na 
     * classe filha
     */
    public function triggerBeforeDelete(){}
    
    /**
     * Executa um código depois de tentar apagar o objeto. É executado tanto 
     * se houver sucesso quanto se houver falha, após a chamada de 
     * triggerAfterDeleteSuccess() e triggerAfterDeleteFail(), respectivamente. 
     * Deve ser sobrescrito na classe filha
     */
    public function triggerAfterDelete(){}
    
    /**
     * Executa um código depois de apagar com sucesso o objeto. Deve ser 
     * sobrescrito na classe filha
     */
    public function triggerAfterDeleteSuccess(){}
    
    /**
     * Executa um código no caso de falha ao apagar o objeto. Deve ser 
     * sobrescrito na classe filha
     */
    public function triggerAfterDeleteFail(){}
    
    /**
     * Método utilizado para criar dinâmicamente os métodos set e get caso não 
     * existam
     * @param string $name nome do método
     * @param array $arguments argumentos do método
     * @return mixed retorno (atributo ou o objeto atual)
     * @since 1.8
     */
    public function __call($name, $arguments){
        if(strtolower(substr($name, 0, 3)) == "get"){
            $attribute = strtolower(substr($name, 3, 1)).substr($name, 4);
            return $this->$attribute;
        }elseif(strtolower(substr($name, 0, 3)) == "set"){
            $attribute = strtolower(substr($name, 3, 1)).substr($name, 4);
            $this->$attribute = $arguments[0];
            return $this;
        }
    }
    
    /**
     * Carrega o objeto a partir de sua chave
     * @param string $attribute atributo cujo valor será a chave do objeto carregado
     * @param string $class classe do objeto carregado
     * @param string $keyname (opcional) atributo utilizado como chave para o carregamento do objeto
     * @param string $module (opcional) módulo do objeto a carregar, se for diferente do atual
     * @param string $databaseconfig (opcional) configuração do banco de dados para o carregamento do objeto, se for diferente da atual
     * @return object objeto carregado
     */
    public function getObject($attribute, $class, $keyname = null, $fieldname = null, $module = null, $databaseconfig = null){
        if($module){
            $moduleTmp = \NyuCore::getModule();
            \NyuCore::setModule($module);
        }
        if($databaseconfig){
            $databaseconfigTmp = \NyuCore::getDatabaseConfig();
            \NyuCore::setDatabaseConfig($databaseconfig);
        }
        $obj = new $class();
        $class = str_replace("Model", "", $class);
        $method = ($keyname) ? "set{$keyname}" : "set{$class}";
        $obj->$method($this->$attribute);
        if(!$fieldname){
            $fieldname = $keyname;
        }
        $obj->load($fieldname);
        //echo "<br>";
        if($moduleTmp){
            \NyuCore::setModule($moduleTmp);
        }
        if(isset($databaseconfig)){
            \NyuCore::setDatabaseConfig($databaseconfigTmp);
        }
        
        return $obj;
    }
}
