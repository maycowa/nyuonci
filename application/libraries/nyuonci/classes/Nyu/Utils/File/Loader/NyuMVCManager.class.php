<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe que trata as Controllers do Nyu quando o modo MVC está ativado
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuMVC
 * @version 1.2.1
 */
class NyuMVCManager{
    
    /**
     * Namespace a carregar a classe
     * @var string
     */
    protected $namespace;
    /**
     * Nome da classe que serão buscados os métodos de Controller
     * @var string
     */
    protected $class;
    
    /**
     * O objeto controller da classe a carregar
     * @var object
     */
    protected $controller;
    
    /**
     * O objeto model da classe controller carregada
     * @var object
     */
    protected $model;
    
    /**
     * Ação da Controller "Index" - será incluído valor neste atributo caso o
     * sistema não encontrar uma controller com o nome informado, porém, existir
     * uma action da controller "Index" com o mesmo nome
     * @var string
     */
    protected $indexAction;
    
    /**
     * Indica que irá ignorar a action, pois trata-se da home (com um parâmetro) 
     * sendo enviado
     * @var boolean 
     */
    protected $ignoreAction;
    
    /**
     * Construtor da classe NyuMVCManager
     * @param string $controller Nome da classe controller a buscar. O sistema 
     * adiciona automaticamente o texto "Controller" ao nome da classe e busca
     * a classe na pasta SITE_FOLDER/mvc/controller. Ex:
     * $controller = new NyuController('Usuario');
     * @param string namespace (optional) Namespace a buscar a controller
     */
    public function __construct($controller = null, $namespace = false) {
        $this->ignoreAction = false;
        $this->namespace = $namespace;
        $this->setController($controller);
    }
    
    /**
     * Seta o objeto controller que está sendo carregado
     * @param string $controller Nome da classe controller a buscar. O sistema 
     * adiciona automaticamente o texto "Controller" ao nome da classe e busca
     * a classe na pasta SITE_FOLDER/mvc/controller. Ex:
     * $controller->setController('Usuario');
     */
    public function setController($controller = null){
        if(!$controller){
            $controller = "Index";
        }
        $this->class = ucwords(NyuMVCManager::convertAction($controller));
        $this->controller = NyuMVCLoader::getController($this->class, $this->namespace);

        if(!$this->controller){
            $controller_name = "IndexController";
            // Se possui namespace, altera o nome da controller
            if($this->namespace){
                $controller_name = str_replace('/', '\\', $this->namespace) . '\\' . $controller_name;
            }
            if(method_exists(new $controller_name(), self::convertAction($controller) . "Action")){
                $this->class = "Index";
                $this->controller = NyuMVCLoader::getController($this->class, $this->namespace);
                $this->indexAction = $controller;
                $this->ignoreAction = true;
            }else{
                if(!\NyuConfig::getConfig('misc', 'ignore_not_found_page')){ // Alterado para buscar da configuração, não da constante, pois assim pode ser alterada a configuração em tempo de execução, se necessário
                    NyuErrorManager::callErrorPage(404);
                    exit;
                }else{
                    $this->class = "Index";
                    $this->controller = NyuMVCLoader::getController($this->class, $this->namespace);
                    $this->indexAction = "index";
                }
            }
        }
    }
    
    /**
     * Seta o nome do método da Controller Index que será chamado.
     * Este método irá forçar a chamada do método indicado na IndexController,
     * caso não seja informada nenhuma action
     * @param string $indexAction
     */
    public function setIndexAction($indexAction = null){
        $this->indexAction = $indexAction;
    }
    
    /**
     * Retorna o método action da classe Index, caso seja encontrado
     * @return string
     */
    public function getIndexAction(){
        return $this->indexAction;
    }
    
    /**
     * Retorna o objeto controller que foi carregado
     * @return object
     */
    public function getController(){
        return $this->controller;
    }
    
    /**
     * Executa uma ação da controller carregada
     * @param string $action
     * @uses $MVCModulePath String com o nome da pasta do módulo, dentro de nyumvc/modules
     */
    public function controllerAction($action = null){
        global $MVCModulePath;
        
        if($action && !$this->ignoreAction){
            $action = self::convertAction($action) . "Action";
        }else{
            if($this->indexAction){ // Se há uma ação da index carregada no objeto
                $action = self::convertAction($this->indexAction) . "Action";
                $this->indexAction = null; // Apaga da memória a controller, para evitar problemas
            }else{
                $action = "indexAction";
            }
        }
        if(method_exists($this->controller, $action)){
            $this->controller->$action();
        }else{
            if($MVCModulePath){ // Se estiver dentro de um módulo, carrega a action padrão, com o parâmetro enviado como $get
                $action = "indexAction";
                $this->controller->$action();
            }else{
                if(!\NyuConfig::getConfig('misc', 'ignore_not_found_page')){ // Alterado para buscar da configuração, não da constante, pois assim pode ser alterada a configuração em tempo de execução, se necessário
                    NyuErrorManager::callErrorPage(404);
                    exit;
                }else{
                    $action = "indexAction";
                    $this->controller->$action();
                }
            }
        }
        
    }

    /**
     * Retorna um objeto model da classe controller carregada
     * @return object
     */
    public function getCurrentModel(){
        if(is_object($this->model)){
            $model = $this->model;
        }else{
            $model = NyuMVCLoader::getModel($this->class);
            if(!$model){
                return false;
            }            
        }
        return $model;
    }

    /**
     * Formata a string vinda da URL para buscar a action
     * Converte traços + letra minúscula em letra maiúscula
     * Se o primeiro caractere da action é numérico, inclui o
     * caractere underline antes do nome, para fazer a busca na
     * classe controller
     * Exemplo: cadastro-usuario -> cadastroUsuario
     * @param string $action
     * @return string
     */
    public static function convertAction($action = null){
        $act = preg_replace_callback("(-.{1})", create_function('$a','return str_replace("-","",strtoupper($a[0]));'), $action);
        if(is_numeric(substr($act, 0, 1))){
            $act = "_".$act;
        }
        return $act;
    }
}