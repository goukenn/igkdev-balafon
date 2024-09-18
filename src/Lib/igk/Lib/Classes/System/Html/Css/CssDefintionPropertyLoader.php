<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssDefintionPropertyLoader.php
// @date: 20240212 09:53:43
namespace IGK\System\Html\Css;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssDefintionPropertyLoader{
    private $m_data = [];
    public function load(array $data){
        $this->m_data = array_merge($this->m_data, $data);
    }
    public function __debugInfo()
    {
        return [];
    }
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