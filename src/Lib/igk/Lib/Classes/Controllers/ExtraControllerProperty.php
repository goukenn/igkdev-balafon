<?php
// @file: ExtraControllerProperty.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Controllers;
use IGKObject;
/**
 * represent an extra property that will be used for custom controller's type configuration
 * @package IGK\Controllers
 */
final class ExtraControllerProperty extends IGKObject{

    /**
    * Properties: default value, type, values.
    * @var mixed
    */
    private $m_DefaultValue, $m_Type, $m_Values;
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

    /**
    * get string presentation.
    */
    public function __toString(){
        return __CLASS__; 
    }

    /**
    * Getcl default value.
    */
    public function getclDefaultValue(){
        return $this->m_DefaultValue;
    }

    /**
    * Getcl type.
    */
    public function getclType(){
        return $this->m_Type;
    }

    /**
    * Getcl values.
    */
    public function getclValues(){
        return $this->m_Values;
    }
}