<?php
/**
 * 2017 NyuOnCI
 */
namespace Nyu\Utils\Format;
/**
 * Classe para tratamento simplificado de datas
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.0
 * @since 5.1
 */
class DateTime{
    
    /**
     * Retorna a data atual no formato informado
     * @param type $format (optional) padrão 'd/m/Y H:i:s', formato da data, de 
     * acordo com http://au.php.net/manual/en/function.date.php
     * @return string
     */
    public static function now($format = 'd/m/Y H:i:s'){
        return date($format);
    }
    
    /**
     * Retorna a data atual no formato 'd/m/Y'
     * @return string
     */
    public static function nowDate(){
        return date('d/m/Y');
    }
    
    /**
     * Retorna a data atual no formato 'Y-m-d H:i:s'
     * @return string
     */
    public static function nowDb(){
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Retorna a data atual no formato 'Y-m-d'
     * @return string
     */
    public static function nowDateDb(){
        return date('Y-m-d');
    }
    
    /**
     * Formata uma data de acordo com o formato informado
     * @param string|DateTime $date data a formatar
     * @param string $format formato da data, de 
     * acordo com http://au.php.net/manual/en/function.date.php
     * @return string
     */
    public static function format($date, $format){
        if(!is_object($date)){
            $date = str_replace("/", "-", $date);
            $date = new DateTime($date);
        }
        return $date->format($format);
    }

    /**
     * Formata uma data para o padrão do banco de dados
     * @param string $date data a formatar
     * @return string
     */
    public static function dateFormToDb($date){
        return self::format($date, 'Y-m-d');
    }

    /**
     * Formata uma data para o padrão brasileiro
     * @param string $date data a formatar
     * @return string
     */
    public static function dateDbToForm($date){
        return self::format($date, 'd/m/Y');
    }

    /**
     * Formata uma data para o padrão do banco de dados com hora e minutos
     * @param string $date data a formatar
     * @return string
     */
    public static function dateTimeFormToDb($date){
        $date = str_replace("/", "-", $date);
        return self::format($date, 'Y-m-d H:i:s');
    }

    /**
     * Formata uma data para o padrão brasileiro com hora e minutos
     * @param string $date data a formatar
     * @return string
     */
    public static function dateTimeDbToForm($date){
        return self::format($date, 'd/m/Y H:i:s');
    }
    
    /**
     * Compara duas datas. Retorna 1 se a 1a for maior, 2 se a 2a for maior ou 
     * 0 se as duas forem iguais
     * @param string $date1
     * @param string $date2
     * @return int
     */
    public static function compareDates($date1, $date2){
        $date1 = str_replace("/", "-", $date1);
        $date2 = str_replace("/", "-", $date2);
        $d1 = new DateTime($date1);
        $d2 = new DateTime($date2);
        if($d1 > $d2){
            return 1;
        }elseif($d2 > $d1){
            return 2;
        }else{
            return 0;
        }
    }
    
    /**
     * Adiciona à data o intervalo e retorna no formato
     * @param string $date data a adicionar
     * @param string|DateInterval $interval string no padrao 
     * de http://php.net/manual/en/class.dateinterval.php ou o objeto DateInterval
     * @param string $format (optional) padrão 'd/m/Y', formato da data, de 
     * acordo com http://au.php.net/manual/en/function.date.php
     * @return string
     */
    public static function add($date, $interval, $format = 'd/m/Y'){
        $date = str_replace("/", "-", $date);
        $date = new DateTime($date);
        if(!is_object($interval)){
            $interval = new DateInterval($interval);
        }
        $date->add($interval);
        return self::format($date, $format);
    }
    
    /**
     * Adiciona à data os dias e retorna no formato
     * @param string $date data a adicionar
     * @param int $days dias a adicionar
     * @param string $format (optional) padrão 'd/m/Y', formato da data, de 
     * acordo com http://au.php.net/manual/en/function.date.php
     * @return string
     */
    public static function addDays($date, $days, $format = 'd/m/Y'){
        return self::add($date, DateInterval::createFromDateString($days." days"), $format);
    }
    
    /**
     * Subtrai à data o intervalo e retorna no formato
     * @param string $date data a subtrair
     * @param string|DateInterval $interval string no padrao 
     * de http://php.net/manual/en/class.dateinterval.php ou o objeto DateInterval
     * @param string $format (optional) padrão 'd/m/Y', formato da data, de 
     * acordo com http://au.php.net/manual/en/function.date.php
     * @return string
     */
    public static function sub($date, $interval, $format = 'd/m/Y'){
        $date = str_replace("/", "-", $date);
        $date = new DateTime($date);
        if(!is_object($interval)){
            $interval = new DateInterval($interval);
        }
        $date->sub($interval);
        return self::format($date, $format);
    }
    
    /**
     * Subtrai à data os dias e retorna no formato
     * @param string $date data a subtrair
     * @param int $days dias a subtrair
     * @param string $format (optional) padrão 'd/m/Y', formato da data, de 
     * acordo com http://au.php.net/manual/en/function.date.php
     * @return string
     */
    public static function subDays($date, $days, $format = 'd/m/Y'){
        return self::sub($date, DateInterval::createFromDateString($days." days"), $format);
    }
    
    /**
     * Retorna a diferença entre duas datas
     * @param string|DateTime $date1
     * @param string|DateTime $date2
     * @return int
     */
    public static function diff($date1, $date2){
        if(!is_object($date1)){
            $date1 = str_replace("/", "-", $date1);
            $date1 = new DateTime($date1);
        }
        if(!is_object($date2)){
            $date2 = str_replace("/", "-", $date2);
            $date2 = new DateTime($date2);
        }
        return $date1->diff($date2)->format("%r%a");
    }
}