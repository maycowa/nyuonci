<?php
/**
 * Controller da Administração antiga do Nyu
 * @author Maycow Alexandre Antunes <maycow@maycow.com.br>
 * @package NyuAdmin
 * @deprecated
 */
class NyuAdminController extends NyuController{
    protected $template;
    
    public function __construct() {
        parent::__construct();
        $this->template = new NyuTemplate(array("cache" => false, "template_dir" => SITE_FOLDER.'/nyu/nyuadmin/templates'));
    }
    
    public function indexAction() {
        $this->template->addVar("active_page", "adm");
        $this->template->renderTemplate("admin.twig");
    }
    
    public function configAction(){
        $this->template->renderTemplate("config.twig");
    }
    
    public function modulesAction(){
        $this->template->renderTemplate("modules.twig");
    }
    
    public function librariesAction(){
        $this->template->renderTemplate("libraries.twig");
    }
    
    public function usersAction(){
        $this->template->renderTemplate("users.twig");
    }
    
    public function aboutAdmAction(){
        $this->template->renderTemplate("aboutadm.twig");
    }
    
    public function aboutAction(){
        $this->template->renderTemplate("about.twig");
    }
    
    public function editUserAction(){
        $this->template->renderTemplate("edituser.twig");
    }
    
    public function logoffAction(){
        $this->redirect(SITE_URL);
    }
}