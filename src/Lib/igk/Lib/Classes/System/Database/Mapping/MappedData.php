<?php
// @author: C.A.D. BONDJE DOUE
// @file: MappedData.php
// @date: 20220819 11:33:43
namespace IGK\System\Database\Mapping;

use IGK\Test\IGKObjectStrictTest;
use IGKObjectStrict;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Mapping
*/
class MappedData{
    public function __construct($data){
        foreach($data as $k=>$v){
            $this->$k = $v;
        }
    }
    public function __set($n, $v){
        $this->$n = $v;
    }
}