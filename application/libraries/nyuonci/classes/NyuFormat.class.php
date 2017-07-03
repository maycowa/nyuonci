<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe de formatação de dados do Nyu
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.3.1
 **/

class NyuFormat {
    
    /**
     * Formata data vinda no padrão mysql para o padrão brasileiro
     * @param string $date Data a formatar
     * @return string
     */
    public static function dateTimeFormat($date){
        $dt = explode(" ",$date);
        $date = explode("-",$dt[0]);
        return $date[2] . "/" . $date[1] . "/" . $date[0] . " " . $dt[1];
    }
    
    /**
     * Formata um valor no formato brasileiro de moeda
     * @param number $number Número a formatar
     * @param number $before Casas decimais
     * @return string
     */
    public static function money($number, $before=2) {
        return number_format($number, $before, ",", '');
    }

    /**
     * Formata um texto para ser convertido em json
     * @param string|array $str Texto a ser formatado
     * @return string|array
     */
    public static function jsonFormatIn($str) {
        if (is_array($str)) {
            return array_map("nyuFormat::jsonFormatIn", $str);
        } else {
            return htmlentities($str);
        }
    }

    /**
     * Formata um texto json para ser convertido em html
     * @param string|array $str Texto a ser formatado
     * @return string|array
     */
    public static function jsonFormatOut($str) {
        if (is_array($str)) {
            return array_map("nyuFormat::jsonFormatOut", $str);
        } else {
            return html_entity_decode($str);
        }
    }

    /**
     * Formata um array para/de json
     * @param string|array $var Valor a formatar
     * @param string $in_out Fixo: IN / OUT indica se está convertendo em json 
     * (IN) ou de json (OUT)
     * @return string|array
     */
    public static function jsonFormat($var, $in_out) {
        if (strtoupper($in_out) == "IN") {
            $var = NyuFormat::jsonFormatIn($var);
            return json_encode($var);
        } else {
            $var = json_decode($var, true);
            return NyuFormat::jsonFormatOut($var);
        }
    }

    /**
     * Função que remove acentos
     * @param string $str Texto a formatar
     * @return string
     */
    public static function removeAccents($str) {
        $from = 'ÀÁÃÂÉÊÍÓÕÔÚÜÇàáãâéêíóõôúüç';
        $to = 'AAAAEEIOOOUUCaaaaeeiooouuc';
        $str = strtr($str, $to, $from);
        return $str;
    }

    /**
     * Função que remove acentos na formatação HTML
     * @param string $str Texto a formatar
     * @param string $enc Encoding de charset a utilizar
     * @return string
     */
    public static function removeAccentsHtml($str, $enc = "UTF-8") {

        $acentos = array(
            'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
            'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
            'C' => '/&Ccedil;/',
            'c' => '/&ccedil;/',
            'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
            'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
            'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
            'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
            'N' => '/&Ntilde;/',
            'n' => '/&ntilde;/',
            'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
            'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
            'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
            'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
            'Y' => '/&Yacute;/',
            'y' => '/&yacute;|&yuml;/',
            'a.' => '/&ordf;/',
            'o.' => '/&ordm;/');

        return preg_replace($acentos, array_keys($acentos), htmlentities($str, ENT_NOQUOTES, $enc));
    }
    
    /**
     * Encripta uma variável
     * @param mixed $variavel Variável a encriptar
     * @param string $senha Senha da criptografia
     * @param int $iv_len Nível de criptogarfia
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function _crypt($variavel, $senha=null, $iv_len = 16){
        $variavel .= "\x13";
        $n = strlen($variavel);
        if ($n % 16) $variavel .= str_repeat("\0", 16 - ($n % 16));
        $i = 0;

        $Enc_Texto = '';
        while ($iv_len-- > 0) {
            $Enc_Texto .= chr(mt_rand() & 0xff);
        }
        $iv = substr($senha ^ $Enc_Texto, 0, 512);
        while ($i < $n) {
            $Bloco = substr($variavel, $i, 16) ^ pack('H*', md5($iv));
            $Enc_Texto .= $Bloco;
            $iv = substr($Bloco . $iv, 0, 512) ^ $senha;
            $i += 16;
        }
        $variavel = base64_encode($Enc_Texto);
        $hex='';
        for ($i=0; $i < strlen($variavel); $i++){
            $hex .= dechex(ord($variavel[$i]));
        }
        return $hex;
    }

    /**
     * Desencripta uma variável
     * @param string $variavel Variável a desencriptar
     * @param string $senha Senha da criptografia
     * @param int $iv_len Nível de criptogarfia
     * @return mixed
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function _decrypt($variavel, $senha=null, $iv_len = 16){
        $string='';
        for ($i=0; $i < strlen($variavel)-1; $i+=2){
            $string .= chr(hexdec($variavel[$i].$variavel[$i+1]));
        }
        $variavel = base64_decode($string);
        $n = strlen($variavel);
        $i = $iv_len;
        $texto = '';
        $iv = substr($senha ^ substr($variavel, 0, $iv_len), 0, 512);
        while ($i < $n) {
            $Bloco = substr($variavel, $i, 16);
            $texto .= $Bloco ^ pack('H*', md5($iv));
            $iv = substr($Bloco . $iv, 0, 512) ^ $senha;
            $i += 16;
        }
        $texto = preg_replace('/\\x13\\x00*$/', '', $texto);
        return $texto;
    }
    
    /**
     * Serializa uma variável e a encripta
     * @param mixed $variavel Variável a encriptar
     * @param string $senha Senha da criptografia
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function _cryptSerialize($variavel, $senha=null){
        $variavel = serialize($variavel);
        return _crypt($variavel, $senha);
    }
    
    /**
     * Desencripta uma variável serializada
     * @param string $variavel Variável a desencriptar
     * @param string $senha Senha da criptografia
     * @return mixed
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function _decryptSerialize($variavel, $senha=null){
        return unserialize(_decrypt($variavel, $senha));
    }

    /**
     * Preenche à esquerda a string $str - se necessário - com $fill até 
     * a string possuir $length quantidade de caracteres
     * @param string $str String a preencher
     * @param string $fill Texto a preencher
     * @param int $length Quantidade de caracteres
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function lpad( $str, $fill, $length ){
        return str_pad($str, $length, $fill, STR_PAD_LEFT);
    }

    /**
     * Preenche à direita a string $str - se necessário - com $fill até 
     * a string possuir $length quantidade de caracteres
     * @param string $str String a preencher
     * @param string $fill Texto a preencher
     * @param int $length Quantidade de caracteres
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function rpad( $str, $fill, $length ){
        return str_pad($str, $length, $fill, STR_PAD_RIGHT);
    }

    /**
     * Converte uma string para seu correspondente em formato binário, 
     * utilizando $delimiter para delimitar cada caractere
     * @param string $str Texto a converter
     * @param string $delimiter delimitador de caractere
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function strToBin($str, $delimiter = '2'){ 
        for($i=0; $i<strlen($str); $i++){ 
            $n_str[] = base_convert(ord($str[$i]), 10, 2); 
        } 
        return implode($delimiter, $n_str); 
    }
    
    /**
     * Converte um texto em formato binário - convertido através do método 
     * strToBin novamente para texto
     * @param string $str Texto em formato binário a converter
     * @param string $delimiter delimitador de caracteres
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function binToStr($str, $delimiter = '2'){ 
        $str = explode($delimiter, $str); 
        $string = '';
        for($i=0;$i<count($str);$i++){ 
            $string .= chr(bindec($str[$i])); 
        } 
        return $string; 
    }
    
    /**
     * Converte uma string para seu correspondente em hexadecimal
     * @param string $string Texto a converter
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function strToHex($string){
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }
    
    /**
     * Converte uma string em formato hexadecimal para seu correspondente em 
     * texto
     * @param string $hex String a converter
     * @return string
     * @author Fernando Gurkievicz <fernando@hazo.com.br>
     */
    public static function hexToStr($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    /**
     * Aplica uma máscara à string
     * @param string $val Texto original
     * @param string @mask Máscara que utiliza # como padrão de caracteres
     */
    public static function mask($val, $mask){
        $maskared = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++){
            if($mask[$i] == '#'){
                if(isset($val[$k]))
                $maskared .= $val[$k++];
            }else{
                if(isset($mask[$i]))
                    $maskared .= $mask[$i];
            }
        }
     return $maskared;
    }
}