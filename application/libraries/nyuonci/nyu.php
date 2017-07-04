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
    global $nyu__config;
    /* Classes do Nyu */
    if (file_exists(APPPATH."libraries/nyuonci/classes/".str_replace('\\', '/', $class).".class.php")){
        require_once (APPPATH."libraries/nyuonci/classes/".str_replace('\\', '/', $class).".class.php");
    /* Interfaces do Nyu */
    }elseif (file_exists(APPPATH."libraries/nyuonci/interfaces/".str_replace('\\', '/', $class).".interface.php")){
        require_once (APPPATH."libraries/nyuonci/interfaces/".str_replace('\\', '/', $class).".interface.php");
    /* traits do Nyu */
    }elseif (file_exists(APPPATH."libraries/nyuonci/traits/".str_replace('\\', '/', $class).".trait.php")){
        require_once (APPPATH."libraries/nyuonci/traits/".str_replace('\\', '/', $class).".trait.php");
    /* Classes customizadas */
    }elseif (file_exists(APPPATH."libraries/nyuonci/custom/classes/".str_replace('\\', '/', $class).".class.php")){
        require_once (APPPATH."libraries/nyuonci/custom/classes/".str_replace('\\', '/', $class).".class.php");
    /* Interfaces customizadas */
    }elseif (file_exists(APPPATH."libraries/nyuonci/custom/interfaces/".str_replace('\\', '/', $class).".interface.php")){
        require_once (APPPATH."libraries/nyuonci/custom/interfaces/".str_replace('\\', '/', $class).".interface.php");
    /* traits customizadas */
    }elseif (file_exists(APPPATH."libraries/nyuonci/custom/traits/".str_replace('\\', '/', $class).".trait.php")){
        require_once (APPPATH."libraries/nyuonci/custom/traits/".str_replace('\\', '/', $class).".trait.php");
    /* Classes customizadas (sem o sufixo .class) */
    }elseif (file_exists(APPPATH."libraries/nyuonci/custom/classes/".str_replace('\\', '/', $class).".php")){
        require_once (APPPATH."libraries/nyuonci/custom/classes/".str_replace('\\', '/', $class).".php");
    /* Interfaces customizadas (sem o sufixo .interface) */
    }elseif (file_exists(APPPATH."libraries/nyuonci/custom/interfaces/".str_replace('\\', '/', $class).".php")){
        require_once (APPPATH."libraries/nyuonci/custom/interfaces/".str_replace('\\', '/', $class).".php");
    /* traits customizadas (sem o sufixo .trait) */
    }elseif (file_exists(APPPATH."libraries/nyuonci/custom/traits/".str_replace('\\', '/', $class).".php")){
        require_once (APPPATH."libraries/nyuonci/custom/traits/".str_replace('\\', '/', $class).".php");
    }
    if($nyu__config['autoload_models']){
        if (file_exists(APPPATH."models/".str_replace('\\', '/', $class).".trait.php")){
            require_once (APPPATH."models/".str_replace('\\', '/', $class).".trait.php");
        }
    }
}

/* Força o carregamento do autoload do Nyu - para autoload de bibliotecas 
 * externas, como o Twig */
ini_set('unserialize_callback_func', 'spl_autoload_call');
spl_autoload_register('nyuAutoload');

