<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Classe do Nyu para gerenciar arquivos e pastas
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuCore
 * @version 1.8.1
 */
class NyuFileManager {
    
    /**
     * Caminho do diretório que está sendo lido
     * @var string
     */
    protected $path;
    /**
     * Arquivos que estão dentro do diretório
     * @var array
     */
    protected $files;
    /**
     * Pastas que estão dentro do diretório
     * @var array
     */
    protected $folders;

    /**
     * Construtor da classe NyuFileManager
     * @param string $path Caminho do diretório que será lido
     * @param boolean $load (Opcional) Indica se a pasta será lida logo que o 
     * objeto for criado, carregando os dados de seus arquivos e pastas nos 
     * atributos $files e $folders
     */
    public function __construct($path=null, $load=true) {
        if ($path) {
            $this->setPath($path);
            if ($load) {
                $this->loadPath();
            }
        }
    }

    /**
     * Altera o caminho da pasta a ser lida. Após sua alteração, é necessário 
     * recarregar o conteúdo da pasta utilizando o método loadPath()
     * @param string $path Caminho do diretório que será lido
     */
    public function setPath($path) {
        if (substr($path, -1, 1) != '/') {
            $path .= "/";
        }
        $this->path = $path;
    }
    
    /**
     * Retorna o caminho do diretório que está sendo lido
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Seta o array $files
     * @param array $files
     */
    public function setFiles($files) {
        $this->files = $files;
    }

    /**
     * Retorna o array $files com os arquivos do diretório
     * @param string $extension (Desde 4.0) Retorna apenas arquivos da extensão informada
     * @param string $returFullPath (Desde 4.0) Retorna o caminho completo dos arquivos
     * @return array
     */
    public function getFiles($extension=null, $returFullPath = false) {
	if($extension){
            $files = array();
            if($this->files){
                foreach($this->files as $file){
                    if(strtoupper(substr($file, strlen($file) - strlen($extension))) === strtoupper($extension)){
                        $files[] = $file;
                    }
                }
            }
			return $files;
        }
        if($returFullPath && $this->files){
            $files = array();
            foreach($this->files as $ind => $file){
                $files[$ind] = realpath($this->path.$file);
            }
			return $files;
        }
        return $this->files;
    }

    /**
     * Seta o array $folders
     * @param array $folders
     */
    public function setFolders($folders) {
        $this->folders = $folders;
    }

    /**
     * Retorna o array $folders com as pastas do diretório
     * @return array
     */
    public function getFolders() {
        return $this->folders;
    }

    /**
     * Carrega/Recarrega o conteúdo de um diretório
     */
    public function loadPath() {
        $ponteiro = opendir($this->path);
        while ($nome_itens = readdir($ponteiro)) {
            $itens[] = $nome_itens;
        }
        sort($itens);
        unset($this->files);// Limpa os arquivos do array
        unset($this->folders);// Limpa as pastas do array
        foreach ($itens as $listar) {
            if ($listar != ".") {
                if (file_exists($this->path . $listar) && is_file($this->path . $listar)) {
                    $this->files[] = $listar;
                } else {
                    $this->folders[] = $listar;
                }
            }
        }
    }

    /**
     * Verifica se um arquivo existe no diretório
     * @param string $filename Nome do arquivo
     * @return boolean
     */
    public function fileExists($filename) {
        return file_exists($this->path . $filename);
    }

    /**
     * Carrega o conteúdo de um arquivo e retorna
     * @param string $filename Arquivo a ser feita a leitura
     * @return boolean|string
     */
    public function loadFile($filename) {
        if (file_exists($this->path . $filename)) {
            $file = fopen($this->path . $filename,"r");
            $s = fread($file,filesize($this->path . $filename));
            fclose($file);
            return $s;
        } else {
            return false;
        }
    }

    /**
     * Cria/Escreve em um arquivo no diretório
     * @param string $filename Nome do arquivo
     * @param string $content Conteúdo a ser escrito no arquivo
     * @param string $mode Modo de acesso ao arquivo
     * @return boolean
     */
    public function saveFile($filename, $content, $mode = "w") {
        $f = fopen($this->path . $filename, $mode);
        if (flock($f, LOCK_EX)) { // Bloqueia o arquivo para evitar acesso simultaneo
            fwrite($f, $content);
            flock($f, LOCK_UN); // libera o lock
        } else {
            return false;
        }
        return fclose($f);
    }

    /**
     * Apaga um arquivo do diretório
     * @param string $filename Nome do arquivo a ser apagado
     * @return boolean
     */
    public function deleteFile($filename) {
        if (file_exists($this->path . $filename)) {
            return unlink($this->path . $filename);
        } else {
            return false;
        }
    }

    /**
     * Renomeia um arquivo no diretório (depreciado, utilizar o método rename())
     * @param string $filename Nome do arquivo
     * @param string $newFilename Novo nome do arquivo
     * @return boolean
     * @deprecated
     */
    public function renameFile($filename, $newFilename) {
        return $this->rename($filename, $newFilename);
    }
    
    /**
     * Renomeia um arquivo ou pasta no diretório
     * @param string $filename Nome do arquivo ou pasta
     * @param string $newFilename Novo nome do arquivo ou pasta
     * @return boolean
     */
    public function rename($filename, $newFilename){
        if (file_exists($this->path . $filename)) {
            return rename($this->path . $filename, $this->path . $newFilename);
        } else {
            return false;
        }
    }
    
    /**
     * Move um arquivo para outro diretório
     * @param string $filename Nome do arquivo
     * @param string $newPath Novo endereço do arquivo - não há necessidade de 
     * ser no mesmo diretório
     * @return boolean
     */
    public function moveFile($filename, $newPath) {
        if (substr($newPath, -1, 1) != '/') {
            $newPath .= "/";
        }
        if (file_exists($this->path . $filename)) {
            rename($this->path . $filename, $newPath . $filename);
        } else {
            return false;
        }
    }
    
    /**
     * Busca um ou mais arquivos na pasta, de acordo com o padrão informado
     * @param string $pattern Nome da pasta a ser criada
     * @param int $flags (Opcional) Flags para a busca, de acordo com a função 
     * glob() do PHP:
     * GLOB_MARK - Acrescenta uma barra a cada item retornado
     * GLOB_NOSORT - Retorna os arquivos conforme eles aparecem no diretório 
     * (sem ordenação)
     * GLOB_NOCHECK - Retorna o padrão da busca se nenhuma combinação de 
     * arquivo for encontrada
     * GLOB_NOESCAPE - Barras invertidas não escapam metacaracteres.
     * GLOB_BRACE - Expande {a,b,c} para combinar com 'a', 'b' ou 'c'
     * GLOB_ONLYDIR - Retorna apenas diretórios que combinem com o padrão
     * GLOB_ERR - Pára em erros de leitura (como diretórios que não podem ser 
     * lidos), por padrão os erros são ignorados.
     * @since 4.0
     * @return boolean
     */
    public function findFiles($pattern, $flags=null) {
        if($flags){
            return glob($this->path . $pattern, $flags);    
        }else{
            return glob($this->path . $pattern);
        }
    }

    /**
     * Cria uma pasta no diretório
     * @param string $foldername Nome da pasta a ser criada
     * @param int $mode (Opcional) Modo (permissões) de acesso à pasta 
     * @return boolean
     */
    public function createFolder($foldername, $mode = 0777) {
        return mkdir($this->path . $foldername, $mode);
    }
    
    /**
     * Altera as permissões de acesso à uma pasta
     * @param string $foldername Nome da pasta a ser modificada
     * @param int $mode Modo (permissões) de acesso à pasta 
     * @return boolean
     */
    public function changePermissionFolder($foldername, $mode){
        return chmod($this->path . $foldername, $mode);
    }

    /**
     * Método para limpar e/ou excluir um diretório
     * @param string $foldername Diretorio a excluir
     * @param boolean $empty Indica que o método deve apenas limpar o diretório,
     * sem apagá-lo 
     * @return boolean
     */
    public function deleteFolder($foldername, $empty = false) {
        $foldername = $this->path . $foldername;
        
        if (substr($foldername, -1) == "/") {
            $foldername = substr($foldername, 0, -1);
        }

        if (!file_exists($foldername) || !is_dir($foldername)) {
            return false;
        } elseif (!is_readable($foldername)) {
            return false;
        } else {
            $directoryHandle = opendir($foldername);

            while ($contents = readdir($directoryHandle)) {
                if ($contents != '.' && $contents != '..') {
                    $path = $foldername . "/" . $contents;

                    if (is_dir($path)) {
                        $path = str_replace($this->path, "", $path);
                        $this->deleteFolder($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            closedir($directoryHandle);

            if ($empty == false) {
                if (!rmdir($foldername)) {
                    return false;
                }
            }

            return true;
        }
    }

    /**
     * Carrega um arquivo JSON e já retorna os dados como array
     * @param string $filename Nome do arquivo a carregar
     * @return array
     */
    public function loadJsonFile($filename) {
        $s = $this->loadFile($filename);
        return \NyuFormat::jsonFormat($s, "out");
    }

    /**
     * Salva um conteúdo em um arquivo JSON, convertendo o conteúdo antes de 
     * gravar, se for um array
     * @param string $filename Nome do arquivo a ser gravado
     * @param array|string $content Conteúdo a ser gravado
     * @return boolean
     */
    public function saveJsonFile($filename, $content) {
        $f = fopen($this->path . $filename, 'w');
        fwrite($f, \NyuFormat::jsonFormat($content, "in"));
        return fclose($f);
    }

    /**
     * Lê o conteúdo de um arquivo CSV do diretório e retorna em formato array
     * @param string $filename Nome do arquivo
     * @return array|boolean
     */
    public function loadCsvFile($filename){
        if (file_exists($this->path . $filename)) {
            $f = fopen($this->path . $filename,'r');
            while ( ($data = fgetcsv($f) ) !== FALSE ) {
                $a[] = $data;
            }
        } else {
            return false;
        }
        fclose($f);
        return $a;
    }
    
    /**
     * Cria uma função de autoload a partir da pasta informada
     * A pasta informada deve estar dentro da pasta informada no atributo
     * $path do objeto atual.
     * @param string $path Caminho da pasta a carregar as classes
     */
    public function autoloadFolder($path){
        $path = $this->path . $path;
        if (substr($path, -1, 1) != '/') {
            $path .= "/";
        }
        $func = create_function('$a','$path = "'.$path.'";'
                . 'if(file_exists($path.str_replace("\\\","/",$a).".class.php")){'
                    . 'require_once($path.str_replace("\\\","/",$a).".class.php");'
                . '}'
                . 'elseif(file_exists($path.str_replace("\\\","/",$a).".trait.php")){'
                    . 'require_once($path.str_replace("\\\","/",$a).".trait.php");'
                . '}'
                . 'elseif(file_exists($path.str_replace("\\\","/",$a).".interface.php")){'
                    . 'require_once($path.str_replace("\\\","/",$a).".interface.php");'
                . '}'
                );
        spl_autoload_register($func);
    }

    /**
     * Método utilizado para gerar csv a partir de um array e fazer download do arquivo
     * @param array $input_array array a processar
     * @param string $output_file_name nome do arquivo de destino
     * @param string $delimiter delimitador das colunas no arquivo csv
     */
    public static function downloadCsvArray($input_array, $output_file_name, $delimiter){
        /** open raw memory as file, no need for temp files */
        $temp_memory = fopen('php://memory', 'w');
        /** loop through array */
        foreach ($input_array as $line) {
            if(is_object($line)){
                $line = get_object_vars($line);
            }
            /** default php csv handler **/
            fputcsv($temp_memory, $line, $delimiter);
        }
        /** rewrind the "file" with the csv lines **/
        fseek($temp_memory, 0);
        /** modify header to be downloadable csv file **/
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $output_file_name . '";');
        /** Send file to browser for download */
        fpassthru($temp_memory);
    }

}