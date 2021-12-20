<?php
// @file: ExtraControllerProperty.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGKObject;

/**
 * represent an extra property that will be used for custom controller's type configuration
 * @package IGK\Controllers
 */
final class ExtraControllerProperty extends IGKObject{
    private $m_DefaultValue, $m_Type, $m_Values;
    ///<summary>.ctr</summary>
    ///<param name="type">type of the parameter. select|text|textarea|bool|radio</param>
    ///<param name="def">array of value in case of "select" or default value</param>
    ///<param name="def1">default value in case of type "select" </param>
    /**
     * 
     * @param mixed $type 
     * @param mixed $def 
     * @param mixed $def1 
     * @return void 
     */
    public function __construct($type, $def, $def1=null){
        $this->m_Type=$type;
        if(strtolower($type) == "select"){
            $this->m_Values=$def;
            $this->m_DefaultValue=$def1;
        }
        else{
            $this->m_DefaultValue=$def;
            $this->m_Values=null;
        }
    }
    ///<summary>display value</summary>
    public function __toString(){
        return __CLASS__; 
    }
    ///<summary></summary>
    public function getclDefaultValue(){
        return $this->m_DefaultValue;
    }
    ///<summary></summary>
    public function getclType(){
        return $this->m_Type;
    }
    ///<summary></summary>
    public function getclValues(){
        return $this->m_Values;
    }
}
