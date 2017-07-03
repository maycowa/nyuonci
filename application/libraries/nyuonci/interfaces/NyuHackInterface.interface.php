<?php
/**
 * 2016 Nyu Framework
 */
/**
 * Interface para Hack de carregamento do Nyu.
 * Para utilizar, criar uma classe que irá implementar os métodos descritos 
 * aqui e inserir o nome na configuração $nyu__config['hack']
 * @since 6.0
 * @version 1.0
 */
interface NyuHackInterface{
    /**
     * Método que será carregado antes da controller ser carregada.
     */
    public function beforeLoadController();
    
    /**
     * Método que será carregado após carregar as bibliotecas padrão do sistema
     * Geralmente utilizado para carregar outras bibliotecas necessárias para
     * o sistema.
     * Exemplo:
     * <code>
     * public function afterLoadLibs(){
     *     $nyuLibManager = new NyuLibManager();
     *     $nyuLibManager->addPath('myLibrary');
     *     $nyuLibManager->autoloadLibs();
     * }
     * </code>
     */
    public function afterLoadLibs();
}