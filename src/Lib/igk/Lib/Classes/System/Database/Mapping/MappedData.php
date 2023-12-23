<?php
// @author: C.A.D. BONDJE DOUE
// @file: MappedData.php
// @date: 20220819 11:33:43
namespace IGK\System\Database\Mapping;

use IGK\Helper\JSon;
use IGK\Helper\JSonEncodeOption;
use IGK\System\IToArrayResolver;
use IGK\Test\IGKObjectStrictTest;
use IGKObjectStrict;
use JsonSerializable;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Mapping
*/
class MappedData implements JsonSerializable, IToArrayResolver{
    private $m_data = [];
    public function __construct($data){
        foreach($data as $k=>$v){
            $this->$k = $v;
        }
        
    }

    public function jsonSerialize(bool $ignore_null=true, bool $ignore_empty=true): mixed { 
        $opts = new JSonEncodeOption;
        $opts->ignore_null = $ignore_null;
        $opts->ignore_empty = $ignore_empty;
        return JSon::Encode($this->m_data);
    }
    public function to_array():array{
        return $this->m_data;
    }
    public function __set($n, $v){
        // $this->$n = $v;
        $this->m_data[$n] = $v;
    }
    public function __get($n){
        return igk_getv($this->m_data, $n);
    }
    public function serialize(){

    }
}