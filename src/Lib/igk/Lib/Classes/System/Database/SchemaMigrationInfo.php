<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SchemaMigrationInfo.php
// @date: 20220804 08:20:25
// @desc: 
namespace IGK\System\Database;
use ArrayAccess;
use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\Helper\Activator;
use IGK\Models\ModelBase;
use IGK\System\Models\IModelDefinitionInfo;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
use IGKException;
/**
 * schema migration info
 * @package IGK\System\Database
 */
class SchemaMigrationInfo implements ArrayAccess, IModelDefinitionInfo
{
    use ArrayAccessSelfTrait;
    /**
    * Map of def table name.
    * @var mixed
    */
    var $defTableName;
    /**
    * Property: column info.
    * @var mixed
    */
    var $columnInfo;
    /**
    * auto generate doc.
    * @var mixed
    */
    var $controller;
    /**
    * Property: description.
    * @var mixed
    */
    var $description;
    /**
    * Property: entries.
    * @var mixed
    */
    var $entries;
    /**
    * Map of table row reference.
    * @var mixed
    */
    var $tableRowReference;
    /**
    * Property: model class.
    * @var mixed
    */
    var $modelClass;
    /**
    * Map of table name.
    * @var mixed
    */
    var $tableName;
    /**
    * Property: definition resolver.
    * @var mixed
    */
    var $definitionResolver;
    /**
     * display column property or expression that will be used for display.
     * - in case of expression use $columnName as placeholder.
     * @var ?string
     */
    var $display;
    /**
     * constant used
     * @param mixed $n 
     * @return ?bool
     */
    // var $constant;
    /**
    * auto generate doc.
    * @var ?array<SchemaForeignConstraintInfo>
    */
    var $foreignConstraint;
    /**
     * configured prefix
     * @var ?string
     */
    var $prefix;
    /**
     * confiugred indexed
     * @param mixed $n 
     * @return ?array 
     */
    var $indexes;
    /**
    * Access offset get.
    * @param mixed $n
    */
    public function _access_OffsetGet($n)
    {
        if (property_exists($this, $n)) {
            return $this->$n;
        }
    }
    /**
    * .ctr
    */
    public function __construct() {}
    /**
     * create from cache info
     * @param mixed $d 
     * @param mixed $gctrl 
     * @return mixed 
     * @throws IGKException 
     */
    public static function CreateFromCacheInfo($d, $gctrl)
    {
        return Activator::CreateNewInstance(static::class,  [
            'columnInfo' => array_map(function ($a) {
                return $a ? Activator::CreateNewInstance(DbColumnInfo::class, $a) : null;
            }, isset($d->columnInfo) ? (array)$d->columnInfo : []),
            'description' => igk_getv($d, 'description'),
            'defTableName' => igk_getv($d, 'defTableName'),
            'controller' => $gctrl,
            'definitionResolver' => null,
            'tableName' => igk_getv($d->controller, 'tableName'),
            'prefix' => igk_getv($d->controller, 'prefix')
        ]);
    }
    /**
     * return model instance
     * @return ?ModelBase 
     */
    public function model()
    {
        /**
        * auto generate doc.
        * @var BaseController $ctrl
        */
        $ctrl = $this->controller;
        $cl = $this->modelClass;
        $ctrl::register_autoload();
        return $cl ? $ctrl::model($cl) : null;
    }
}