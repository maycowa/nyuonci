<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe de tratamento de erros e páginas de erro
 * @package NyuCore
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 */
class NyuErrorManager{
    /**
     * Arquivo que será exibido para erros 401
     * @var string
     */
    protected static $file_401 = _401_ERROR_FILE_;
    /**
     * Arquivo que será exibido para erros 403
     * @var string
     */
    protected static $file_403 = _403_ERROR_FILE_;
    /**
     * Arquivo que será exibido para erros 404
     * @var string
     */
    protected static $file_404 = _404_ERROR_FILE_;
    /**
     * Arquivo que será exibido para erros 500
     * @var string
     */
    protected static $file_500 = _500_ERROR_FILE_;
    /**
     * Arquivo que será exibido para outros erros
     * @var string
     */
    protected static $file_default = _DEFAULT_ERROR_FILE_;

    /**
     * Chama a página de erro
     * @param string $code
     */
    public static function callErrorPage($code = null){
        switch ($code){
            case "401":
                require_once(SITE_FOLDER.'/'.self::$file_401);
                break;
            case "403":
                require_once(SITE_FOLDER.'/'.self::$file_403);
                break;
            case "404":
                require_once(SITE_FOLDER.'/'.self::$file_404);
                break;
            case "500":
                require_once(SITE_FOLDER.'/'.self::$file_500);
                break;
            default:
                require_once(SITE_FOLDER.'/'.self::$file_default);
                break;
        }
    }
}
