<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ToolHost.php
// @date: 20220803 13:48:57
// @desc: 


namespace  IGK\System\Configuration\Controllers;

///<summary>Represente class: IGKToolHost</summary>
/**
* Represent IGKToolHost class
*/
class ToolHost{
    private $_tools;
    ///<summary>Represente __construct function</summary>
    /**
    * Represent __construct function
    */
    public function __construct($tab){
        $this->_tools=$tab;
    }
    ///<summary>Represente getTools function</summary>
    /**
    * Represent getTools function
    */
    public function getTools(){
        return $this->_tools;
    }
    ///<summary>Represente register function</summary>
    ///<param name="ctrl"></param>
    /**
    * Represent register function
    * @param  $ctrl
    */
    public function register($ctrl){
        $this->_tools->setFlag($ctrl->getName(), 1);//$ctrl;
    }
}