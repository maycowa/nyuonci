<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe de regras de validação de objetos
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.1.1
 * @since 5.0
 */
class NyuValidateRule {
    
    /**
     * Atributo a validar
     * @var string
     */
    protected $attribute;
    
    /** 
     * Regra para validar o atributo
     * @var string
     */
    protected $rule;
    
    /**
     * Valor a comparar
     * @var mixed
     */
    protected $compareTo;
    
    /**
     * Mensagem a exibir
     * @var string
     */
    protected $message;
    
    /**
     * Construtor da classe NyuValidateRule
     * @param string $attribute Atributo a validar
     * @param string $rule Regra para validar o atributo
     * @param mixed $compareTo (Opcional) Valor a comparar
     */
    public function __construct($attribute, $rule, $compareTo = null, $message = null, $fieldName = null){
        $this->attribute = $attribute;
        $this->rule = $rule;
        $this->compareTo = $compareTo;
        
        $fieldName = ($fieldName ? $fieldName : $attribute);
        
        if(isset($message) && $message != null){
            $this->message = $message;
        }else{
            if($this->rule == NYU_RULE_MORE){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_MORE_EXCEPTION);
            }elseif($this->rule == NYU_RULE_LESS){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_LESS_EXCEPTION);
            }elseif($this->rule == NYU_RULE_EQUAL){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_EQUAL_EXCEPTION);
            }elseif($this->rule == NYU_RULE_DIFF){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_DIFF_EXCEPTION);
            }elseif($this->rule == NYU_RULE_EXISTS){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_EXISTS_EXCEPTION);
            }elseif($this->rule == NYU_RULE_MIN_LEN){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_MIN_LEN_EXCEPTION);
            }elseif($this->rule == NYU_RULE_MAX_LEN){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_MAX_LEN_EXCEPTION);
            }elseif($this->rule == NYU_RULE_EQUAL_LEN){
                $this->message = str_replace('#FIELD#', $fieldName, NYU_RULE_EQUAL_LEN_EXCEPTION);
            }
        }
    }
    
    /**
     * Validação customizada. Pode ser informado diretamente no construtor da classe.
     * Este método é interessante para separar regras customizadas das regras
     * disponíveis por padrão
     * @param string $rule Regra de validação, um código PHP utilizando 
     * necessariamente a variável $a (valor do atributo). Caso seja necessário
     * testar com o valor informado em $compareTo, utilizar a variável $b
     * @param string $message Mensagem de erro em caso do valor ser inválido
     */
    public function customValidation($rule, $message, $compareTo = null){
        $this->rule = $rule;
        $this->message = $message;
        if(isset($compareTo)){
            $this->compareTo = $compareTo;
        }
    }
    
    /**
     * Retorna a mensagem de erro
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Altera a mensagem padrão da regra
     * @param string $message Nova mensagem
     */
    public function setMessage($message) {
        $this->message = $message;
    }
        
    /**
     * Retorna o atributo $attribute
     * @return string
     */
    public function getAttribute() {
        return $this->attribute;
    }

    /**
     * Retorna o atributo $rule
     * @return string
     */
    public function getRule() {
        return $this->rule;
    }

    /**
     * Retorna o atributo $compareTo
     * @return mixed
     */
    public function getCompareTo() {
        return $this->compareTo;
    }

    public function __toString() {
        $str = "if(".$this->rule."){";
        $str .= 'return true;';
        $str .= "}else{";
        $str .= "throw new NyuValidateException('".$this->getMessage()."');";
        $str .= 'return false;';
        $str .= "}";
        return $str;
    }
}