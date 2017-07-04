<?php
/**
 * Core do sistema Nyu
 * @package NyuCore
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 */

/* Inicia a Sessão */
session_start();

/* Configurações do Sistema */
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "config.php");

/* Constantes necessárias */
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "nyu_constants.helper.php");

/**
 * Autocarregamento de Classes
 * @param string $class Nome da classe a buscar
 * @version 3.0
 */
function nyuAutoload($class){
    
    /* Classes do core do sistema */
    if (file_exists(APPPATH."libraries/nyuonci/classes/".str_replace('\\', '/', $class).".class.php")){
        require_once (APPPATH."libraries/nyuonci/classes/".str_replace('\\', '/', $class).".class.php");
    /* MVC */
    }elseif(file_exists(SITE_FOLDER.Nyu\Core\Config::getConfig('mvc', 'controllers_path').str_replace('\\', '/', $class).".class.php")){
        require_once (SITE_FOLDER.Nyu\Core\Config::getConfig('mvc', 'controllers_path').str_replace('\\', '/', $class).".class.php");
    }elseif(file_exists(SITE_FOLDER.Nyu\Core\Config::getConfig('mvc', 'models_path').str_replace('\\', '/', $class).".class.php")){
        require_once (SITE_FOLDER.Nyu\Core\Config::getConfig('mvc', 'models_path').str_replace('\\', '/', $class).".class.php");
    /* Libs diretas (sem diretório da lib) */
    }elseif (file_exists(SITE_FOLDER.'/'.Nyu\Core\Config::getConfig("lib_folder").'/'.str_replace('\\', '/', $class).".class.php")){ // Classes em libs
        require_once (SITE_FOLDER.'/'.Nyu\Core\Config::getConfig("lib_folder").'/'.str_replace('\\', '/', $class).".class.php");
    /* Interfaces do sistema */
    }elseif (file_exists(APPPATH."libraries/nyuonci/interfaces/".str_replace('\\', '/', $class).".interface.php")){
        require_once (APPPATH."libraries/nyuonci/interfaces/".str_replace('\\', '/', $class).".interface.php");
    /* traits do sistema */
    }elseif (file_exists(APPPATH."libraries/nyuonci/traits/".str_replace('\\', '/', $class).".trait.php")){
        require_once (APPPATH."libraries/nyuonci/traits/".str_replace('\\', '/', $class).".trait.php");
    /* Classes customizadas do site */
    }elseif (file_exists("siteclasses/classes/".str_replace('\\', '/', $class).".class.php")){
        require_once ("siteclasses/classes/".str_replace('\\', '/', $class).".class.php");
    /* Interfaces customizadas do site */
    }elseif (file_exists("siteclasses/interfaces/".str_replace('\\', '/', $class).".interface.php")){
        require_once ("siteclasses/interfaces/".str_replace('\\', '/', $class).".interface.php");
    /* traits customizadas do site */
    }elseif (file_exists("siteclasses/traits/".str_replace('\\', '/', $class).".trait.php")){
        require_once ("siteclasses/traits/".str_replace('\\', '/', $class).".trait.php");
    }
}

/* Força o carregamento do autoload do Nyu - para autoload de bibliotecas 
 * externas, como o Twig */
ini_set('unserialize_callback_func', 'spl_autoload_call');
spl_autoload_register('nyuAutoload');

