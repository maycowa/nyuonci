<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe que altera o padrão de chamada de primaryKey na classe NyuModel
 * Por padrão, a classe utiliza um campo com o mesmo nome da tabela como chave 
 * primária. Esta classe adiciona o campo estático $primaryKey, que permite
 * personalizar a chave primária para todas as chamadas da classe, não havendo
 * necessidade de alterar todos os métodos em cada classe.
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.0
 */
class NyuKeyModel extends NyuModel{
    
    /**
     * Campo que referencia a chave do registro no banco de dados. Deve ser sobrescrito 
     * na classe filha
     * @var string 
     */
    public static $primaryKey = "key";
     
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
        if($searchField == null){
            $searchField = static::$primaryKey;
        }
        return parent::save($searchField, $cols);
    }
    
    /**
     * Carrega os dados de um objeto
     * @param string $searchField (Opcional) Se informado, utiliza este campo 
     * como chave para a exclusão do registro. Se for nulo, será buscado
     * um campo com o nome da tabela para efetuar a operação
     * @param array $cols (Opcional)(Desde 4.1) Array com o mesmo formato do 
     * atributo $cols, se informado, irá buscar apenas os campos indicados 
     * neste parâmetro, senão, buscará todos os campos do atributo $cols
     * @return boolean|\NyuModel
     */
    public function load($searchField = null, $cols = false) {
        if($searchField == null){
            $searchField = static::$primaryKey;
        }
        return parent::load($searchField, $cols);
    }
    
    /**
     * Apaga um registro do banco de dados
     * @param string $searchField (Opcional) Se informado, utiliza este campo 
     * como chave para a exclusão do registro. Se for nulo, será buscado
     * um campo com o nome da tabela para efetuar a operação
     * @return boolean
     */
    public function delete($searchField = null) {
        if($searchField == null){
            $searchField = static::$primaryKey;
        }
        return parent::delete($searchField);
    }
    
    /**
     * Conta quantos registros existem na tabela do objeto
     * @param string $key (Opcional) Valor da chave para incluir na busca
     * @param string $searchField (Opcional) Nome do campo chave que será 
     * utilizado na busca
     * @return boolean
     */
    public function count($key = null, $searchField = null) {
        if($searchField == null){
            $searchField = static::$primaryKey;
        }
        return parent::count($key, $searchField);
    }
}