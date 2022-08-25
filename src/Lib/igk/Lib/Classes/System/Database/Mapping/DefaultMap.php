<?php
// @author: C.A.D. BONDJE DOUE
// @file: DefaultMap.php
// @date: 20220819 11:28:51
namespace IGK\System\Database\Mapping;

use IGK\Helper\SysUtils;

///<summary></summary>
/**
* represent default mapping data
* @package IGK\System\Database\Mapping
*/
class DefaultMap{
    public function map($data): ?array {
        if (is_null($data)){
            return null;
        }
        if (!is_array($data)){
            $data = [$data];
        }
        return array_map(function($c){       
            return $this->mapCoreDataBase(SysUtils::ToArray($c));
        }, $data );
    }
    /**
     * map core lib data 
     * @param mixed $c 
     * @param string $prefix 
     * @return MappedData 
     */
    public function mapCoreDataBase($c, $prefix=IGK_FIELD_PREFIX): MappedData{
        $out = [];
        $ln = strlen($prefix);
        foreach($c as $k=>$v){
            $n = $k;
            if (strpos($k, $prefix)===0){
                $n = lcfirst(substr($k,  $ln));
            }
            $out[$n] = $v;
        } 
        return new MappedData((object)$out);
    }
}