<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Core;
/**
 * Classe para carregar as configurações do nyu
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.0.1
 * @since 5.0
 */
class Config extends \Nyu\Core\CI{
    
    /**
     * Retorna uma configuração salva no arquivo config.php
     * @param string $type O tipo da configuração. Pode ser uma das constantes
     * definidas pelo sistema:
     * NYU_CONFIG_ROUTES - valor: routes
     * NYU_CONFIG_HELPERS - valor: helpers
     * NYU_CONFIG_DATABASE - valor: database
     * NYU_CONFIG_EXCEP - valor: excep
     * Ou outro valor em caso de uma configuração customizada, como por exemplo:
     * 
     * // Configuração customizada:
     * $nyu__config['novaconfiguracao'] = array('foo' => 'bar');
     * 
     * // Carregando a configuração:
     * $novaconfig = NyuConfig::getConfig("novaconfiguracao");
     * // $novaconfig terá o seguinte valor: array('foo' => 'bar')
     * 
     * $novaconfig = NyuConfig::getConfig("novaconfiguracao", "foo");
     * // $novaconfig terá o seguinte valor: 'bar'
     * 
     * @param string (opcional) $name nome ou chave da configuração a ser retornada
     * @return mixed
     * @global $nyu__config
     */
    public static function getConfig($type, $name = null){
        global $nyu__config, $nyu__helpers, $nyu__excep;
        if($type == "routes"){
            if($name){
                return $nyu__config['routes'][$name];
            }else{
                return $nyu__config['routes'];
            }
        }elseif($type == "helpers"){
            if($name){
                return $nyu__helpers[$name];
            }else{
                return $nyu__helpers;
            }
        }elseif($type == "database"){
            if($name){
                return $nyu__config['database'][$name];
            }else{
                return $nyu__config['database'];
            }
        }elseif($type == "excep"){
            if($name){
                return $nyu__excep[$name];
            }else{
                return $nyu__excep;
            }
        }else{
            if($name){
                return $nyu__config[$type][$name];
            }else{
                return $nyu__config[$type];
            }
        }
    }
    
    /**
     * Grava uma configuração na sessão atual
     * Este método altera a variável $nyu__config, adicionando um novo índice
     * ou alterando um existente
     * @param string $name Nome do índice da configuração a alterar/incluir
     * @param mixed $value Novo valor da configuração
     * @global $nyu__config
     */
    public static function setConfig($name, $value){
        global $nyu__config;
        $nyu__config[$name] = $value;
    }
    
    /**
     * Adiciona uma configuração na sessão atual
     * Este método altera a variável $nyu__config, adicionando um novo índice à
     * configuração informada
     * Exemplo:
     * NyuConfig::addConfig('database', 
     *                      'mydatabase', 
     *                      array('driver' => 'sqlite',
     *                            'path' => '/foo/bar/mydatabase.sqlite'));
     * @param string $name Nome do índice da configuração a adicionar
     * @param string $subname Nome do índice dentro da configuração a adicionar
     * @param mixed $value Valor da configuração
     * @global $nyu__config
     */
    public static function addConfig($name, $subname, $value){
        global $nyu__config;
        $nyu__config[$name][$subname] = $value;
    }
}