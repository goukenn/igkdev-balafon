<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DomNodeBase.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\System\Html\Dom\EnvironmentDomEngineCreator;
use IGK\System\Html\HtmlInitNodeInfo;
use IGKObject;

/**
 * represent dom node.
 * @package IGK\System\Html\Dom
 */
abstract class DomNodeBase extends IGKObject{   
    const CREATOR_PREFIX_KEY = 'dom_creator://'; 
    /**
     * set property used to initialize the node
     * @param string $type 
     * @return mixed 
     */
    protected abstract function setInitNodeTypeInfo(HtmlInitNodeInfo $info);

    /**
     * get init node type info
     * @return null|HtmlInitNodeInfo 
     */
    public abstract function getInitNodeTypeInfo() : ?HtmlInitNodeInfo;

    /**
     * retrieve parent node 
     * @return ?static|mixed 
     */
    public abstract function getParentNode();

    public static function GetCreatorEngine(string $name){
        $env = igk_environment();
        $key = self::CREATOR_PREFIX_KEY.$name;
        if ($g = $env->services->creator){
            return $g->{$key};
        }
    }
    /**
     * 
     * @param string $name 
     * @return void 
     */
    public static function RegisterCreator(string $name, $class_name){
        $env = igk_environment();
        $g = $env->services->dom_node_creator ?? $env->services->dom_node_creator = new EnvironmentDomEngineCreator;
        if (is_subclass_of($class_name, DomCreatorNodeService::class )){
            $o = new $class_name; 
            $key = self::CREATOR_PREFIX_KEY.$name; 
            $g->register($key, $o);
        }
    }
}