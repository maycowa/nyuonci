<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe que trata dos templates do sistema utilizando a template engine Twig
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.8
 */
class NyuTemplate{
    /**
     * Loader das classes do Twig
     * @var Twig_Loader_Filesystem
     */
    protected $loader;
    /**
     * Objeto do Twig
     * @var Twig_Environment 
     */
    protected $twig;
    /**
     * Template carregado
     * @var object
     */
    protected $template;
    /**
     * Variáveis que serão carregadas automaticamente no template, sem
     * necessidade de incluir na chamada do método render
     * @var array
     */
    protected $vars;
    
    /**
     * Construtor da classe NyuTemplate
     * @param array $options Opções do Twig_Environment
     * @uses $MVCModulePath String com o nome da pasta do módulo, dentro de nyumvc/modules
     */
    public function __construct($options=null) {
        global $MVCModulePath;
        
        if(is_null($options['cache']) && (_CACHE_VIEWS_ == 1 || $options['force_cache'] == 1)){ // Seta a pasta de cache de templates
            $options['cache'] = SITE_FOLDER.NyuConfig::getConfig('mvc', 'cache_path');
        }else{
            $options['cache'] = false;
        }
        
        // Trata o diretório dos templates
        if(!isset($options['template_dir'])){
            /*if($MVCModulePath){
                $options['template_dir'] = SITE_FOLDER.'/mvc/modules/'.$MVCModulePath.'/view';
            }else{*/
                $options['template_dir'] = SITE_FOLDER.NyuConfig::getConfig('mvc', 'views_path');
            /*}*/
        }
        $template_dir = $options['template_dir'];
        unset($options['template_dir']);

        //Carrega a engine do Twig
        if(file_exists(SITE_FOLDER.'/'.NyuConfig::getConfig("lib_folder").'/Twig/Autoloader.php')){
            require_once(SITE_FOLDER.'/'.NyuConfig::getConfig("lib_folder").'/Twig/Autoloader.php');
        }else{
            throw new Exception("Twig não está incluído nesta instalação do ". _SYS_NAME_);
        }
        
        Twig_Autoloader::register();
        $this->loader = new Twig_Loader_Filesystem($template_dir);
        $this->twig = new Twig_Environment($this->loader, $options);
        
        /* Adiciona constantes do sistema que podem ser utilizadas no template */
        $this->addVar("_SYS_NAME_", _SYS_NAME_);
        $this->addVar("_SYS_VERSION_", _SYS_VERSION_);
        $this->addVar("_SYS_VERSION_CODE_", _SYS_VERSION_CODE_);
        $this->addVar("SITE_NAME", SITE_NAME);
        $this->addVar("SITE_NAME_SYS", SITE_NAME_SYS);
        $this->addVar("SITE_URL", SITE_URL);
        $this->addVar("CURRENT_URL", CURRENT_URL);
        $this->addVar("NYU_LOGO", NYU_LOGO);
        $this->addVar("NYU_ADMIN_LOGO", NYU_ADMIN_LOGO);
        $this->addVar("NYU_ADMIN_URL", NYU_ADMIN_URL);
        
        /* Adiciona classes globais para executar métodos úteis */
        $this->twig->addGlobal('NYUCORE', new NyuCore());
        $this->twig->addGlobal('NYUDATETIME', new NyuDateTime());
    }
    
    /**
     * Carrega um template
     * @param string $file Arquivo do template
     */
    public function loadTemplate($file){
        $this->template = $this->twig->loadTemplate($file);
    }
    
    /**
     * Renderiza o template na tela
     * @param array $vars Variáveis a carregar no template
     * @param boolean $return Se true, retorna o valor, não imprime na tela
     */
    public function render($vars=array(), $return=false){
        if($this->vars){
            $vars = array_merge($this->vars, $vars); // Mescla as variáveis informadas com as variáveis já existentes
        }
        if($return){
            return $this->template->render($vars);
        }else{
        echo $this->template->render($vars);
        }
    }
    
    /**
     * Carrega o template e renderiza na tela
     * @param array $vars Variáveis a carregar no template
     */
    public function renderTemplate($file, $vars=array()){
        $this->loadTemplate($file);
        $this->render($vars);
    }
    
    /**
     * Apaga os arquivos de cache do Twig
     */
    public function clearCache(){
        $this->twig->clearCacheFiles();
    }
    
    /**
     * Apaga o arquivo de cache do template informado
     * @param string $file template a buscar
     */
    public function clearTemplateCache($file){
        $fileCache = $this->twig->getCacheFilename($file);

        if (is_file($fileCache)) {
            @unlink($fileCache);
        }
    }
    
    /**
     * Adiciona uma função para ser utilizada no template
     * @param string $name nome da função
     * @param function $code código da função
     */
    public function addFunction($name, $code){
        $function = new Twig_SimpleFunction($name, $code);
        $this->twig->addFunction($function);
    }
    
    /**
     * Adiciona uma variável global no template
     * @param string $name nome variável no template
     * @param function $var variável
     */
    public function addGlobal($name, $var){
        $this->twig->addGlobal($name, $var);
    }
    
    /**
     * Seta o valor de uma variável que será incluída no template
     * @param string $varname Nome da variável
     * @param string $value Valor da variável
     */
    public function addVar($varname, $value){
        $this->vars[$varname] = $value;
    }
    
    /**
     * Retorna o valor de uma variável do template
     * @param string $varname Nome da variável
     * @return string
     */
    public function getVar($varname){
        return $this->vars[$varname];
    }
    
    /**
     * Retorna todas as variáveis do template
     * @return array
     */
    public function getVars(){
        return $this->vars;
    }
    
    /**
     * Retorna o objeto Twig, para ser modificado manualmente, se necessário
     * @return object
     */
    public function getTwigObject(){
        return $this->twig;
    }
    
    /**
     * Altera o caminho dos arquivos de template a carregar.
     * Exemplos de uso:
     * <pre><code>
     * $this->setPath('mvc/view/foo/bar/');
     * $this->setPath('mvc/foo/bar/view/');
     * </code></pre>
     * @param string|array $path string|array, com o(s) caminhos de pasta a carregar
     * no objeto Twig_Loader_Filesystem armazenado no atributo $this->loader
     * @since 1.8
     */
    public function setPath($path){
        $this->loader->setPaths($path);
    }
}