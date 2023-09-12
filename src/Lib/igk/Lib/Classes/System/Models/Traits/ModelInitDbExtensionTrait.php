<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelInitDbExtensionTrait.php
// @date: 20230831 17:02:41
namespace IGK\System\Models\Traits;

use IGK\Models\ModelBase;

///<summary></summary>
/**
* 
* @package IGK\System\Models\Traits
*/
trait ModelInitDbExtensionTrait{
    /**
     * extension method init entries from json data 
     * @param ModelBase $model 
     * @param string $path path to json file 
     * @return bool|int number of entries 
     */
    public static function initEntriesFromJsonDataFile(ModelBase $model, string $path, $extraData=null){
        $count = 0;
        if (file_exists ($file = $path) || file_exists ($file = $model->getController()->getDataDir().'/Database/'.$path)){
            if ($data  = json_decode(file_get_contents($file))){
                foreach($data as $e){
                    $r = (array)$e;
                    if (is_array($extraData)){
                        $r = array_merge($r, $extraData);
                    }
                    if($model->insertIfNotExists($r)){
                        $count++;
                    }
                }
                return $count;
            }
        }
        return false;
    }
}