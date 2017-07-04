<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Core;
/**
 * Classe Model com paginação simples, sem filtros. Extende a classe NyuModel, 
 * herdando todos os seus métodos. Para adicionar filtros para a paginação, 
 * extender esta classe e modificar seus métodos, ou criar uma nova classe
 * extendendo a NyuModel e utilizando a Trait NyuPagination.
 * @uses NyuPagination A Trait padrão responsável por tratar da paginação
 */
class SimplePaginationModel extends \Nyu\Core\Model{
    /**
     * Chama a Trait NyuPagination, responsável por montar o array com os 
     * índices para construir os links de paginação
     */
    use \Nyu\Core\Pagination;
    /**
     * Método que retorna a collection de objetos que será utilizada na página
     * 
     * @param int $page Página atual
     * @param int $itemsPerPage Quantidade de itens por página
     * @return array
     */
    public static function getCollection($page, $itemsPerPage){
        $start = ($page-1)*$itemsPerPage;
        $list = static::listAll(null, null, false, $start, $itemsPerPage);
        return $list;
    }
    /**
     * Método que retorna a quantidade total de objetos em todas as páginas
     * 
     * @return int
     */
    public static function getCount() {
        return static::count();
    }
    /**
     * Retorna a última página disponível
     * 
     * @param int $itemsPerPage
     * @return int
     */
    public static function getLastPage($itemsPerPage) {
        $count = static::getCount();
        $page = ceil($count / $itemsPerPage);
        return $page;
    }
}