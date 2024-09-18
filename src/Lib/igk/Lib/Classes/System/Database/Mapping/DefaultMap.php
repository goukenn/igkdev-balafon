<?php
// @author: C.A.D. BONDJE DOUE
// @file: DefaultMap.php
// @date: 20220819 11:28:51
namespace IGK\System\Database\Mapping;

use IGK\Helper\SysUtils;
use IGK\Models\ModelBase;

///<summary></summary>
/**
* represent default mapping data
* @package IGK\System\Database\Mapping
*/
class DefaultMap{
    /**
     * 
     * @param array|object $map 
     * @param mixed $data 
     * @return array<array-key, \IGK\System\Database\Mapping\MappedData> 
     */
    public static function MapModelData($map, $data){
        return array_map(function(ModelBase $row)use($map){
            $out = [];
            $row_keys = $row->getRowKeys();
            foreach($row_keys as $k){
                $v = igk_getv($map, $k, $k);
                $out[$v] = $row[$k];
            }
            return new MappedData((object)$out);
        }, $data);
    }
    /**
     * 
     * @param mixed $data 
     * @return null|array 
     */
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