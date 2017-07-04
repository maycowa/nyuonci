<?php
/**
 * 2016 Nyu Framework
 */

/**
 * Métodos do core do Nyu
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 2.0
 */
class NyuCore {

    /**
     * Salva uma variável na sessão do sistema
     * @param string $name Nome da variável
     * @param mixed $var Valor da variável
     */
    public static function saveInSess($name, $var) {
        $_SESSION[SITE_NAME_SYS][$name] = serialize($var);
    }

    /**
     * Retorna uma variável da sessão do sistema
     * @param string $name Nome da variável
     * @param boolean $delete Padrão true, indica se irá ou não apagar a 
     * variável da sessão
     * @return mixed
     */
    public static function loadFromSess($name, $delete = true) {
        $sess = unserialize($_SESSION[SITE_NAME_SYS][$name]);
        if ($delete) {
            unset($_SESSION[SITE_NAME_SYS][$name]);
        }
        return $sess;
    }

    /**
     * Remove uma variável da sessão do sistema
     * @param string $name Nome da variável
     */
    public static function deleteFromSess($name) {
        unset($_SESSION[SITE_NAME_SYS][$name]);
    }

    /**
     * Retorna a Excessão, se existir
     * @param boolean $delete Padrão true, indica se irá ou não apagar a 
     * variável da sessão
     * @param boolean $delete Padrão true, se false, não irá excluir da sessão 
     * a informação
     * @param boolean $returnObject Padrão false, se true, irá retornar o 
     * objeto Exception
     * @return mixed
     * @since 4.1
     */
    public static function getException($delete = true, $returnObject = false) {
        $value = self::loadFromSess("exception", $delete);
        if(is_object($value) && !$returnObject){
            $value = $value->getMessage();
        }
        return $value;
    }
    
    /**
     * Salva a Excessão
     * @param string $value Valor a gravar
     * @return mixed
     * @since 5.0
     */
    public static function setException($value) {
        
        return self::saveInSess("exception", $value);
    }
    
    /**
     * Seta o valor da variável $nyu__database_config, responsável por carregar
     * a configuração do banco de dados para instâncias diferentes da padrão
     * do sistema. Após alterar a conexão atual, reinicia o objeto NyuDb 
     * instanciado em memória. Transações não commitadas serão perdidas nesta
     * operação
     * @param string $databaseConfig Nome da configuração
     * Para retornar à configuração padrão, inserir o valor "default" sem as 
     * aspas, ou o valor nulo
     * @since 5.0
     */
    public static function setDatabaseConfig($databaseConfig){
        global $nyu__database_config;
        if(!$databaseConfig){
            $databaseConfig = "default";
        }
        $nyu__database_config = $databaseConfig;
        NyuDb::resetInstance(); // Reinicia o Singleton do objeto de bd
    }
    
    /**
     * Retorna a configuração do banco de dados atual
     * @global string $nyu__database_config
     * @return string
     */
    public static function getDatabaseConfig(){
        global $nyu__database_config;
        return $nyu__database_config;
    }
    
    /**
     * Atalho para NyuMVCLoader::setModule()
     * @param string $module O novo valor da variável $MVCModulePath
     * @return string A Variável $MVCModulePath alterada
     * @since 5.0
     */
    public static function setModule($module){
         return NyuMVCLoader::setModule($module);
    }
    
    /**
     * Atalho para NyuMVCLoader::getModule()
     * @return string A Variável $MVCModulePath
     * @since 5.0
     */
    public static function getModule(){
         return NyuMVCLoader::getModule();
    }
    
    /**
     * Carrega as classes model, misc e plugins da administração para serem 
     * utilizadas normalmente
     */
    public static function loadAdmin(){
        $fm = new NyuFileManager(SITE_FOLDER);
        $fm->autoloadFolder("nyu/adminmvc/models");
        $fm->autoloadFolder("nyu/adminmvc/miscclasses");
        $fm->autoloadFolder("nyu/adminmvc/plugins");
    }
    
    /**
     * Função de debug de variáveis
     * @param mixed $value Valor a debugar
     * @param string $name Identificador do valor
     * @param boolean $exit Se true, cancela a execução do script (funciona 
     * apenas se o parâmetro $return é falso
     * @param boolean $return Se true, não exibe na tela o valor, e retorna
     * o conteúdo do debug na função
     * @return string Se o parâmetro $return é true, retorna o valor do debug
     */
    public static function debug($value, $name = null, $exit = false, $return = false){
        ob_start();
        echo "<pre>";
        echo "Variável: {$name}<br>";
        print_r($value);
        echo "</pre>";
        $ret = ob_get_contents();
        ob_end_clean();
        
        if($return){
            return $ret;
        }
        echo $ret;
        if($exit){
            die;
        }
    }

    /**
     * Método que retorna um objeto ckeditor preparado para substituir elementos no sistema
     * @param String $toolbar Indica qual modelo de toolbar os editores preparados utilizarão.
     * Valores permitidos: Full; Basic; outros padrões personalizados;
     * @uses CKEditor Editor de texto localizado em SITE_FOLDER/js/ckeditor/
     */
    public static function prepareEditor($toolbar = null) {
        if (!class_exists("CKEditor")) {
            if (file_exists(SITE_FOLDER . "/" . NyuConfig::getConfig("lib_folder"). "/ckeditor/ckeditor.php")) {
                include_once(SITE_FOLDER . "/" . NyuConfig::getConfig("lib_folder"). "/ckeditor/ckeditor.php");
            } else {
                throw new Exception("CKEditor não está incluído nesta instalação do " . _SYS_NAME_);
            }
        }
        $CKEditor = new CKEditor(SITE_URL . "/" . NyuConfig::getConfig("lib_folder"). '/ckeditor/');
        $CKEditor->config["extraPlugins"] = 'tableresize';
        if ($toolbar) {
            $CKEditor->config["toolbar"] = $toolbar;
        }
        //$CKEditor->config["toolbar"] = 'Full';
        //$CKEditor->config["toolbar"] = 'Basic';
        //$CKEditor->config["toolbar"] = "Chat";
        //$CKEditor->config["toolbar"] = "Notes";
        return $CKEditor;
    }

    /**
     * Método que retorna uma tag script para importar um arquivo javascript para a página
     * @param string $url URL do arquivo javascript a ser incluído
     * @return string conteúdo da tag script
     */
    public static function incJs($url) {
        $s = "<script type='text/javascript' src='{$url}'></script>";
        return $s;
    }

    /**
     * Método que retorna uma tag link para importar um arquivo css para a página
     * @param string $url URL do arquivo css a ser incluído
     * @return string conteúdo da tag link
     */
    public static function incCss($url) {
        $s = "<link type='text/css' href='{$url}' rel='stylesheet' />";
        return $s;
    }

    /**
     * Converte um array em form e envia por post para a url informada
     * @param array $array Array a enviar
     * @param string $url Url de destino
     * @version 1.0
     */
    public static function ArrayToPost($array, $url) {
        $s = "<form method='POST' id='arrayToPost' name='arrayToPost' action='{$url}'>";
        foreach ($array as $k => $var) {
            $s .= "<input type='hidden' name='{$k}' id='{$k}' value='{$var}'/>";
        }
        $s .= "<input type='submit' name='smt' value='' style='display:none' />";
        $s .= "</form>";
        $s .= "<script>document.getElementById('arrayToPost').submit()</script>";
        echo $s;
    }

    /**
     * Verifica se uma string contém uma quantidade mínima de caracteres
     * @param string $var Conteúdo a ser testado
     * @param int $minChars Quantidade mínima de caracteres
     * @return boolean
     */
    public static function minChars($var, $minChars) {
        if (strlen($var) < $minChars) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Verifica se uma string contém menos que uma quantidade de caracteres
     * @param string $var Conteúdo a ser testado
     * @param int $maxChars Quantidade máxima de caracteres
     * @return boolean
     */
    public static function maxChars($var, $maxChars) {
        if (strlen($var) > $maxChars) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Verifica se uma string contém uma quantidade de caracteres
     * @param string $var Conteúdo a ser testado
     * @param int $exactChars Quantidade de caracteres
     * @return boolean
     */
    public static function exactChars($var, $exactChars) {
        if (strlen($var) != $exactChars) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Verifica se a quantidade de caracteres de um conteúdo está válida 
     * conforme o filtro indicado
     * @param string $var Conteúdo a ser testado
     * @param int $qtdeChars Quantidade de caracteres
     * @param string $mode Fixo: MIN / MAX / EXACT indica qual operação está 
     * sendo verificada
     * @return boolean
     */
    public static function verifyChars($var, $qtdeChars, $mode) {
        if ($mode == "MIN") {
            return NyuCore::minChars($var, $qtdeChars);
        } elseif ($mode == "MAX") {
            return NyuCore::maxChars($var, $qtdeChars);
        } elseif ($mode == "EXACT") {
            return NyuCore::exactChars($var, $qtdeChars);
        }
    }

    /**
     * Exibe as configurações do sistema
     * @param boolean $onlydata Indica que o método deve retornar apenas os 
     * dados, sem estilo
     */
    public static function aboutNyu($onlydata = false) {
        if (!$onlydata) {
            echo "<style>
                body{
                    font-family: Arial;
                    background-color: #f7f8fd;
                    color: #ee5f5b;
                    padding: 0px;
                    margin: 0px;
                }
                h1.nyumessage{
                    font-size: 18px;
                }
                h2.nyumessage{
                    font-size: 14px;
                }
                a.nyulink{
                    color:#d14;
                }
                #nyuallpage{
                    text-align: center;
                    width: 1024px;
                    height: 100%;
                    margin:0 auto;
                }

                #nyuleftcol{
                    text-align: left;
                    width: 195px;
                    height: 100%;
                    display: block;
                    float: left;
                    border-right: 5px solid #00A6C7;
                }

                #nyurightcol{
                    text-align: left;
                    width: 1024px;
                    height: 100%;
                }

                #nyurightcontent{
                    padding-left: 210px;
                }

                #nyufooter{
                    position: absolute;
                    bottom: 0px;
                }

                .nyuhr{
                    background: #00A6C7;
                    height: 5px;
                    border: 0;
                }

                .h1_about{
                    font-size: 25px;
                    padding: 0px;
                }

                .h3_about{
                    font-size: 20px;
                    padding: 0px;
                }

                .table_about {
                    border-collapse: collapse;
                    width: 100%;
                }

                .table_about td, .table_about tr {
                    border: 1px solid #00A6C7;
                }

                .table_about_titulo{
                    font-weight: bold;
                }

                .nyulogo{
                    width: 459px;
                }

            </style>";
            echo "<div id=\"nyuallpage\">
                <div id=\"nyuleftcol\">
                    <div id=\"nyufooter\">Nyu Framework &copy; 2013</div>
                </div>
                <div id=\"nyurightcol\">
                <a href='" . SITE_URL . "/nyu/admin'><img src=\"" . NYU_ADMIN_LOGO . "\" class='nyulogo' alt=\"" . _SYS_NAME_ . "\" /></a>
                    <hr class=\"nyuhr\"/>
                    <div id=\"nyurightcontent\">";
        }
        echo "<h1 class='h1_about'>Sobre o Nyu</h1>";
        echo "<h3 class='h3_about'>Constantes do Sistema:</h3>";
        echo "<table class='table_about'>";
        echo "<tr><td class='table_about_titulo'>_SYS_NAME_</td><td>" . _SYS_NAME_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_SYS_VERSION_</td><td>" . _SYS_VERSION_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_SYS_VERSION_CODE_</td><td>" . _SYS_VERSION_CODE_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_LANG_</td><td>" . _LANG_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>SITE_NAME</td><td>" . SITE_NAME . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>SITE_NAME_SYS</td><td>" . SITE_NAME_SYS . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>SITE_URL</td><td>" . SITE_URL . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>SITE_FOLDER</td><td>" . SITE_FOLDER . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>SITE_PATH</td><td>" . SITE_PATH . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_DB_HOST_</td><td>" . _DB_HOST_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_DB_NAME_</td><td>" . _DB_NAME_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_DB_USR_</td><td>" . _DB_USR_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_DB_PSW_</td><td>" . _DB_PSW_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_DB_STR_</td><td>" . _DB_STR_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_DB_DRIVER_</td><td>" . _DB_DRIVER_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_IGNORE_NOT_FOUND_PAGE_</td><td>" . (int) _IGNORE_NOT_FOUND_PAGE_ . "</td></tr>";
        echo "<tr><td class='table_about_titulo'>_NYU_ADMIN_</td><td>" . (int) _NYU_ADMIN_ . "</td></tr>";
        echo "</table>";
        echo "<h3 class='h3_about'>Libs Instaladas:";
        echo "<table class='table_about'>";
        $fm = new NyuFileManager(SITE_FOLDER . "/" . NyuConfig::getConfig("lib_folder"));
        foreach ($fm->getFolders() as $f) {
            if ($f != "..") {
                echo "<tr><td>" . $f . "</td></tr>";
            }
        }
        echo "</table>";
        if (!$onlydata) {
            echo "</div>
                </div>
            </div>";
        }
    }

    /**
     * Exibe a tela de boas vindas do sistema
     */
    public static function welcomeNyu() {
        $vars['_template'] = 'welcomeNyu';
        $vars['_js'] = $js;
        $vars['_css'] = $css;
        $vars['_title'] = $title;
        $vars['_link_home_'] = SITE_URL;
        $base_template = "offline_content";
        $tpl = new NyuTemplate(array('template_dir' => SITE_FOLDER.'/nyu/adminmvc/views'));
        $tpl->renderTemplate("{$base_template}.twig", $vars);
    }

}