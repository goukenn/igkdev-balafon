<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ToolHost.php
// @date: 20220803 13:48:57
// @desc: 


namespace  IGK\System\Configuration\Controllers;

/**
* Represent IGKToolHost class
*/
class ToolHost{
    private $_tools;
    /**
    * Represent __construct function
    */
    public function __construct($tab){
        $this->_tools=$tab;
    }
    /**
    * Represent getTools function
    */
    public function getTools(){
        return $this->_tools;
    }
    /**
    * Represent register function
    * @param  $ctrl
    */
    public function register($ctrl){
        $this->_tools->setFlag($ctrl->getName(), 1);//$ctrl;
    }
}