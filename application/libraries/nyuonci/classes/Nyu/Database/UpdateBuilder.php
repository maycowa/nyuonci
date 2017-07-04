<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Database;

/**
 * Gera uma string de insert
 */
class UpdateBuilder extends \Nyu\Core\CI{

    /**
     * A tabela a inserir
     * @var string
     */
    protected $table;

    /**
     * Os campos a inserir
     * @var array
     */
    protected $values;

    /**
     * As condições do update
     * @var array
     */
    protected $where;

    /**
     * Os tipos dos campos a inserir
     */
    protected $types;

    /**
     * Os tipos das condições do update
     */
    protected $typeWhere;

    /**
     * Resultado do método appendToBuild()
     * @var string
     */
    protected $append = '';

    /**
     * Resultado do método prependToBuild()
     * @var string
     */
    protected $prepend = '';

    /**
     * Tipo de campo texto (string, data, etc)
     * @var string
     */
    public static $TYPE_STRING = 'string';

    /**
     * Tipo de campo número
     * @var string
     */
    public static $TYPE_NUMBER = 'number';

    /**
     * Retorna um novo objeto do tipo UpdateBuilder
     */
    public static function factory() {
        return new self();
    }
    
    /**
     * Construtor da classe
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Seta a tabela
     * @param string $table
     * @return \Nyu\Database\UpdateBuilder O objeto atual
     */
    public function setTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Retorna a tabela
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Adiciona um campo no objeto
     * @param string $field
     * @param string $value
     * @param string type Tipo do campo, padrão "string"
     * @return \Nyu\Database\UpdateBuilder O objeto atual
     */
    public function addValue($field, $value, $type = 'string') {
        $this->values[$field] = $value;
        $this->types[$field] = $type;
        return $this;
    }

    /**
     * Adiciona um campo na lista de condições
     * @param string $condition
     * @param string $value
     * @return \Nyu\Database\UpdateBuilder O objeto atual
     */
    public function addWhere($condition, $value = null, $type = 'string') {
        $this->where[$condition] = $value;
        $this->typeWhere[$condition] = $type;
        return $this;
    }

    /**
     * Retorna o valor de um campo
     * @param string $field
     * 
     */
    public function getValue($field) {
        return $this->values[$field];
    }

    /**
     * Retorna o tipo de um campo
     * @param string $field
     * 
     */
    public function getType($field) {
        return $this->types[$field];
    }

    /**
     * Executa o método build e retorna
     */
    public function __toString() {
        return $this->build();
    }

    /**
     * Constrói a string de insert
     * @return string
     */
    public function build() {

        $update = '';
        if ($this->prepend != '') {
            $update .= "\n{$this->prepend}\n";
        }

        $update .= "update {$this->table} set";

        $valuesString = '';
        foreach ($this->values as $key => $value) {
            if ($value === null) {
                $valuesString .= "{$key} = null,";
            } elseif ($this->types[$key] == 'string') {
                $valuesString .= "{$key} = '{$value}',";
            } else {
                $valuesString .= "{$key} = {$value},";
            }
        }

        if (substr($valuesString, -1) == ',') {
            $valuesString = substr($valuesString, 0, strlen($valuesString) - 1);
        }
        $update .= " {$valuesString}";

        if (count($this->where) > 0) {
            $whereString = ' where ';

            foreach ($this->where as $key => $value) {
                if ($value === null) {
                    $whereString .= "{$key} and ";
                } elseif ($this->typeWhere[$key] == 'string') {
                    $whereString .= "{$key} = '{$value}' and ";
                } else {
                    $whereString .= "{$key} = {$value} and ";
                }
            }

            if (substr($whereString, -4) == 'and ') {
                $whereString = substr($whereString, 0, strlen($whereString) - 4);
            }
            $update .= " {$whereString};\n";
        } else {
            $update .= ";\n";
        }

        if ($this->append != '') {
            $update .= "\n{$this->append}\n";
        }

        return $update;
    }

    /**
     * Adiciona um código ao build
     * @param string|function $callback se $callback é uma string,
     * insere o texto após a construção da string de update, se é
     * uma função, executa a função passando o objeto atual como 
     * parâmetro e insere o retorno após a construção da string de
     * update
     * @return \Nyu\Database\UpdateBuilder O objeto atual
     */
    public function appendToBuild($callback) {
        if (is_callable($callback)) {
            $this->append = $callback($this);
        } else {
            $this->append = $callback;
        }
        return $this;
    }

    /**
     * Adiciona um código antes do build
     * @param string|function $callback se $callback é uma string,
     * insere o texto antes da construção da string de update, se é
     * uma função, executa a função passando o objeto atual como 
     * parâmetro e insere o retorno antes da construção da string de
     * insert
     * @return \Nyu\Database\UpdateBuilder O objeto atual
     */
    public function prependToBuild($callback) {
        if (is_callable($callback)) {
            $this->prepend = $callback($this);
        } else {
            $this->prepend = $callback;
        }
        return $this;
    }

}
