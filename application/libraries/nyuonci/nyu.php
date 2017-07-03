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
    }elseif(file_exists(SITE_FOLDER.NyuConfig::getConfig('mvc', 'controllers_path').str_replace('\\', '/', $class).".class.php")){
        require_once (SITE_FOLDER.NyuConfig::getConfig('mvc', 'controllers_path').str_replace('\\', '/', $class).".class.php");
    }elseif(file_exists(SITE_FOLDER.NyuConfig::getConfig('mvc', 'models_path').str_replace('\\', '/', $class).".class.php")){
        require_once (SITE_FOLDER.NyuConfig::getConfig('mvc', 'models_path').str_replace('\\', '/', $class).".class.php");
    /* Libs diretas (sem diretório da lib) */
    }elseif (file_exists(SITE_FOLDER.'/'.NyuConfig::getConfig("lib_folder").'/'.str_replace('\\', '/', $class).".class.php")){ // Classes em libs
        require_once (SITE_FOLDER.'/'.NyuConfig::getConfig("lib_folder").'/'.str_replace('\\', '/', $class).".class.php");
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

/* Autoload de libs */
$nyuLibManager = new NyuLibManager();
$nyuLibManager->addPath("FPDF");
$nyuLibManager->addPath("Mobile_Detect");
$nyuLibManager->addPath("PHPMailer");
$nyuLibManager->addPath("WideImage");
$nyuLibManager->addPath("PHPExcel");
$nyuLibManager->addPath("PHPWord");
$nyuLibManager->autoloadLibs(); // Registra o método de autocarregamento para as pastas das libs padrão
NyuLibManager::loadLibFile("adodb/adodb-exceptions.inc.php"); // Carrega o arquivo de tratamento de erros do ADOdb para PDO
NyuLibManager::loadLibFile("adodb/adodb-errorhandler.inc.php"); // Carrega o arquivo de tratamento de erros do ADOdb
NyuLibManager::loadLibFile("adodb/adodb.inc.php"); // Carrega a biblioteca do ADOdb

/* Autoload do Composer */
if(file_exists(SITE_FOLDER.'/'.NyuConfig::getConfig("lib_folder").'/'.'autoload.php')){
    require_once(SITE_FOLDER.'/'.NyuConfig::getConfig("lib_folder").'/'.'autoload.php');
}

/* Autoload de helpers */
/* Helpers do sistema */
$nyuHelperLoader = new NyuHelperLoader();
$nyuHelperLoader->setPath(SITE_FOLDER."/nyu/helpers")->addHelper("nyu_constants")->load();
/* Helpers do usuário */
$nyuHelperLoader = new NyuHelperLoader($nyu__helpers);
$nyuHelperLoader->load();

/**
 * Tipo de log do ADOdb ativo
 */
define('ADODB_ERROR_LOG_TYPE', 3);
/**
 * Arquivo de Log do ADOdb
 */
define('ADODB_ERROR_LOG_DEST', SITE_FOLDER.'/sitedata/adodb_errors.log');

/* Extrai a query string */
$nyu__qs = $_GET['get'];

/* Remove a barra do início */
if(substr($nyu__qs, 0, 1) == "/" && strlen($nyu__qs) > 1){
    $nyu__qs = substr($nyu__qs, 1);
}

/* Rotas */
if(count($nyu__config['routes']) > 0){
    foreach($nyu__config['routes'] as $rts => $value){       
        if(substr($nyu__qs."/", 0, (strlen($rts)+1)) == $rts."/"){
        //if(strpos($nyu__qs, $rts) === 0){
            $qs_tmp = preg_replace('|'.$rts.'/|', '', $nyu__qs."/", 1);
            $gets_tmp = explode("/", $value.($qs_tmp ? "/".$qs_tmp:""));
            break;
        }
    }
    
    if($gets_tmp){
        //$gets = explode("/", $nyu__config['routes'][$nyu__qs]);
        $gets = $gets_tmp;
    }else{
        $gets = explode("/",$nyu__qs);
    }
}else{
    $gets = explode("/",$nyu__qs);
}

/* Tratamento de URLs Amigáveis */
foreach($gets as $get){
    if($get){ // Ignora as excessões
        $GET[] = $get;
    }
}

/* Hack de processamento */
if(isset($nyu__config['hack']) && $nyu__config['hack'] != ''){
    $hack_class = $nyu__config['hack'];
    $nyu__hack = new $hack_class();
}

/**
 * Redireciona para o pré-instalador se ainda não foi configurado (e se está habilitada a administração)
 */
if(_NYU_ADMIN_ && (file_exists(SITE_FOLDER."/nyu-install") || 
        (!file_exists(SITE_FOLDER."/nyu/admindata/nyuadmin.sqlite")) && !file_exists(SITE_FOLDER."/nyu/admindata/customdatabase.nyudata")) 
        && $GET[0] != '_nyuadmin'){
    $fm = new NyuFileManager(SITE_FOLDER);
    $fm->deleteFile('nyu-install');
    header("location: _nyuadmin/install/");
    exit;
}

// Alias para a classe handler da administração do nyu
use \nyuadmin\NyuAdminBase as nyuadm;

/* Nova administração do sistema */
if(isset($GET[0]) && $GET[0] == '_nyuadmin' && _NYU_ADMIN_){
    // Carrega as configurações de mvc da administração do sistema
    NyuConfig::setConfig('mvc', array("index_folder" => 'nyu/adminmvc',
                                      "default_folder" => '',
                                      "controllers_folder" => 'controllers',
                                      "models_folder" => 'models',
                                      "views_folder" => 'views',
                                      "cache_folder" => 'cache',
                                      "controllers_path" => '/nyu/adminmvc/controllers/',
                                      "models_path" => '/nyu/adminmvc/models/',
                                      "views_path" => '/nyu/adminmvc/views/',
                                      "cache_path" => '/nyu/adminmvc/cache/'));
    // Se é um plugin, ignora mensagem de erro 404
    if($GET[1] == 'plugin'){
        NyuConfig::addConfig('misc', 'ignore_not_found_page', true);
    }
    // Carrega a pasta de classes genéricas da administração do Nyu
    $fm = new NyuFileManager(SITE_FOLDER);
    $fm->autoloadFolder("nyu/adminmvc/miscclasses");
    $fm->autoloadFolder("nyu/adminmvc/plugins");
    
    // Cria a configuração de banco de dados para a administração do sistema
    $fm = new NyuFileManager(nyuadm::$admindataPath);
    // Se há uma configuração customizada (mysql)
    if($fm->fileExists(nyuadm::$customDbDataFileName)){
        $customadmindatabase = $fm->loadJsonFile(nyuadm::$customDbDataFileName);
        NyuConfig::addConfig("database", "_nyu_admin_", $customadmindatabase);
    // Se é a configuração padrão (sqlite)
    }else{
        $databaseFileName = nyuadm::$admindataPath.nyuadm::$databaseName;
        NyuConfig::addConfig("database", 
                             "_nyu_admin_", 
                             array("driver" => "sqlite",
                                   "path" => $databaseFileName));
    }
    // Carrega a configuração de banco de dados da administração do sistema
    NyuCore::setDatabaseConfig('_nyu_admin_');
    // Remove 'nyu:admin' do array $GET, para continuar o processamento
    unset($GET[0]);
    $GET = implode('|', $GET);
    $GET = explode('|', $GET);
    /* Carrega a controller da administração */
    // Encontra o namespace para carregar
    $namespace = NyuMVCLoader::findControllerNamespace(implode('/', $GET));
    // Monta novamente o get para encontrar o nome da controller
    $namespace_get = implode('/', $GET);
    // Remove o namespace do GET
    $namespace_controller = trim(str_replace($namespace, '', $namespace_get), '/');
    // Monta novamente o get
    $GET = explode('/', $namespace_controller);
    // Carrega o objeto controller
    $controller = new NyuMVCManager($GET[0], $namespace);
    // Chama a action
    $controller->controllerAction(@$GET[1]);
    // Encerra a execução
    exit;
}

/* Páginas de erro do sistema */
if(isset($GET[0]) && $GET[0] == "nyu"){
    if(!$GET[1]){
        NyuErrorManager::callErrorPage(403);
        exit;
    }elseif(file_exists(SITE_FOLDER."/nyu/nyuadmin/".$GET[1].".php") && !$GET[2] && _NYU_ADMIN_){
        require_once(SITE_FOLDER."/nyu/nyuadmin/".$GET[1].".php");
        exit;
    }elseif(file_exists(SITE_FOLDER."/nyu/nyuadmin/".$GET[1].".".$GET[2].".php") && _NYU_ADMIN_){
        require_once(SITE_FOLDER."/nyu/nyuadmin/".$GET[1].".".$GET[2].".php");
        exit;
    }elseif(!$GET[1] && _NYU_ADMIN_){
        require_once(SITE_FOLDER."/nyu/nyuadmin/admin.php");
        exit;
    }else{
        if($GET[1] == "401"){
            NyuErrorManager::callErrorPage(401);
            exit;
        }elseif($GET[1] == "403"){
            NyuErrorManager::callErrorPage(403);
            exit;
        }elseif($GET[1] == "404"){
            NyuErrorManager::callErrorPage(404);
            exit;
        }elseif($GET[1] == "500"){
            NyuErrorManager::callErrorPage(500);
            exit;
        }else{
            NyuErrorManager::callErrorPage();
            exit;
        }
    }
}

/* Hack: beforeLoadController */
if(isset($nyu__hack)){
    $nyu__hack->beforeLoadController();
}

/* Páginas do site */
if(!isset($GET[0]) && file_exists(SITE_FOLDER."/pages/index.php")){ // Home como página
    require_once(SITE_FOLDER."/pages/index.php");
    exit;
}else{
    if(isset($GET[0]) && file_exists(SITE_FOLDER."/pages/".$GET[0].".php")){
        require_once(SITE_FOLDER."/pages/".$GET[0].".php");
    }else{
        // Encontra o namespace para carregar
        $namespace = @NyuMVCLoader::findControllerNamespace(implode('/', $GET));
        // Monta novamente o get para encontrar o nome da controller
        $namespace_get = @implode('/', $GET);
        // Remove o namespace do GET
        $namespace_controller = trim(str_replace($namespace, '', $namespace_get), '/');
        // Monta novamente o get
        $GET = explode('/', $namespace_controller);
        // Carrega o objeto controller
        $controller = new NyuMVCManager($GET[0], $namespace);
        // Chama a action
        @$controller->controllerAction($GET[1]);
        // Encerra a execução
        exit;
    }
}