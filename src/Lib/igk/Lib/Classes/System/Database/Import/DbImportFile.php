<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbImportFile.php
// @date: 20240918 16:35:16
namespace IGK\System\Database\Import;
use IGK\Controllers\BaseController;
use IGK\Models\ModelBase;
use IGKCSVDataAdapter;
use IGKException;
/**
* 
* @package IGK\System\Database\Import
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Database\Import
*/
class DbImportFile{
    /**
    * Constant: handle method prefix.
    * @var mixed
    */
    const HandleMethodPrefix = '_Handle';
    /**
    * Constant: support types.
    * @var mixed
    */
    const SUPPORT_TYPES= 'json|csv';
    /**
    * auto generate doc.
    * @param string $file
    * @return void
    */
    public static function Import(ModelBase $model, string $file, ?string $type=null, ?bool $autoregister=null, $entry = null){
        $ext = null;
        if (!is_null($type) && in_array($type, explode('|', self::SUPPORT_TYPES))){
            $ext = $type;
        }
        $ext = igk_io_path_ext($file);
        // json 
        if (method_exists(static::class, $fc = self::HandleMethodPrefix.ucfirst(strtolower($ext)))){
            return call_user_func_array([static::class, $fc], [$model, $file, $autoregister, $entry]);
        }
        return self::_HandleJson($model, $file, $autoregister);
    }
    /**
     * handle json db imports
     * @param ModelBase $model 
     * @param string $file 
     * @param null|bool $autoregister 
     * @return true|void 
     * @throws IGKException 
     */
    protected static function _HandleJson(ModelBase $model, string $file, ?bool $autoregister, ?string $entry=null){
        if ($data = json_decode(file_get_contents($file))){
            if ($entry){
                $data = igk_conf_get($data, $entry);
            }
            if (!is_array($data)){ 
                $data = $data ? [$data] : []; 
            }
            $mapping = DbModelImporterMap::CreateFrom($model);
            $mapping->autoregister = $autoregister===true;
            array_map($mapping, $data);
            return true;
        }
    }
    /**
     * s
     */
    protected static function _HandleCsv(ModelBase $model, string $file){
        $data = [];
        $mapping = DbModelImporterMap::CreateFrom($model);
        array_map($mapping, $data);
        return true; 
    }
}