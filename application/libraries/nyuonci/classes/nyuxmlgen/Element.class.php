<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Namespace nyuxmlgen
 */
 namespace nyuxmlgen;
/**
 * Cria um Elemento XML que ir� compor um documento XML ou outros Elementos
 * @author Maycow Alexandre Antunes
 * @version 1.5
 * @package nyuxmlgen
 */
class Element{
    /**
     * Nome do Elemento
     * @var string
     */
    protected $name;
    /**
     * Valor do Elemento (caso seja o �timo n�vel da �rvore)
     * @var string
     */
    protected $value;
    /**
     * Array com os Elementos filhos que comp�em o Elemento
     *  @var array
     */
    protected $Elements = array();
    /**
     * Array com os Atributos pertencentes ao Elemento
     * @var array
     */
    protected $Attributes = array();
    /**
     * Se true, o Elemento ir� conter caracteres especiais (Enter e Tabula��o)
     *  @var boolean
     */
    protected $specialChars = false;
    /**
     * �ndice do Elemento na �rvore de elementos do Elemento Pai
     * @var integer
     */
    protected $index;
    /**
     * Se false, n�o ir� imprimir a tag se nao existir valor
     * @var boolean
     */
    protected $printTag = true;
    /**
     * Se true, for�a a impress�o da tag aberta (<tag></tag>)
     * se false, imprime a tag fechada (<tag/>)
     * @var boolean
     */
    protected $openTag = false;

    /**
     * Construtor da classe Element. Cria um Objeto Element.
     * @param string $name Nome do Elemento
     * @param string $value Valor do Elemento (caso seja o �timo n�vel da �rvore)
     * @param boolean $specialChars Se true, o elemento ir� conter caracteres especiais (Enter e Tabula��o)
     * @param boolean $printTag Se false, n�o ir� imprimir a tag se nao existir valor
     */
    public function __construct($name, $value= '', $specialChars = false, $printTag = true, $openTag = false){
        $this->setName($name);
        if($value != ''){
            $this->setValue($value);
        }
        if($specialChars){ 
            $this->setSpecialChars($specialChars);
        }
        $this->setPrintTag($printTag);
        $this->setOpenTag($openTag);
    }
    
    /**
     * Seta o nome do Elemento
     * @param string $name Nome do Elemento
     * @return Element o objeto atual
     */
    public function setName($name){
        $this->name = $name;
        return $this;
    }

    /**
     * Retorna o nome do Elemento
     * @return string Nome do Elemento
     */
    public function getName(){
        return $this->name;
    }

    /**
     * Seta o valor do Elemento
     * @param string $value Valor do Elemento
     * @return Element o objeto atual
     */
    public function setValue($value){
        $this->value = (String)$value;
        return $this;
    }

    /**
     * Retorna o valor do Elemento
     * @return string Valor do Elemento
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * Seta o atributo $specialChars, respons�vel por indicar se o Elemento
     * ir� conter ou n�o caracteres especiais (Enter e Tabula��o)
     * @param boolean $specialChars Se true, o elemento ir� conter caracteres especiais (Enter e Tabula��o)
     * @return Element o objeto atual
     */
    public function setSpecialChars($specialChars){
        $this->specialChars = $specialChars;
        return $this;
    }
    
    /**
     * Retorna o valor do atributo $specialChars, respons�vel por indicar
     * se o Elemento ir� conter ou n�o caracteres especiais (Enter e Tabula��o)
     * @return boolean Se true, o elemento ir� conter caracteres especiais (Enter e Tabula��o)
     */
    public function getSpecialChars(){
        return $this->specialChars;
    }

    /**
     * Seta o �ndice do Elemento na �rvore de elementos do Elemento Pai
     * @param integer $index �ndice do Elemento na �rvore de elementos do Elemento Pai
     * @return Element o objeto atual
     */
    public function setIndex($index){
        $this->index = $index;
        return $this;
    }

    /**
     * Retorna o �ndice do Elemento na �rvore de elementos do Elemento Pai
     * @return integer $index �ndice do Elemento na �rvore de elementos do Elemento Pai
     */
    public function getIndex(){
        return $this->index;
    }

    /**
     * Seta o valor do atributo $printTag
     * @param boolean $printTag Se false, n�o ir� imprimir a tag se nao existir valor
     * @return Element o objeto atual
     */
    public function setPrintTag($printTag){
        $this->printTag = $printTag;
        return $this;
    }

    /**
     * Retorna o valor do atributo printTag
     * @return boolean Se false, n�o ir� imprimir a tag se nao existir valor
     */
    public function getPrintTag(){
        return $this->printTag;
    }

    /**
     * Seta o valor do atributo $openTag
     * @param boolean $openTag Se true, for�a a impress�o da tag aberta (<tag></tag>)
     * se false, imprime a tag fechada (<tag/>)
     * @return Element o objeto atual
     */
    public function setOpenTag($openTag){
        $this->openTag = $openTag;
        return $this;
    }

    /**
     * Retorna o valor do atributo openTag
     * @return boolean Se true, for�a a impress�o da tag aberta (<tag></tag>)
     * se false, imprime a tag fechada (<tag/>)
     */
    public function getOpenTag(){
        return $this->openTag;
    }

    /**
     * Adiciona um elemento � arvore de Elementos do Elemento
     * @param Element $Element Objeto Element a ser adicionado
     * @return Element o objeto atual
    */
    public function addElement($element){
        $this->Elements[] = $element;
        $element->setIndex(count($this->Elements) - 1);
        return $this;
    }

    /**
     * Alias para Element::addElement(). Adiciona um elemento � arvore de Elementos do Elemento
     * @param Element $element Objeto Element a ser adicionado
     * @return Element o objeto atual
    */
    public function addChild($element){
        return $this->addElement($element);
    }

    /**
     * Remove um elemento da arvore de Elementos do Elemento
     * @param Element $element Objeto Element a ser removido
     * @return Element o objeto atual
     */
    public function removeElement($element){
        unset($this->Elements[$element->getIndex()]);
        return $this;
    }

    /**
     * Alias para Element::removeElement(). Remove um elemento da arvore de Elementos do Elemento
     * @param Element $element Objeto Element a ser removido
     * @return Element o objeto atual
     */
    public function removeChild($element){
        return $this->removeElement($element);
    }

    /**
     * Adiciona um atributo ao Elemento
     * @param string $name Nome do atributo
     * @param string $value Valor do atributo
     * @return Element o objeto atual
     */
    public function addAttribute($name,$value){
        $this->Attributes[$name]['name'] = $name;
        $this->Attributes[$name]['value'] = $value;
        return $this;
    }

    /**
     * Alias para Element::addAttribute(). Adiciona um atributo ao Elemento
     * @param string $name Nome do atributo
     * @param string $value Valor do atributo
     * @return Element o objeto atual
     */
    public function addAttr($name, $value){
        return $this->addAttribute($name, $value);
    }

    /**
     * Remove um atributo do Elemento
     * @param string $name Nome do atributo
     * @return Element o objeto atual
     */
    public function removeAttribute($name){
        unset($this->Attributes[$name]);
        return $this;
    }

    /**
     * Alias para Element::removeAttribute(). Remove um atributo do Elemento
     * @param string $name Nome do atributo
     * @return Element o objeto atual
     */
    public function removeAttr($name){
        return $this->removeAttribute($name);
    }

    /**
     * Retorna o valor de um atributo
     * @param string $name Nome do atributo
     */
    public function getAttribute($name){
        return $this->Attributes[$name];
    }

    /**
     * Alias para Element::getAttribute(). Retorna o valor de um atributo
     * @param string $name Nome do atributo
     */
    public function getAttr($name){
        return $this->getAttribute($name);
    }

    /**
     * Retorna os atributos do Elemento, em formato String
     * @return string Atributos do Elemento
     */
    public function getAttributes(){
        $s = "";
        if(count($this->Attributes) > 0){
            foreach ($this->Attributes as $name => $Attribute){
                $s .= " {$Attribute['name']}=\"{$Attribute['value']}\"";
            }
        }
        return $s;
    }

    /**
     * Retorna os Elementos pertencentes � arvore de Elementos do Elemento em quest�o
     * @return array array com todos os objetos Element da �rvore
     */
    public function getElements(){
        return $this->Elements;    
    }

    /**
     * Retorna o Elemento em formato String para que seja utilizado na constru��o do Documento XML
     * @return string Elemento e �rvore de elementos componentes em formato String
     */
    public function toString(){
        $arg = func_get_args();
        if(isset($arg[0])){
            $this->specialChars = $arg[0];
        }
        $s = "<". $this->getName() . $this->getAttributes() . ">";
        if($this->value != '' && count($this->Elements) == 0){ // Se possui valor e n�o possui filhos
            $s .= (String)$this->getValue();
            $s .= "</" . $this->getName() . ">";
        }elseif(count($this->Elements) > 0){ // Se possui filhos
            foreach ($this->Elements as $index => $Element){
                if($this->specialChars){
                    $s .= "\r\n\t";
                    if(isset($arg[1])){
                        for($i = 0; $i < $arg[1]; $i++){
                            $s .= "\t";
                        }
                    }
                }
                $s .= $Element->toString($this->specialChars,@$arg[1]+1);
            }
            if($this->specialChars){
                $s .= "\r\n";
                if(isset($arg[1])){
                    for($i = 0; $i < $arg[1]; $i++){
                        $s .= "\t";
                    }
                }
            }
            // Se possui filhos e valor (casos bem espec�ficos), insere o valor ap�s os filhos
            if($this->value != ''){
                $s .= (String)$this->getValue();
            }
            $s .= "</" . $this->getName() . ">";
        }else{
            if($this->printTag){
                if($this->openTag){
                    $s = $s = "<". $this->getName() . $this->getAttributes() . "></" . $this->getName() . ">";
                }else{
                    $s = $s = "<". $this->getName() . $this->getAttributes() . "/>";
                }
            }else{
                $s = '';
            }
        }
		
        return $s;    
    }

    /**
     * M�todo m�gico __toString
     */
    public function __toString(){
        return $this->toString();
    }
}