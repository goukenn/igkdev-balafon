<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssDefintionPropertyLoader.php
// @date: 20240212 09:53:43
namespace IGK\System\Html\Css;
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssDefintionPropertyLoader{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_data = [];

    /**
    * auto generate doc.
    * @param array $data
    */
    public function load(array $data){
        $this->m_data = array_merge($this->m_data, $data);
    }

    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return [];
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        $s = '';
        $t = array_keys($this->m_data);
        sort($t);
        $s = implode(';', array_map(function($k){
            return sprintf("%s:%s",$k, $this->m_data[$k]);
        }, $t)).';';
        return $s; 
    }
}