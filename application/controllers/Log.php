<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Classe que executa os processos de assinatura do auto atendimento
 */
class Log extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('LogFile');
        $this->load->library('FileManager');
    }
    
    public function index(){
        $excep = ['index.html', 'tracker_nav.txt'];
        
        echo "<h1>Arquivos de Log</h1>";
        $fm = new FileManager(APPPATH.'logs');
        $files = $fm->getFiles();
        if($files && count($files) > 0){
            echo "<ul>";
            foreach($files as $file){
                if(!in_array($file, $excep)){
                    echo "<li>";
                    echo "<a target='_blank' href=".base_url("log/read/".$file).">".$file."</a>";
                    echo " | <a href=".base_url("log/delete/".$file).">Excluir Log</a>";
                    echo "</li>";
                }
            }
            echo "</ul>";
        }
        echo "<p><a target='_blank' href='".base_url('log/sample/')."'>Exemplo de Uso</a></p>";
    }
    
    public function save($file, $content){
        $log = new LogFile();
        if(empty($file)){
            $file = $this->input->post('file');
        }
        if(empty($content)){
            $content = $this->input->post('content');
        }
        var_dump($log->log($content, $file));
    }
    
    public function read($file){
        echo "<h1>{$file}</h1>";
        echo "<p><a href=".base_url("log/delete/".$file).">Excluir Log</a></p>";
        $fm = new FileManager(APPPATH.'logs');
        echo $fm->loadFile($file);
        echo "<p><a href='javascript:window.close()'>Fechar</a></p>";
    }

    public function delete($file){
        $fm = new FileManager(APPPATH.'logs');
        $fm->deleteFile($file);
        redirect(base_url('log'));
    }

    public function sample(){
        echo "<code>
        // Importa a biblioteca - geralmente no construtor da controller/model<br>
        \$this->load->library('LogFile');<br><br>
        // Cria um objeto LogFile<br>
        \$log = new LogFile();<br><br>
        // Grava no log<br>
        \$log->log('conteudo a gravar', 'nomedoarquivo.log');<br><br>
        // O log permite gravar vários blocos, que serão divididos por data no arquivo<br>
        ob_start();<br>
        echo 'isto estará no arquivo de log';<br>
        \$content = ob_get_contents();<br>
        ob_end_clean();<br>
        \$log->log(\$content, 'nomedoarquivo.log');<br>
        </code>";
        echo "<p><a href='javascript:window.close()'>Fechar</a></p>";
    }
    
    /**
     * Liga / Desliga ferramenta de rastreio de navegação
     * ATENÇÃO, ACESSAR APENAS PELO SISTEMA DE CONTA
     * @param int $status 1 para ativar, 0 para desativar, nada para verificar status
     */
    public function tracker_nav_status($status = null){
        $fm = new FileManager(APPPATH.'logs');
        if($status !== null){
            if($status == '1'){
                $fm->saveFile('tracker_nav.txt', '1');
            }else{
                $fm->saveFile('tracker_nav.txt', '0');
            }
        }
        $content = $fm->loadFile('tracker_nav.txt');
        if($content == '1'){
            echo '<p style="color:green">Ferramenta de Rastreio de Navegação Ativada</p>';
        }else{
            echo '<p style="color:red">Ferramenta de Rastreio de Navegação Destivada</p>';
        }
    }
    
    /**
     * Carrega o arquivo que indica se o rastreio de navegação está ativo ou não
     */
    public function tracker_nav(){
        $fm = new FileManager(APPPATH.'logs');
        echo $fm->loadFile('tracker_nav.txt');
    }
}