<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationEncoder.php
// @date: 20220830 11:35:12
// @desc: 
namespace IGK\System\IO\Configuration;
/**
 * 
 * @package IGK\System\IO\Configuration
 */
class ConfigurationEncoder{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $separator = '=';

    /**
    * auto generate doc.
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