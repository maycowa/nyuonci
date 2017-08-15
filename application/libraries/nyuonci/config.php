<?php
/**
 * Arquivo de configurações do sistema
 * @package NyuConfig
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @version 2.0
 */

/**
 * Define a constante __DIR__ se não existir (versões anteriores do PHP)
 */
if(!defined('__DIR__')){
	define("__DIR__", dirname(__FILE__));
}
/**
 * Busca as configurações, variáveis e constantes personalizadas do site
 */
if(file_exists(APPPATH."config/nyu-config.php")){
    require_once(APPPATH."config/nyu-config.php");
}

/**
 * Pasta raiz do sistema
 */
define('SITE_FOLDER', dirname(__DIR__));

/**
 * Nome do Sistema
 */
define('_SYS_NAME_',"Nyu on CodeIgniter");

/**
 * Para sessão interna do sistema
 */
define('SITE_NAME_SYS',"NyuOnCI");
/**
 * Descrição da Versão do Sistema
 */
define('_SYS_VERSION_',"1.0 (Based on Nyu 6.0)");
/**
 * Versão do Sistema
 */
define('_SYS_VERSION_CODE_',"1.0");
/**
 * Idioma Utilizado no sistema
 */
define('_LANG_',"pt-br");

$site_url_http = explode("/",$_SERVER['SERVER_PROTOCOL']);
$tmp_get_site_url = trim(str_replace(@$_GET['get'], "", $_SERVER["REQUEST_URI"]), "/");
$site_url = strtolower($site_url_http[0])."://".$_SERVER['SERVER_NAME'].(substr($_SERVER['SERVER_NAME'], strlen($_SERVER['SERVER_NAME']) - 1, 1) == "/" ? "" : "/").$tmp_get_site_url;
$site_url = trim($site_url, "/")."/";
/**
 * A url do sistema
 */
define('SITE_URL', $site_url);

$currentUrl = 'http';
if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on"){
    $pageURL .= "s";
}
$currentUrl .= "://";
if($_SERVER["SERVER_PORT"] != "80"){
    $currentUrl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
}else{
    $currentUrl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
}
define('CURRENT_URL', $currentUrl);
/**
 * Caminho padrão dos arquivos
 */
define("SITE_PATH", "/");
/**
 * Separador de pasta
 */
define('DS', DIRECTORY_SEPARATOR);
/**
 * Salt para a administração do Nyu
 */
define('NYU_ADMIN_SALT', 'QcvOFdADzuustVSmpEn4jV1vxi9Sbxuka5Jqc4vf4XH');
/**
 * Host padrão do banco de dados
 */
@define('_DB_HOST_', $nyu__config['database']['default']['host']);
/**
 * Host padrão do banco de dados
 */
@define('_DB_NAME_', $nyu__config['database']['default']['name']);
/**
 * Usuário padrão do banco de dados
 */
@define('_DB_USR_', $nyu__config['database']['default']['user']);
/**
 * Senha padrão do banco de dados
 */
@define('_DB_PSW_',  $nyu__config['database']['default']['password']);
/**
 * Driver padrão do banco de dados
 */
@define('_DB_DRIVER_',  $nyu__config['database']['default']['driver']);
/**
 * String padrão de comunicação do banco de dados
 */
@define('_DB_STR_',"mysql:host="._DB_HOST_.";dbname="._DB_NAME_);

/* ini_sets necessários */
date_default_timezone_set('America/Sao_Paulo');
ini_set("session.gc_maxlifetime", 3600 * 12);
ini_set("max_execution_time", 600);
ini_set("memory_limit", "256M");
//ini_set("session.name", 'sess_'.SITE_NAME_SYS);
ini_set("default_charset", 'utf-8');
ini_set('post_max_size', '200M');
ini_set('upload_max_filesize', '200M');

//header('Content-Type: text/html; charset=utf-8');
/*ini_set("session.save_path", SITE_URL."/data/sess/sessions");
ini_set("session.cookie_path", SITE_URL."/data/sess/cookies");*/
