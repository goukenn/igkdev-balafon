<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitBase.php
// @desc: Database initialization
// @date: 20211007 08:31:28
namespace IGK\System\Database;
use IGK\Controllers\BaseController;

/**
* Init base.
* @package IGK\System\Database
*/
abstract class InitBase{
    /**
    * Constant: init method.
    * @var mixed
    */
    const INIT_METHOD = 'Init';
    // + | public static function Init(SourceController $controller){
    // + |      override this to init your database
    // + | }
    /**
     * Initialise authorisation records from the constants of the given class.
     *
     * @param string $classname The class whose constants supply authorisation names.
     * @param ?BaseController $owner Optional owning controller.
     * @return void
     */
    protected static function InitAuthorisations( string $classname, ?BaseController $owner=null){
        self::_initConstantModel($classname, \IGK\Models\Authorizations::class, $owner, function($v, $cl)use($owner){
            return [
                IGK_FD_NAME => $owner ? $owner::name($v) : $v,
                "clController"=>$cl,
            ];
        });
    }
    /**
     * Insert model rows from constants of a class if they do not already exist.
     *
     * @param string $classname The class whose constants provide values.
     * @param mixed $modelclass The model class used to insert records.
     * @param ?BaseController $owner Optional owning controller.
     * @param ?callable $callback Optional callback to build the row data.
     * @return void
     */
    protected static function _initConstantModel(string $classname, $modelclass,  ?BaseController $owner=null, ?callable $callback=null){
        $cl = null;
        if ($owner)
            $cl = $owner::name(igk_uri(get_class($owner)));
        $auths = igk_reflection_get_constants($classname); 
        foreach($auths as $v){
            $tab = $callback ? $callback($v, $cl) : [
                IGK_FD_NAME => $v,
                "clController"=>$cl,
            ];
            $modelclass::insertIfNotExists($tab);
        } 
    }
    /**
     * Initialise group records from the constants of the given class.
     *
     * @param string $classname The class whose constants supply group names.
     * @param ?BaseController $owner Optional owning controller.
     * @return void
     */
    protected static function InitGroups( string $classname, ?BaseController $owner=null){
        self::_initConstantModel($classname,\IGK\Models\Groups::class, $owner, function($v, $cl)use($owner){
            return [
                IGK_FD_NAME => $owner ? $owner::name($v) : $v,
                "clController"=>$cl,
            ];
        });      
    }
}