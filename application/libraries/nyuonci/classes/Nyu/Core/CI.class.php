<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Core;

/**
 * Classe Base da integração do Nyu com o CodeIgniter para as classes que não 
 * possuem integração nativa (Ex: Nyu\Core\Controller e Nyu\Core\Model)
 */
class CI{
    
    /**
     * Objeto global do CodeIgniter
     * @var CI_Base
     */
    public $CI;
    
    /**
     * Construtor da classe base da integração do Nyu com o CodeIgniter
     * @global CI_Base $CI
     */
    public function __construct() {
        global $CI;
        $this->CI = $CI;
    }
}