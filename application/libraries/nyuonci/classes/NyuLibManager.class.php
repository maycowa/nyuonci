<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe que gerencia as libs instaladas, para facilitar a utilização e 
 * manutenção, e autocarregamento (se a lib não possuir um próprio)
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.1
 * @since 2.1
 */
class NyuLibManager{
    /**
     * Armazena os caminhos de libs dentro da pasta lib
     * @var array
     */
    protected $paths = array();
    
    /**
     * Construtor da classe NyuLibManager
     * @param array $paths Array com os caminhos de libs dentro da pasta lib que serão feitas as buscas
     */
    public function __construct($paths = false) {
        if($paths){
            $this->paths = $paths;
        }
    }
    
    /**
     * Adiciona um caminho de lib à lista de libs
     * @param string $path
     * @return NyuLibManager O objeto atual
     */
    public function addPath($path){
        $this->paths[] = $path;
        return $this;
    }
    
    /**
     * Cria um autocarregamento para as libs carregadas na lista
     */
    public function autoloadLibs(){
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array($this, 'autoloadLibFinder'));
    }
    
    /**
     * Carrega as classes a partir das libs
     * Tenta identificar o arquivo da lib e carrega
     * @param string $class Nome da Classe
     */
    protected function autoloadLibFinder($class){
        $tmp = explode("_", $class);
        
        foreach($this->paths as $path){
            if (substr($path, -1, 1) != '/') {
                $path .= "/";
            }
            if(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".class.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".class.php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".cls.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".cls.php");
            }elseif(file_exists(SITE_FOLDER."libs/".$path.$class.".lib.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".lib.php");
            }elseif(file_exists(SITE_FOLDER."libs/".$path."class.".$class.".php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path."class.".$class.".php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".classe.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$class.".classe.php");
            }elseif(isset($tmp[1]) && file_exists(SITE_FOLDER."libs/".$path.$tmp[0]."/".$tmp[1].".php")){
                require_once(SITE_FOLDER."libs/".$path.$tmp[0]."/".$tmp[1].".php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".class.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".class.php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".cls.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".cls.php");
            }elseif(file_exists(SITE_FOLDER."libs/".$path.strtolower($class).".lib.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path."class.".strtolower($class).".php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path."class.".strtolower($class).".php");
            }elseif(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".classe.php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.strtolower($class).".classe.php");
            }elseif(isset($tmp[1]) && file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$tmp[0]."/".$tmp[1].".php")){
                require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path.$tmp[0]."/".strtolower($tmp[1]).".php");
            }
        }
    }


    /**
     * Retorna os caminhos de libs incluídos
     * @return array
     */
    public function getPaths(){
        return $this->paths;
    }
    
    /**
     * Carrega um arquivo da pasta libs.
     * Utilizado para os casos em que não é possível carregar o arquivo 
     * automaticamente
     * @param string $path Caminho do arquivo dentro da pasta libs
     * @since 4.0
     */
    public static function loadLibFile($path){
        if(file_exists(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path)){
            require_once(SITE_FOLDER."/".NyuConfig::getConfig("lib_folder")."/".$path);
        }
    }
}