<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ControllerAutoLoad.php
// @date: 20230131 12:50:42
// @desc: 
namespace IGK\Controllers;

/**
 * used to load - after cache complete 
 * @package 
 */
class ControllerAutoLoad{
    /**
    * Property: controller.
    * @var mixed
    */
    private $m_controller;
    /**
    * Property: autoloadfile.
    * @var mixed
    */
    private $m_autoloadfile;
    /**
    * Name of hookname.
    * @var mixed
    */
    private $m_hookname;
    /**
    * .ctr
    * @param BaseController $controller
    * @param string $file
    * @param null|mixed $hookname
    */
    public function __construct(BaseController $controller, string $file, $hookname=null)
    {
        $this->m_controller = $controller;
        $this->m_autoloadfile  = $file;
        $this->m_hookname = $hookname;
    }
    /**
    * Called when an object is used as a function.
    */
    public function __invoke(){
        $ctrl = $this->m_controller;
        require_once($this->m_autoloadfile);
        if ($this->m_hookname){
            igk_unreg_hook($this->m_hookname, $this);
        }
    }
}