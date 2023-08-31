<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbMapper.php
// @date: 20230725 23:26:07
namespace IGK\Mapping;


///<summary></summary>
/**
* mapper use to construct fields
* @package IGK\Mapping
*/
class DbMapper{
    private $m_data;
    public function __construct($data){
        $this->m_data = $data;
    }
    /**
     * 
     * @param mixed $key_list 
     * @return array<array-key, object> 
     */
    public function map($key_list){
        return array_map(function($o)use($key_list){
            $m = [];
            foreach($key_list as $k=>$v){
                $m[$v]= igk_getv($o, $k);
            }
            return (object)$m;
        }, $this->m_data);
    }
}