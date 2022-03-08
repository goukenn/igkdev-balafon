<?php

namespace  IGK\System\Configuration\Controllers;

///<summary>Represente class: IGKToolHost</summary>
/**
* Represente IGKToolHost class
*/
class ToolHost{
    private $_tools;
    ///<summary>Represente __construct function</summary>
    /**
    * Represente __construct function
    */
    public function __construct($tab){
        $this->_tools=$tab;
    }
    ///<summary>Represente getTools function</summary>
    /**
    * Represente getTools function
    */
    public function getTools(){
        return $this->_tools;
    }
    ///<summary>Represente register function</summary>
    ///<param name="ctrl"></param>
    /**
    * Represente register function
    * @param  $ctrl
    */
    public function register($ctrl){
        $this->_tools->setFlag($ctrl->getName(), 1);//$ctrl;
    }
}