<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe de carregamento de bibliotecas de helper
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.0.1
 * @since 5.0
 */
class NyuHelperLoader{
    /**
     * Helpers que serão carregadas
     * @var array
     */
    protected $helpers;
    
    /**
     * Caminho da pasta a carregar as helpers
     * @var string
     */
    protected $path;
    
    public function __construct($helpers = false) {
        if($helpers){
            $this->helpers = $helpers;
        }else{
            $this->helpers = array();
        }
        $this->setPath(SITE_FOLDER."/helpers/");
    }
    
    /**
     * Adiciona uma biblioteca de helper no Loader
     * @param string $name
     * @return NyuHelperLoader O objeto atual
     */
    public function addHelper($name){
        $this->helpers[] = $name;
        return $this;
    }
    
    /**
     * Seta o caminho para a pasta de helpers que será utilizada
     * @param string $path
     * @return NyuHelperLoader O Objeto atual
     */
    public function setPath($path){
        $this->path = rtrim($path, "/")."/";
        return $this;
    }
    
    /**
     * Retorna o caminho para a pasta de helpers do objeto loader
     * @return string
     */
    public function getPath(){
        return $this->path;
    }
    
    /**
     * Carrega as helpers
     */
    public function load(){
        if(count($this->helpers) > 0){
            foreach($this->helpers as $helper){
                if(file_exists($this->path.$helper.".helper.php")){
                	require_once($this->path.$helper.".helper.php");
                }
            }
        }
    }
}