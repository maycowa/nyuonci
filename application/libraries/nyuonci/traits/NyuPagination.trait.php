<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Trait Responsável por criar a estrutura de paginação
 */
trait NyuPagination{
    
    /**
     * Método abstrato que retorna a collection de objetos que será utilizada na página
     * >> Sugestão de override:
     * <code>
       public static function getCollection($page, $itemsPerPage){
           $start = ($page-1)*$itemsPerPage;
           $list = static::listAll(null, null, false, $start, $itemsPerPage);
           return $list;
       }
      </code>
     * 
     * Para incluir filtros, adicionar na chamada e tratar no parâmetro where do método listAll();
     */
    public static abstract function getCollection();
    
    /**
     * Método abstrato que retorna a quantidade total de objetos em todas as páginas
     * >> Sugestão de override:
     * <code>
       public static function getCount(){
           return static::count();
       }
       </code>
     * 
     * Para incluir filtros, adicionar na chamada e tratar como consulta comum:
     * <code>
       public static function getCount($filters = null){
           $where = [];
           if($filters['field1']){
               $where[] = ' field1 = ? ';
               $bind[] = $filters['field1'];
           }
           $ret = static::query("select count(1) as c from table " . ($where ? implode(' and ', $where) : '', $bind));
           return $ret[0]['c'] ? $ret[0]['c'] : 0;
       }
       </code>
     */
    public static abstract function getCount();
    
    /**
     * Método abstrato que retorna a última página disponível
     * >> Sugestão de override:
     * <code>
       public static function getLastPage($itemsPerPage){
           $count = static::getCount();
           $page = ceil($count / $itemsPerPage);
           return $page;
       }
      </code> 
     * Para incluir filtros, adicionar na chamada e tratar no método getCount()
     * <code>
       public static function getLastPage($itemsPerPage, $filters = null){
           $count = static::getCount($filters);
           $page = ceil($count / $itemsPerPage);
           return $page;
       }
       </code>
     */
    public static abstract function getLastPage();

    /**
     * Monta os índices das páginas para os links Primeiro, Último, Próximo e 
     * Anterior da paginação e retorna em array
     * @param int $currentPage Página atual
     * @param int $itemsPerPage Quantidade de itens que irão ser carregados por página
     * @uses static::getLastPage() Exige a existência de um método chamado getLastPage() na classe
     * @return array
     */
    public static function getPagination($currentPage, $itemsPerPage){
        $class = get_called_class();
        $lastPage = $class::getLastPage($itemsPerPage);
        
        if($currentPage == 0){ // Se a página atual é zero, é a 1a página
            $currentPage = 1;
        }
        if($currentPage == 1){ // Se a página atual é a 1a
            $first = array('index' => null, // index = null carrega a primeira pagina
                           'disabled' => true); // disabled = true indica que está na 1a página
            $prev = array('index' => null, // index = null carrega a primeira pagina
                           'disabled' => true); // disabled = true indica que está na 1a página
        }
        if($currentPage == $lastPage){ // Se a página atual é a última
            // Link "Última"
            $last = array('index' => $currentPage == 1 ? null : $currentPage, // index = null carrega a primeira pagina
                          'disabled' => true); // disabled = true indica que está na ultima página
            // Link "Próxima"
            $next = array('index' => $currentPage == 1 ? null : $currentPage, // index = null carrega a primeira pagina
                          'disabled' => true); // disabled = true indica que está na ultima página
        }
        if(!isset($first)){
            $first = array('index' => null, // index = null carrega a primeira pagina
                           'disabled' => false); // disabled = false indica que há navegação
        }
        if(!isset($last)){
            $last = array('index' => $lastPage == 1 ? null : $lastPage, // index = null carrega a primeira pagina
                           'disabled' => false); // disabled = false indica que há navegação
        }
        if(!isset($prev)){
            $prev = array('index' => $currentPage - 1, // Página anterior
                           'disabled' => false); // disabled = false indica que há navegação
        }
        if(!isset($next)){
            $next = array('index' => $currentPage + 1, // Próxima página
                           'disabled' => false); // disabled = false indica que há navegação
        }
        
        /* Páginas:
         1a página, 2 páginas antes, página atual, 2 páginas depois, última página
         */
        $pages = [];
        
        foreach(range(1, $lastPage) as $page){
            if($page == $currentPage){ // Página atual, fica desativada
                $pages[] = array('index' => $page,
                                 'disabled' => true);
            }elseif($page == 1 || $page == $lastPage || ($page >= $currentPage - 2 && $page <= $currentPage + 2)){ // Outras páginas, fica habilitado
                $pages[] = array('index' => $page,
                                 'disabled' => false);
            }
        }
        
        $pagination = array('first' => $first, 'prev' => $prev, 'next' => $next, 'last' => $last, 'pages' => $pages);
        return $pagination;
    }
}