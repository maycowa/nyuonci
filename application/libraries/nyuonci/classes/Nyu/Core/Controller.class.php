<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Core;
/**
 * Classe que trata as Controllers do Nyu MVC
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuMVC
 * @version 1.3
 */
class Controller extends \CI_Controller{
    
    /**
     * Nome da classe que serão buscados os métodos de Controller
     * @var string
     */
    protected $class;
    
    /**
     * Array com os parâmetros enviados na variável $GET
     * @var array 
     */
    public $get;
    
    /**
     * Array com os parâmetros enviados na variável $_POST
     * @var array 
     */
    public $post;
    
    /**
     * Array com os parâmetros enviados na variável $_GET
     * @var array 
     */
    public $queryStrGet;
    
    /**
     * Array com os parâmetros enviados na variável $_FILES
     * @var array 
     */
    public $files;
    
    /**
     * Construtor da classe NyuController
     * @uses $GET Parâmetros enviados via GET através da URL amigável
     */
    public function __construct() {
        global $GET;
        $this->get = $GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->queryStrGet = $_GET;
    }
    
    /**
     * Ação padrão de uma tela
     */
    public function indexAction(){}
    
    /**
     * Retorna o nome da classe atual, sem a string "Controller"
     * @return string
     */
    public function getClass(){
        return substr(get_called_class(), 0, strlen(get_called_class())-10);
    }
    
    /**
     * Redireciona para um outro endereço
     * @param string $url endereço a redirecionar
     * @param int $code Código de redirecionamento HTTP - Padrão 302
     */
    public function redirect($url, $code = null){
        if($code){
            header("Location: ".$url, true, $code);
        }else{
            header("Location: ".$url);
        }
        exit;
    }
    
    /**
     * Imprime o conteúdo enviado no parâmetro $ret em formato json e encerra o processamento
     * @param mixed $ret conteúdo a ser impresso em formato json
     */
    public function json($ret){
        $callback = @$this->get['callback'];
        if(!$callback){
            $callback = @$this->post['callback'];
        }
        
        if ($callback){
            echo $callback.'('.json_encode($ret).')';
        }else{
            echo json_encode($ret);
        }
        exit;
    }

}