<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe base para regras de Negócio e validação do objeto.
 * As classes devem extender essa classe para utilizar suas funcionalidades
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 0.1
 * @since 6.0
 * @uses NyuValidate
 * @uses NyuValidateRule
 */
class NyuBusiness{
    
    /**
     * Regras de validação do objeto
     * @var array
     */
    protected $rules = array();
    
    /**
     * Objeto NyuModel a validar e gravar
     * @var object
     */
    protected $object;
    
    /**
     * Object NyuController atual, para executar métodos de controller dentro 
     * das triggers
     * @var object
     */
    protected $controller;
    
    /**
     * Indicador interno que verifica se as regras foram formatadas corretamente
     * @var boolean
     */
    protected $haveRulesFormatted = false;
    
    /**
     * Construtor da classe NyuBusiness
     * @param object $object Objeto NyuModel a validar e gravar
     * @param array $rules Array com as regras a validar
     * @param object $controller Objeto controller para executar métodos dentro
     * das triggers
     */
    public function __construct($object = null, $rules = null, $controller = null) {
        // Se foi informado um objeto
        if(isset($object) && $object != null){
            $this->setObject($object);
        }
        
        // Se foram informadas as regras
        if(isset($rules) && $rules != null){
            $this->setRules($rules);
        }
        
        // Se foi informada a controller
        if(isset($controller) && $controller != null){
            $this->setController($controller);
        }
    }
    
    /**
     * Método que é executado antes da validação
     */
    public function triggerBeforeValidate(){}
    
    /**
     * Método que é executado após a validação com sucesso (antes de retornar)
     */
    public function triggerAfterValidateSuccess(){}
    
    /**
     * Método que é executado após a validação com falha (antes de chamar a excessão)
     */
    public function triggerAfterValidateFail(){}
    
    /**
     * Método que é executado antes da gravação
     */
    public function triggerBeforeSave(){}
    
    /**
     * Método que é executado após a gravação com sucesso
     */
    public function triggerAfterSaveSuccess(){}
    
    /**
     * Método que é executado após a gravação com falha
     */
    public function triggerAfterSaveFail(){}

    /**
     * Método base para agrupar a execução das regras de negócio
     * Neste método estático podemos criar o objeto e executar a validação
     * ou gravação, conforme necessário.
     * Segue exemplo:
     * public static function run($object = null, $controller = null){
     * 
     *     $o = parent::run($object, $controller); // Chama o método pai (cria uma instância do objeto com o objeto a validar e a controller atual)
     *     $rules = array(array('attr' => 'field1', // Regras de validação
     *                          'max' => 2));
     *     $o->setRules($rules); // Seta as regras de validação no objeto.
     *     $o->validate(); // Usar este método para apenas validar
     *     // * Para processamentos antes da validação, sobrescrever o método
     *     // triggerBeforeValidate()
     *     // * Para processamentos após a validação, sobrescrever os métodos
     *     // triggerAfterValidateSuccess() e triggerAfterValidateFail()
     *     //$o->save(); // Usar este método para validar e tentar gravar
     *     // * Para processamentos antes da gravação (e após a validação), 
     *     // sobrescrever o método triggerBeforeSave()
     *     // * Para processamentos após a gravação, sobrescrever os métodos
     *     // triggerAfterSaveSuccess() e triggerAfterSaveFail()
     *     return $o;
     * }
     * 
     * @param object $object Objeto NyuModel a validar e gravar
     * @param object $controller Objeto controller para executar métodos dentro
     */
    public static function run($object = null, $controller = null){
        $class = get_called_class();
        return new $class($object, null, $controller); // Retorna uma instância do objeto
    }
    
    /**
     * Valida o objeto
     * @throws NyuBusinessException Em caso de objeto não validado, carrega um erro
     * @return boolean true em caso de sucesso e false em caso de falha
     */
    public function validate(){
        if(isset($this->rules) && $this->rules != null){ // Se há regras de validação, faz a validação, senão, ignora a validação
            if(!$this->haveRulesFormatted){ // Se as regras não foram formatadas, provavelmente elas foram definidas na declaração da classe, então deve formatar
                $this->setRules($this->rules); // Formata as regras
            }
            $this->triggerBeforeValidate(); // Executa um código antes de validar
            $val = new NyuValidate($this->object, $this->rules); // Cria o objeto de validação
            $val->validate(); // Valida o objeto
            if(!$val->isValid()){ // Se não está válido
                $this->triggerAfterValidateFail(); // Executa um processamento após a validação com falha
                throw new NyuBusinessException($val->getMessage(), 99); // Retorna a mensagem de erro em uma Excessão
                return false; // Retorna falha
            }
            $this->triggerAfterValidateSuccess(); // Executa um processamento após a validação com sucesso
        }
        return true; // Retorna sucesso
    }
    
    /**
     * Salva o objeto
     * @return boolean true em caso de sucesso e false em caso de falha
     */
    public function save(){
        if(isset($this->object)){ // Se há objeto a gravar, segue o processamento, senão, ignora e retorna false
            if($this->validate()){ // Se o objeto está validado
                $this->triggerBeforeSave(); // Trigger antes da gravação
                if($this->object->save()){ // Tenta gravar o objeto
                    $this->triggerAfterSaveSuccess(); // Trigger após o processamento com sucesso
                    return true;
                }else{
                    $this->triggerAfterSaveFail(); // Trigger após o processamento com falha
                    $message = \NyuCore::getException(); // Pega a exceção gerada na NyuModel
                    throw new NyuBusinessException($message, 99); // Retorna a mensagem de erro em uma Excessão
                    return false;
                }
            }
        }
        return false;
    }
    
    /**
     * Seta o objeto a validar
     * @param object $obj
     */
    public function setObject($obj){
        $this->object = $obj;
    }
    
    /**
     * Retorna o objeto a validar
     * @return object
     */
    public function getObject(){
        return $this->object;
    }
    
    /**
     * Seta a controller a utilizar dentro das triggers
     * @param object $controller objeto controller a utilizar
     */
    public function setController($controller){
        $this->controller = $controller;
    }
    
    /**
     * Seta as regras de validação
     * @param array $rules
     * @return object O objeto atual
     */
    public function setRules($rules){
        foreach($rules as $rule){
            $this->addRule($rule);
        }
        return $this;
    }
    
    /**
     * Adiciona uma regra de validação
     * @param NyuValidateRule|array $rule regra de validação
     * @throws NyuBusinessException
     */
    public function addRule($rule){
        $this->haveRulesFormatted = true; // Indica que as regras foram validadas corretamente
        if(is_object($rule)){ // Se é um objeto NyuValidateRule
            if(is_a($rule, "\NyuValidateRule")){
                $this->rules[] = $rule;
            }else{
                throw new NyuBusinessException(NYU_BUSINESS_EXCEPTION_00, 00);
            }
        }elseif(is_array($rule)){ // Senão, se é um array
            
            if(array_key_exists('min', $rule)){ // No mínimo o valor informado
                $nyuRule = NYU_RULE_MORE_EQ;
                $ruleValue = $rule['min'];
            }elseif(array_key_exists('max', $rule)){ // No máximo o valor informado
                $nyuRule = NYU_RULE_LESS_EQ;
                $ruleValue = $rule['max'];
            }elseif(array_key_exists('less_than', $rule)){ // Menor que o valor informado
                $nyuRule = NYU_RULE_LESS;
                $ruleValue = $rule['less_than'];
            }elseif(array_key_exists('more_than', $rule)){ // Maior que o valor informado
                $nyuRule = NYU_RULE_MORE;
                $ruleValue = $rule['more_than'];
            }elseif(array_key_exists('equal', $rule)){ // Igual ao valor informado
                $nyuRule = NYU_RULE_EQUAL;
                $ruleValue = $rule['equal'];
            }elseif(array_key_exists('required', $rule)){ // Valor Obrigatório
                $nyuRule = NYU_RULE_EXISTS;
                $ruleValue = $rule['required'];
            }elseif(array_key_exists('min_length', $rule)){ // Tamanho mínimo
                $nyuRule = NYU_RULE_MIN_LEN;
                $ruleValue = $rule['min_length'];
            }elseif(array_key_exists('max_length', $rule)){ // Tamanho máximo
                $nyuRule = NYU_RULE_MAX_LEN;
                $ruleValue = $rule['max_length'];
            }elseif(array_key_exists('length', $rule)){ // Tamanho exato
                $nyuRule = NYU_RULE_EQUAL_LEN;
                $ruleValue = $rule['length'];
            }elseif(array_key_exists('different', $rule)){ // Diferente de
                $nyuRule = NYU_RULE_DIFF;
                $ruleValue = $rule['different'];
            }elseif(array_key_exists('custom', $rule)){ // Regra customizada
                $nyuRule = $rule['custom'];
                $ruleValue = $rule['value']; // Para regra customizada, se irá utilizar um valor a comparar, utilizar o índice 'value'
            }
            
            $fieldName = null;
            if(array_key_exists('fieldName', $rule)){
                $fieldName = $rule['fieldName'];
            }
            
            // Mensagem de erro personalizada
            $message = null;
            if(array_key_exists('message', $rule)){
                $message = $rule['message'];
            }
            
            // Cria o objeto de regra de validação
            $vr = new NyuValidateRule($rule['attr'], $nyuRule, $ruleValue, $message, $fieldName);
            $this->rules[] = $vr; // Adiciona no array a regra
        }else{
            // Se não é uma regra, gera uma exceção
            throw new NyuBusinessException(NYU_BUSINESS_EXCEPTION_00, 00);
        }
    }
}