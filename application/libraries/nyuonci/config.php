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

// Se o debug está ligado
/*if(isset($nyu__debug) && $nyu__debug == true){
    ini_set("display_errors", 1); 
    error_reporting(1);
}else{
    ini_set("display_errors", 0);
}*/

/**
 * Nome do Sistema
 */
define('_SYS_NAME_',"Nyu on CodeIgniter");
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
/**
 * Nome do Site
 */
define('SITE_NAME', $nyu__config['about']['site_name']);
/**
 * Nome do Site para variáveis do sistema
 */
define('SITE_NAME_SYS', $nyu__config['about']['site_name_sys']);

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
 * Logo do Nyu
 */
//define('NYU_LOGO',SITE_URL."nyu/adminmedia/img/nyu-logo.png");
///**
// * Logo do Nyu | Admin
// */
//define('NYU_ADMIN_LOGO',SITE_URL."nyu/adminmedia/img/nyu-logo.png");
///**
// * Url da administração do Nyu
// */
//define('NYU_ADMIN_URL', SITE_URL.'_nyuadmin/');
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
/**
 * Indica se o sistema irá ignorar os casos de página não encontrada e irá 
 * redirecionar para a página principal
 */
define('_IGNORE_NOT_FOUND_PAGE_', $nyu__config['misc']['ignore_not_found_page']);
/**
 * Indica se o sistema irá fazer cache das views por padrão
 */
define('_CACHE_VIEWS_', $nyu__config['misc']['cache_views']);
/**
 * Definição de arquivos de erros HTTP: 401
 */
define('_401_ERROR_FILE_', $nyu__config['misc']['401_error_file']);
/**
 * Definição de arquivos de erros HTTP: 403
 */
define('_403_ERROR_FILE_', $nyu__config['misc']['403_error_file']);
/**
 * Definição de arquivos de erros HTTP: 404
 */
define('_404_ERROR_FILE_', $nyu__config['misc']['404_error_file']);
/**
 * Definição de arquivos de erros HTTP: 500
 */
define('_500_ERROR_FILE_', $nyu__config['misc']['500_error_file']);
/**
 * Definição de arquivos de erros: default
 */
define('_DEFAULT_ERROR_FILE_', $nyu__config['misc']['default_error_file']);
/**
 * Indica se o módulo de administração do Nyu está ativado
 */
define('_NYU_ADMIN_', $nyu__config['misc']['nyu_admin']);
/**
 * Registro de log: 1: Apenas transações; 2: Transações e consultas de objeto; 
 * 3: Transações, consultas de objeto e consultas simples
 */
define('_LOG_',1);

/* ini_sets necessários */
date_default_timezone_set('America/Sao_Paulo');
ini_set("session.gc_maxlifetime", 3600 * 12);
ini_set("max_execution_time", 600);
ini_set("memory_limit", "256M");
ini_set("session.name", 'sess_'.SITE_NAME_SYS);
ini_set("default_charset", 'utf-8');
ini_set('post_max_size', '200M');
ini_set('upload_max_filesize', '200M');

//header('Content-Type: text/html; charset=utf-8');
/*ini_set("session.save_path", SITE_URL."/data/sess/sessions");
ini_set("session.cookie_path", SITE_URL."/data/sess/cookies");*/
