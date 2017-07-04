<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Utils\File;

/**
 * Classe simples de log
 */
class LogFile extends \Nyu\Core\CI{
    /**
     * Caminho da pasta em que será armazenado o log
     * @var string
     */
    protected $path;
    /**
     * Mome do arquivo de log
     * @var string
     */
    protected $filename;
    
    /**
     * Objeto de gerenciamento de arquivos
     * @var FileManager
     */
    protected $fm;
    
    /* Setters & Getters */
    function getPath() {
        return $this->path;
    }

    function getFilename() {
        return $this->filename;
    }

    function setPath($path) {
        $this->path = $path;
    }

    function setFilename($filename) {
        $this->filename = $filename;
    }
    
    /**
     * Construtor da classe
     * @param string $path Caminho do arquivo
     * @param string $filename Nome do arquivo
     * @param string $content Conteúdo a gravar
     */
    public function __construct($path = null, $filename = null, $content = null) {
        parent::__construct();
        
        if(isset($path)){
            $this->path = $path;
        }else{
            $this->path = APPPATH.'logs';
        }
        
        $this->fm = new \Nyu\Utils\File\FileManager($this->path, false);
        
        if(isset($filename)){
            $this->filename = $filename;
        }
        if(isset($content)){
            $this->log($content);
        }
    }

    /**
     * Grava um conteúdo no log
     * @param string $content
     */
    public function log($content, $filename = null){
        if(empty($this->filename) && empty($filename)){
            return false;
        }elseif(!empty($filename)){
            $this->filename = $filename;
        }
        $fileContent = $this->fm->loadFile($this->filename);
        $content = "<br/>" . date('Y-m-d h:i:s') . '<br/>' . $content . '<br/><br/>---------------------------------------------------<br/><br/>' . $fileContent;
        
        return $this->fm->saveFile($this->filename, $content);
    }
}