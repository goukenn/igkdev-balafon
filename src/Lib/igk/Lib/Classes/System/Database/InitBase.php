<?php
// @author: C.A.D. BONDJE DOUE
// @file: InitBase.php
// @desc: Database initialization
// @date: 20211007 08:31:28
///<summary>init databalse class</summary>
namespace IGK\System\Database;

use IGK\Controllers\BaseController;

///<summary> to initialize database entries</summary>
abstract class InitBase{
    // + | public static function Init(SourceController $controller){
    // + |      override this to init your database
    // + | }

    protected static function InitAuthorisations( string $classname, ?BaseController $owner=null){
        self::_initConstantModel($classname, \IGK\Models\Authorizations::class, $owner, function($v, $cl)use($owner){
            return [
                IGK_FD_NAME => $owner ? $owner::name($v) : $v,
                "clController"=>$cl,
            ];
        });
       
    }
    private static function _initConstantModel(string $classname, $modelclass,  ?BaseController $owner=null, callable $callback=null){
        $cl = null;
        if ($owner)
            $cl = $owner::name(igk_html_uri(get_class($owner)));
        $auths = igk_reflection_get_constants($classname); 
        foreach($auths as $v){
            $tab = $callback ? $callback($v, $cl) : [
                IGK_FD_NAME => $v,
                "clController"=>$cl,
            ];
            $modelclass::insertIfNotExists($tab);
        } 
    }
    protected static function InitGroups( string $classname, ?BaseController $owner=null){
        self::_initConstantModel($classname,\IGK\Models\Groups::class, $owner, function($v, $cl)use($owner){
            return [
                IGK_FD_NAME => $owner ? $owner::name($v) : $v,
                "clController"=>$cl,
            ];
        });      
    }
}