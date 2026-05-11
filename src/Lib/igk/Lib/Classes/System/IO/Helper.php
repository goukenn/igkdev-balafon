<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Helper.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\IO;
use IGK\Controllers\BaseController;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\Controllers\SystemController as IGKSystemController;
use ReflectionException;

/**
 * IO Helper class 
 * @package IGK\System\IO
 */
final class Helper{
    /**
    * Constant: table property.
    * @var mixed
    */
    const TABLE_PROPERTY  = 'tables';
    /**
    * auto generate doc.
    * @param BaseController $ctrl
    * @param callable $callback
    * @param bool $force
    * @return array
    */
    public static function GenerateModel(BaseController $ctrl, callable $callback, $force=false){
        $manifest = [];
        $file = $ctrl->getDataSchemaFile();
        if (!igk_io_file_exists($file)){
            die("schema file not found.");
        }
        $schema = igk_db_load_data_schemas($file, $ctrl, true);
        $tables = igk_getv($schema, self::TABLE_PROPERTY); 
        foreach($tables as $t=>$info){
            $callback($ctrl, $t, $info, $manifest, $force);
        }
        return $manifest;
    }
    /**
    * auto generate doc.
    * @param string $file
    * @param callable $callback
    * @param bool $force
    * @return array
    */
    public static function GenerateModelFromFile(string $file, callable $callback, $force=false){
        $manifest = [];
        $ctrl = igk_getctrl(IGKSystemController::class);
        $schema = igk_db_load_data_schemas($file, null, true);
        $tables = igk_getv($schema, self::TABLE_PROPERTY); 
        foreach($tables as $t=>$info){
            $callback($ctrl, $t, $info, $manifest, $force);
        }
        return $manifest;
    }
}