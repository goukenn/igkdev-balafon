<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationEncoder.php
// @date: 20220830 11:35:12
// @desc: 
namespace IGK\System\IO\Configuration;

/**
* auto generate doc.
* @package IGK\System\IO\Configuration
*/
class ConfigurationEncoder{
    /**
    * Property: separator.
    * @var mixed
    */
    var $separator = '=';
    /**
    * Property: delimiter.
    * @var mixed
    */
    var $delimiter = ',';
    /**
     * encode data
     * @param mixed $data 
     * @return string 
     */
    public function encode($data){
        $sb = '';
        $sep = '';
        foreach($data as $k=>$v){
            $sb.=$sep;
            $sb.=$k;
            $sb.=$this->separator;
            $sb.= is_numeric($v)? $v : $v;
            $sep = $this->delimiter;
        } 
        return $sb;
    }
}