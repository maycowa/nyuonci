<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe para criar consultas sql
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.0
 * @since 4.0
 */
class NyuQueryBuilder{

    /**
     * Campos a buscar/combinação de campos a buscar
     * @var array
     */
    protected $fields = array();
    
    /**
     * Tabela principal da consulta
     * @var string
     */
    protected $table = "";
    
    /**
     * Tabelas a incluir como junção na consulta. Utiliza o seguinte padrão:
     * [][table]
     * [][condition]
     * [][type]
     * 
     * @var array
     */
    protected $join = array();
    
    /**
     * Condições da consulta. Utiliza seguinte padrão:
     * [][operator]
     * [][condition]
     * @var array
     */
    protected $where = array();
    
    /**
     * Campos a utilizar para a ordenação
     * @var array
     */
    protected $order = array();
    
    /**
     * Campos a utilizar para o agrupamento
     * @var array
     */
    protected $group = array();
    
    /**
     * Monta a expressão Limit do MySQL no formato limit [0],[1]
     * @var string
     */
    protected $limit = array();
    
    /**
     * Adiciona um campo na consulta
     * @param string $field Campo a adicionar na consulta
     * @return NyuQueryBuilder O objeto atual
     */
    public function addField($field){
        $this->fields[] = $field;
        return $this;
    }
    
    /**
     * Atribui todos os campos da consulta
     * @param array $fields array com os campos a buscar na consulta
     * @return NyuQueryBuilder O objeto atual
     */
    public function setFields($fields){
        $this->fields = $fields;
        return $this;
    }
    
    /**
     * Atribui a tabela a qual será feita a consulta
     * @param string $table Tabela a ser feita a consulta
     * @return NyuQueryBuilder O objeto atual
     */
    public function setTable($table){
        $this->table = $table;
        return $this;
    }
    
    /**
     * Adiciona uma junção de tabela à consulta
     * @param string $table Tabela
     * @param string $condition Condição
     * @param string $type Tipo da junção (inner/left/right)
     * @return NyuQueryBuilder O objeto atual
     */
    public function addJoin($table, $condition, $type=false){
        $join = array("table" => $table, 
                      "condition" => $condition, 
                      "type" => $type);

        $this->join[] = $join;
        return $this;
    }
    
    /**
     * Adiciona uma condição à consulta
     * @param string $condition Condição
     * @param string $operator Operador (and/or)
     * @return NyuQueryBuilder O objeto atual
     */
    public function addWhere($condition, $operator=false){
        $where = array("condition" => $condition, "operator" => $operator);
        $this->where[] = $where;
        return $this;
    }
    
    /**
     * Adiciona um campo na ordenação da consulta
     * @param string $order Campo a adicionar
     * @return NyuQueryBuilder O objeto atual
     */
    public function addOrder($order){
        $this->order[] = $order;
        return $this;
    }
    
    /**
     * Atribui todos os campos da ordenação da consulta
     * @param array $order array com os campos a ordenar
     * @return NyuQueryBuilder O objeto atual
     */
    public function setOrder($order){
        $this->order = $order;
        return $this;
    }

    /**
     * Adiciona um campo no agrupamento da consulta
     * @param string $group Campo a adicionar
     * @return NyuQueryBuilder O objeto atual
     */
    public function addGroup($group){
        $this->group[] = $group;
        return $this;
    }
    
    /**
     * Atribui todos os campos do agrupamento da consulta
     * @param array $group array com os campos a agrupar
     * @return NyuQueryBuilder O objeto atual
     */
    public function setGroup($group){
        $this->group = $group;
        return $this;
    }
    
    /**
     * Atribui os valores de limite para uma consulta - utilizado em MySQL
     * @param int $limit1 Primeiro valor do limite
     * @param int $limit2 Segundo valor do limite
     * @return NyuQueryBuilder O objeto atual
     */
    public function setLimit($limit1, $limit2=null){
        $this->limit = array($limit1, $limit2);
        return $this;
    }

    /**
     * Gera a consulta SQL
     * @return string A consulta gerada
     */
    public function build(){
        $sql = "SELECT ";
        
        /* Campos */
        if(!$this->fields){
            throw new NyuQueryBuilderException("Campos não informados para a criação da consulta.");
        }else{
            $first = true;
            foreach($this->fields as $fields){
                $sql .= (($first)?"":", ").$fields;
                $first = false;
            }
        }
        
        /* Tabela Principal */
        if(!$this->table){
            throw new NyuQueryBuilderException("Tabela não informada para a criação da consulta.");
        }else{
            $sql .= " FROM ".$this->table." ";
        }
        
        /* Junções */
        if($this->join){
            foreach($this->join as $join){
                $sql .= " ".$join['type']." JOIN ".$join['table']." ON ".$join['condition']." ";
            }
        }
        
        /* Condição */
        if($this->where){
            $first = true;
            foreach($this->where as $where){
                if(!$where['operator'] && !$first){
                    throw new NyuQueryBuilderException("É necessário informar o operador para a condição '".$where['condition']."'");
                }
                if($where['condition']){
                    $sql .= (($first)?" WHERE ".$where['condition']." ":$where['operator']." ".$where['condition']." ");
                    $first = false;
                }
            }
        }
        
        /* Agrupamento */
        if($this->group){
            $sql .= " GROUP BY ";
            $first = true;
            foreach($this->group as $group){
                $sql .= (($first)?"":", ").$group;
                $first = false;
            }
        }
        
        /* Ordenação */
        if($this->order){
            $sql .= " ORDER BY ";
            $first = true;
            foreach($this->order as $order){
                $sql .= (($first)?"":", ").$order;
                $first = false;
            }
        }
        
        if($this->limit){
            if(!$this->limit[0]){
                throw new NyuQueryBuilderException("É necessário informar um limite inicial.");
            }else{
                $sql .= " LIMIT ".$this->limit[0] . (($this->limit[1])?", ".$this->limit[1]:"");
            }
        }
        
        return $sql;
    }
    
    /**
     * Imprime a consulta gerada ao invés de imprimir o objeto
     * @return string A consulta gerada
     */
    public function __toString() {
        return $this->build();
    }
}
?>