<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Namespace nyuxmlgen
 */
 namespace nyuxmlgen;
/**
 * Cria um Documento XML a partir de Elementos XML
 * @author Maycow Alexandre Antunes
 * @version 1.4
 */
class XML extends Element{
    /**
     * Vers�o do Documento XML
     * @var string
     */
    protected $version;
    /**
     * Charset (Conjunto de Caracteres) utilizado no documento
     * @var string
     */
    protected $encoding;
    /**
     * Se true, o documento criado ir� conter caracteres especiais (Enter e Tabula��o)
     * @var boolean
     */
    protected $specialChars = false;

    /**
     * Construtor do XML. Cria um Objeto XML.
     * @param string $version Vers�o do Documento XML
     * @param string $encoding Charset (Conjunto de Caracteres) utilizado no documento
     * @param boolean $specialChars Se true, o documento criado ir� conter caracteres 
     * especiais (Enter e Tabula��o)
     * @param string $name Nome do documento XML - ser� utilizado no download do arquivo
     */
    public function __construct($version = '', $encoding = '', $specialChars = false, $name = ''){
        if($version){
            $this->version = $version;
        }
        if($encoding){
            $this->encoding = $encoding;
        }
        if($specialChars){
            $this->specialChars = $specialChars;
        }
        if($name){
            $this->name = $name;
        }else{
            $this->name = "xml";
        }
   }    

    /**
     * Retorna o documento XML gerado em String
     * @return String
     */
    public function toString(){
        $s = "<?xml version=\"{$this->version}\"".((isset($this->encoding))?$s .= " encoding=\"" . $this->encoding . "\"":"")." ?>";
        $s .= "\r\n";
        $level = -1;
        foreach ($this->Elements as $index => $Element){
            $s .= $Element->toString($this->specialChars,$level+1) . (($this->specialChars) ? "\n" : "")."\r\n";
        }
        return $s;
    }

    /**
     * Faz o download do documento XML gerado
     * @param  string $name (Opcional) Nome do documento XML a fazer o download
     */
    public function getXMLFile(){
        $arg = func_get_args();
        if(isset($arg[0])){
            $this->name = $arg[0];
        }
        header("Content-type: application/xml");
        header("Content-Disposition: attachment; filename=\"{$this->name}.xml\"");
        echo $this->toString();
    }
    
    /**
     * Salva no servidor o documento XML gerado
     * @param string $path Diret�rio onde ser� salvo o arquivo
     * @param string $name (Opcional) Nome do documento XML
     * @return boolean true se foi poss�vel gravar o arquivo, false se n�o for poss�vel
     */
    public function saveXMLFile($path, $name = null){
        if($name != null){
            $this->name = $name;
        }
        $path = $path."/".(($this->name)?$this->name:"xml").".xml";
        
        if(!$arq = fopen($path, "w")){
            return false;
        }
        if(!fwrite($arq, $this->toString())){
            return false;
        }
        if(fclose($arq)){
            return true;
        }else{
            return false;
        }
    }
}
?>