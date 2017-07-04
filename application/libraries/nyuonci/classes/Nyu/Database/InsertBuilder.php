<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Database;

/**
 * Gera uma string de insert
 */
class InsertBuilder extends \Nyu\Core\CI{

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
     * Os tipos dos campos a inserir
     */
    protected $types;

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
     * Retorna um novo objeto do tipo Insert
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
     * @return \Nyu\Database\InsertBuilder O objeto atual
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
     * @return \Nyu\Database\InsertBuilder O objeto atual
     */
    public function addValue($field, $value, $type = 'string') {
        $this->values[$field] = $value;
        $this->types[$field] = $type;
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

        $insert = '';
        if ($this->prepend != '') {
            $insert .= "\n{$this->prepend}\n";
        }

        $fields = array_keys($this->values);
        $fieldsString = implode($fields, ',');
        if (substr($fieldsString, -1) == ',') {
            $fieldsString = substr($fieldsString, 0, strlen($fieldsString) - 1);
        }
        $insert .= "insert into {$this->table} ({$fieldsString}) values (";

        $valuesString = '';
        foreach ($this->values as $key => $value) {
            if ($value === null) {
                $valuesString .= "null,";
            } elseif ($this->types[$key] == 'string') {
                $valuesString .= "'{$value}',";
            } else {
                $valuesString .= "{$value},";
            }
        }

        if (substr($valuesString, -1) == ',') {
            $valuesString = substr($valuesString, 0, strlen($valuesString) - 1);
        }
        $insert .= "{$valuesString});\n";

        if ($this->append != '') {
            $insert .= "\n{$this->append}\n";
        }

        return $insert;
    }

    /**
     * Adiciona um código ao build
     * @param string|function $callback se $callback é uma string,
     * insere o texto após a construção da string de insert, se é
     * uma função, executa a função passando o objeto atual como 
     * parâmetro e insere o retorno após a construção da string de
     * insert
     * @return \Nyu\Database\InsertBuilder O objeto atual
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
     * insere o texto antes da construção da string de insert, se é
     * uma função, executa a função passando o objeto atual como 
     * parâmetro e insere o retorno antes da construção da string de
     * insert
     * @return \Nyu\Database\InsertBuilder O objeto atual
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
