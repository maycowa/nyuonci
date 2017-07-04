<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Utils\File\Cache;
/**
 * Controller padrão para tratar views cacheadas
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.0
 */
class CachedController extends \Nyu\Core\Controller{
    protected $prefix;
    protected $template;
    protected $cache;
    
    public function __construct() {
        parent::__construct();
        $this->template = new NyuTemplate();
        $this->cache = new NyuCacheContent(\Nyu\Core\Config::getConfig("mvc", "cache_path"));
    }
    
    /**
     * Salva o cache de uma view
     * @param string $view
     * @param array $vars
     */
    public function __saveCache($view = false, $vars = array()){
        if($vars){
            foreach($vars as $k => $var){
                $this->template->addVar($k, $var);
            }
        }
        $html = $this->returnTemplate($this->prefix.$view);
        $this->cache->setContent($html);
        $this->cache->saveCache();
        echo $html;
    }
    
    /**
     * Deleta um cache
     * @param string $view
     */
    protected function __delete($view){
        $this->cache->setRef($this->prefix.$view);
        $this->cache->deleteCache();
    }
    
    /**
     * Deleta todos os caches
     */
    protected function __deleteAll(){
        $this->cache->deleteAllCache();
    }
    
    /**
     * Seta a ref para o cache com o prefixo do módulo
     * @param string $view
     */
    protected function __ref($view){
        $this->cache->setRef($this->prefix.$view);
    }
    
    /**
     * Carrega uma view cacheada se existir
     * @param int $cacheTime Tempo o qual o cache será guardado (em segundos)
     */
    protected function __loadCache($cacheTime = false){
        if($cacheTime){
            $this->cache->cacheTime($cacheTime);
        }
        if($this->cache->cacheExists()){
            echo $this->cache->loadCache();
            exit;
        }
    }
}
