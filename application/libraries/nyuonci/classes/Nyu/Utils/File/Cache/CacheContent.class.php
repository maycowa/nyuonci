<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Utils\File\Cache;
/**
 * Classe do Nyu para gerenciar arquivos de cache
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package nyu\utils\cache
 * @version 1.0
 */
class CacheContent extends \Nyu\Core\CI{
    /**
     * Conteúdo que será salvo em cache
     * @var string
     */
    protected $content;
    /**
     * Referência do cache (nome do arquivo em que será salvo o cache)
     * @var string
     */
    protected $ref;
    /**
     * Pasta em que serão salvos os arquivos de cache
     * @var string
     */
    protected $folder;
    /**
     * Objeto NyuFileManager que irá manipular os arquivos
     * @var NyuFileManager
     */
    protected $fm;
    
    /**
     * Construtor da classe NyuCacheContent
     * @param string $folder Pasta em que serão salvos os arquivos de cache
     */
    function __construct($folder = null) {
        parent::__construct();
        $this->folder = SITE_FOLDER."/".$folder;
        $this->fm = new \Nyu\Utils\File\FileManager($this->folder);
    }
   
    /**
     * Retorna o atributo content
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * Retorna o atributo ref
     * @return string
     */
    public function getRef() {
        return $this->ref;
    }

    /**
     * Retorna o atributo folder
     * @return string
     */
    public function getFolder() {
        return $this->folder;
    }

    /**
     * Seta o atributo content
     * @param string $content Conteúdo que será salvo em cache
     */
    public function setContent($content) {
        $this->content = $content;
    }
    
    /**
     * Seta o atributo ref
     * @param string $ref Referência do cache (nome do arquivo em que será salvo o cache)
     */
    public function setRef($ref) {
        $this->ref = $ref.".nyucache";
    }

    /**
     * Seta o atributo folder
     * @param string $folder Pasta em que serão salvos os arquivos de cache
     */
    public function setFolder($folder) {
        $this->folder = $folder;
    }
    
    /**
     * Retorna o caminho do arquivo de cache atual
     * @return string
     */
    public function getCacheFilename(){
        return rtrim($this->folder,"/")."/".$this->ref;
    }

    /**
     * Salva o conteúdo (content) em cache utilizando a referência (ref)
     * @return boolean
     */
    public function saveCache(){
        return $this->fm->saveFile($this->ref, $this->content);
    }
    
    /**
     * Carrega um conteúdo cacheado no atributo content e retorna
     * @return string
     */
    public function loadCache(){
        $this->content = $this->fm->loadFile($this->ref);
        return $this->content;
    }
    
    /**
     * Verifica se um arquivo de cache existe com a referência
     * @return boolean
     */
    public function cacheExists(){
        return $this->fm->fileExists($this->ref);
    }
    
    /**
     * Apaga um cache a partir da referência
     * @return boolean
     */
    public function deleteCache(){
        return $this->fm->deleteFile($this->ref);
    }
    
    /**
     * Apaga todos os caches do site
     */
    public function deleteAllCache(){
        $this->fm->loadPath();
        $l = $this->fm->getFiles();
        if($l){
            foreach($l as $f){
                $this->fm->deleteFile($f);
            }
        }
    }
    
    /**
     * Verifica se o tempo do cache expirou, se sim, apaga o cache
     * @param int $cacheTime Tempo o qual o cache será guardado (em segundos)
     */
    public function cacheTime($cacheTime = false){
        if($this->fm->fileExists($this->ref)){
            $filecreation = filemtime($this->getCacheFilename());
        }
        $now = time();
        if($now - $filecreation >= $cacheTime){            
            $this->deleteCache();
        }
    }
}
