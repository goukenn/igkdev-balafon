<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbImportFile.php
// @date: 20240918 16:35:16
namespace IGK\System\Database\Import;

use IGK\Controllers\BaseController;
use IGK\Models\ModelBase;
use IGKCSVDataAdapter;

///<summary></summary>
/**
* 
* @package IGK\System\Database\Import
* @author C.A.D. BONDJE DOUE
*/
class DbImportFile{
    const HandleMethodPrefix = '_Handle';
    const SUPPORT_TYPES= 'json|csv';
    /**
     * 
     * @param ModelBase $model 
     * @param string $file 
     * @return void 
     */
    public static function Import(ModelBase $model, string $file, ?string $type=null, ?bool $autoregister=null){
        $ext = null;
        if (!is_null($type) && in_array($type, explode('|', self::SUPPORT_TYPES))){
            $ext = $type;
        }
        $ext = igk_io_path_ext($file);
        // json 
        if (method_exists(static::class, $fc = self::HandleMethodPrefix.ucfirst(strtolower($ext)))){
            return call_user_func_array([static::class, $fc], [$model, $file, $autoregister]);
        }
        return self::_HandleJson($model, $file, $autoregister);
    }
    protected static function _HandleJson(ModelBase $model, string $file, ?bool $autoregister){
        if ($data = json_decode(file_get_contents($file))){
            $mapping = DbModelImporterMap::CreateFrom($model);
            $mapping->autoregister = $autoregister===true;
            array_map($mapping, $data);
            return true;
        }
    }
    protected static function _HandleCsv(ModelBase $model, string $file){
        $data = [];
        $mapping = DbModelImporterMap::CreateFrom($model);
        array_map($mapping, $data);
        return true; 
    }
}