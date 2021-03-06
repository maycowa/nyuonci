<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Validate;
/**
 * Classe de validação de objetos
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.0.1
 * @since 5.0
 */
class Validate{
    /**
     * Regras a utilizar na validação
     * @var array
     */
    protected $rules;
    
    /**
     * Indica se o objeto está valido ou não
     * @var boolean
     */
    protected $valid;
    
    /**
     * Objeto a validar
     * @var object
     */
    protected $object;
    
    /**
     * Mensagem de erro, se existir
     * @var string
     */
    protected $message;
    
    /**
     * Construtor da classe NyuValidate
     * @param object $object (opcional) Seta o objeto a validar
     * @param array $rules (opcional) Seta as regras a serem utilizadas para a validação
     */
    public function __construct($object = false, $rules = false){
        $this->valid = false;
        if($object){
            $this->object = $object;
        }
        if($rules){
            $this->rules = $rules;
        }
        return $this;
    }
    
    /**
     * Seta o objeto a validar
     * @param object $object
     * @return \Nyu\Validate\Validate O objeto atual
     */
    public function setObject($object){
        $this->object = $object;
        return $this;
    }
    
    /**
     * Adiciona uma regra a ser utilizada para a validação
     * @param NyuValidateRule $rule Objeto da regra a utilizar
     * @return \Nyu\Validate\Validate O objeto atual
     */
    public function addRule($rule){
        $this->rules[] = $rule;
        return $this;
    }
    
    /**
     * Retorna se o objeto está válido ou não
     * @return boolean
     */
    public function isValid(){
        return $this->valid;
    }
    
    /**
     * Valida o objeto a partir das regras e retorna se está válido ou não
     * @return boolean
     */
    public function validate(){
        $this->valid = true; // Seta como válido para começar a validação
        foreach($this->rules as $rule){
            if($this->valid){
                $method = "get".ucfirst($rule->getAttribute());
                $a = $this->object->$method();
                $b = $rule->getCompareTo();
                $rule = str_replace('$a', "'".$a."'", $rule);
                $rule = str_replace('$b', "'".$b."'", $rule);
                try{
                    $this->valid = eval($rule);
                }catch(\Nyu\Exception\ValidateException $e){
                    $this->message = $e->getMessage();
                    $this->valid = false;
                    continue;
                }
            }
        }
        return $this->isValid();
    }
    
    public function getMessage() {
        return $this->message;
    }
}