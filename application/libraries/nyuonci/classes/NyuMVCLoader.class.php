<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe que trata do carregamento das classes de Model e Controller e da View no MVC do Nyu
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuMVC
 * @version 1.3
 */
class NyuMVCLoader{
    
    /**
     * Retorna um objeto model a partir do nome de sua classe
     * @param string $class Nome da classe - sem o texto "Model"
     * @return object|boolean
     */
    public static function getModel($class){
        $class = ucwords(NyuMVCManager::convertAction($class)) . "Model";
        if(class_exists($class)){
            return new $class();
        }else{
            return false;
        }
    }
    
    /**
     * Retorna o nome da classe model do objeto
     * @param string $class Nome da classe - sem o texto "Model"
     * @return string|boolean
     * @since 4.1
     */
    public static function getModelName($class){
        $class = ucwords(NyuMVCManager::convertAction($class)) . "Model";
        if(class_exists($class)){
            return $class;
        }else{
            return false;
        }
    }
    
    /**
     * Retorna um objeto controller a partir do nome de sua classe
     * @param string $class Nome da classe - sem o texto "Controller"
     * @param boolean $namespace (optional) Se informado, irá buscar a classe
     * no namespace informado
     * @return object|boolean
     */
    public static function getController($class, $namespace = false){
        if($namespace){
            $namespace = str_replace('/', '\\', $namespace).'\\';
        }
        $class = $namespace.ucwords(NyuMVCManager::convertAction($class)) . "Controller";
        if(class_exists($class)){
            return new $class();
        }else{
            return false;
        }
    }
    
    /**
     * Retorna o nome da classe controller do objeto
     * @param string $class Nome da classe - sem o texto "Model"
     * @return string|boolean
     */
    public static function getControllerName($class){
        $class = ucwords(NyuMVCManager::convertAction($class)) . "Controller";
        if(class_exists($class)){
            return $class;
        }else{
            return false;
        }
    }
    
    /**
     * Renderiza uma view na tela
     * @param string $view Nome da view a carregar
     * @param array $variables Variáveis a incluir no template
     * @param array $options Opções para customizar o Twig
     */
    public static function renderView($view, $variables = array(), $options = null){
        $tpldir = SITE_FOLDER.NyuConfig::getConfig('mvc', 'views_path');
        $options = $options ? $options : array("cache" => false, "template_dir" => $tpldir);
        $options['template_dir'] = $options['template_dir'] ? $options['template_dir'] : $tpldir;
        $tp = new NyuTemplate($options);
        $tp->renderTemplate("{$view}", $variables);
    }
    
    /**
     * Encontra a pasta do namespace da controller a partir da URL
     * @param string $namespace
     * @return boolean|string
     */
    public static function findControllerNamespace($namespace){
        $arr_namespace = explode("/",$namespace);
        if(count($arr_namespace) > 0){
            for($i = count($arr_namespace); $i > 0; $i--){
                $tmp_namespace = implode('/', $arr_namespace);
                if(file_exists(self::getControllersPath().$tmp_namespace)){
                    return $tmp_namespace;
                }else{
                    array_pop($arr_namespace);
                }
            }
            return false;
        }else{
            return false;
        }
    }
    
    /**
     * Altera o valor padrão da variável $MVCModulePath, pasta do módulo a carregar as models e views
     * @global string $MVCModulePath
     * @param string $module O novo valor da variável $MVCModulePath
     * @return string A Variável $MVCModulePath alterada
     */
    public static function setModule($module){
        global $MVCModulePath;
        $MVCModulePath = $module;
        return $MVCModulePath;
    }
    
    /**
     * Retorna o módulo ativado
     * @global string $MVCModulePath
     * @return string A Variável $MVCModulePath
     */
    public static function getModule(){
        global $MVCModulePath;
        return $MVCModulePath;
    }
    
    /**
     * Retorna o caminho completo para a pasta de controllers
     * @return string
     */
    public static function getControllersPath(){
        return SITE_FOLDER.NyuConfig::getConfig('mvc','controllers_path');
    }
    
    /**
     * Retorna o caminho completo para a pasta de models
     * @return string
     */
    public static function getModelsPath(){
        return SITE_FOLDER.NyuConfig::getConfig('mvc','models_path');
    }
    
    /**
     * Retorna o caminho completo para a pasta de views
     * @return string
     */
    public static function getViewsPath(){
        return SITE_FOLDER.NyuConfig::getConfig('mvc','views_path');
    }
}
