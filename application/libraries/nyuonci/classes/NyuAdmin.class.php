<?php
/**
 * 2014 Nyu Framework
 */
/**
 * Métodos antigos da administração do Nyu
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuAdmin
 * @version 2.0
 * @deprecated
 */
class NyuAdmin{
    /**
     * Cria/altera o arquivo config.ini com as configurações do sistema
     * @param array $config array com as configurações a gravar
     */
    public static function saveConfig($config){
        $config['site_name_sys'] = strtolower(str_replace(" ", "_", preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities($config['site_name']))));
        $configcontent = '; Configurações do Nyu
[about]
site_name = "'.$config['site_name'].'"
site_name_sys = "'.$config['site_name_sys'].'"

[database]
host = "'.$config['host'].'"
name = "'.$config['name'].'"
user = "'.$config['user'].'"
password = "'.$config['password'].'"

[misc]
ignore_not_found_page = '.(int)$config['ignore_not_found_page'].'
nyu_admin = '.(int)$config['nyu_admin'].'
cache_views = '.(int)$config['cache_views'].'
401_error_file = '.$config['401_error_file'].'
403_error_file = '.$config['403_error_file'].'
404_error_file = '.$config['404_error_file'].'
500_error_file = '.$config['500_error_file'].'
default_error_file = '.$config['default_error_file'];
        $fm = new NyuFileManager(SITE_FOLDER."/sitedata");
        $fm->saveFile("config.ini", $configcontent);
    }
    
    /**
     * Configura o .htaccess e o config.php para a primeira instalação do Nyu e 
     * apaga o arquivo preinstaller.php para evitar modificações indevidas
     * @param string $path Pasta onde foi instalado o Nyu (dentro do host)
     */
    public static function preInstaller($path){
        if($path != "/"){
            $fm = new NyuFileManager(SITE_FOLDER."/sitedata", false);
            $data = $fm->loadFile("config.php");
            $exptmp = explode("/", $path);
            $exptmp = array_filter($exptmp);
            $exceptions = '"'.implode('","', $exptmp).'"';
            $data = str_replace('$nyu__excep = array("");', '$nyu__excep = array('.$exceptions.');', $data);
            $fm->saveFile("config.php", $data);
            
            $fm = new NyuFileManager(SITE_FOLDER, false);
            $data = $fm->loadFile(".htaccess");
            
            //$data = str_replace('RewriteBase /', 'RewriteBase '.$path, $data);
            $data = str_replace('ErrorDocument 401 /nyu/401', 'ErrorDocument 401 '.$path.'nyu/401', $data);
            $data = str_replace('ErrorDocument 403 /nyu/403', 'ErrorDocument 403 '.$path.'nyu/403', $data);
            $data = str_replace('ErrorDocument 404 /nyu/404', 'ErrorDocument 404 '.$path.'nyu/404', $data);
            $data = str_replace('ErrorDocument 500 /nyu/500', 'ErrorDocument 500 '.$path.'nyu/500', $data);
            
            $fm->saveFile(".htaccess", $data);
        }
        
        $fm = new NyuFileManager(SITE_FOLDER, false);
        $fm->moveFile("preinstaller.php", SITE_FOLDER . "/sitedata");
        $fm = new NyuFileManager(SITE_FOLDER."/sitedata");
        $fm->renameFile("preinstaller.php", "preinstaller");
    }
    
    /**
     * Indica se há ou não um usuário logado. Se existir, retorna o usuário logado
     * @return NyuUserModel / boolean
     */
    public static function userLogged(){
        $usr = NyuCore::loadFromSess("nyu_user_logged", false);
        if($usr){
            return $usr;
        }
        return false;
    }
    
    /**
     * Remove o usuário atual da sessão
     */
    public static function userLogoff(){
        NyuCore::deleteFromSess("nyu_user_logged");
    }
}
