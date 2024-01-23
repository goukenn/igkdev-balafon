<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLoadingContext.php
// @date: 20220707 01:38:40
// @desc: html loading context
//      - component conatiner can bind context when loading special items
//      - surround object callback is use to ensure loading on callback context

namespace IGK\System\Html;

use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlItemBase;
use IGKEvents;

/**
 * html loading context 
 * @package 
 */
class HtmlLoadingContext{
    /**
     * context name
     * @var mixed
     */
    var $name;

    /**
     * enable or not loading with setContent
     * @var bool
     */
    var $load_content;

    /**
     * enable or not loading expression
     * @var bool
     */
    var $load_expression;

    /**
     * source node
     * @var mixed
     */
    var $node;

    /**
     * ignore tags on rendering|creation context
     * @var ?array
     */
    var $ignore_tags;

    private static $sm_context_loading;
    
    /**
     * get current loading context
     * @return ?static
     */
    public static function GetCurrentContext(){
        $g= self::$sm_context_loading ? igk_getv(self::$sm_context_loading, 0) : null;// [] igk_environment()->peek(self::class);

        return $g ? $g[0] : null;
    }
    private static function  & _RefLoading(){
        $sm = & self::$sm_context_loading;
        if (is_null($sm)){
            $sm = [];
        }
        return $sm;
    }
    /**
     * push loading context
     * @param HtmlLoadingContext $p 
     * @return void 
     * @throws EnvironmentArrayException 
     */
    public static function PushContext(HtmlLoadingContext $context, HtmlItemBase $parent){
        $sm_context_loading = & self::_RefLoading();
        // $def = [get_class($context), $parent];
        $def = [$context, $parent];
        
        if ((count($sm_context_loading)<=0) || 
            !(($sm_context_loading[0]== $def) || ($sm_context_loading[0][0]== $def[0]))){ 
            self::_LoadContextAndInitialize($sm_context_loading, $def); 
            igk_hook(IGKEvents::HOOK_HTML_LOADING_CONTEXT_REGISTER, [$context, $parent]);
        }
    }
    private static function _LoadContextAndInitialize(& $sm_context_loading, $def){
        self::_LoadContext($sm_context_loading, $def);
        $def[0]->initialize();  
    }
    private static function _LoadContext(& $sm_context_loading, $def){
        array_unshift($sm_context_loading, $def);
        // igk_environment()->push(self::class, $def); 
    }
    /**
     * pop loading context
     * @param mixed|bool|object $uninitialize 
     * @return void 
     */
    public static function PopContext($uninitialize=false){ 
        if (!self::$sm_context_loading){
            return;
        }
        $c = self::$sm_context_loading[0];
        $v_pop = is_bool($uninitialize); // false;
        if (($uninitialize instanceof static) && ($c[0] == $uninitialize)){ 
            $v_pop = true;  
        }
        else if (($uninitialize instanceof HtmlItemBase) && ($c[1]== $uninitialize)){
            $v_pop = true; 
        }
        else if (is_array($uninitialize) && ($uninitialize==$c)){
            
            $v_pop = true;
            $uninitialize = true;
        }
        if ($v_pop){

            $c = array_shift(self::$sm_context_loading);
            if ($c && is_bool($uninitialize) && $uninitialize){
                $c[0]->uninitialize();
            }
            return true;
        }
        return false;
    }
    /**
     * get count countext
     * @return int<0, max>|int 
     */
    public static function CountCountext(){
        $i = -1;
        if ($c = self::$sm_context_loading){ //  igk_environment()->get(self::class)){
            $i = count($c);
        }
        return $i;
    }
    protected function initialize(){

    }
    protected function uninitialize(){

    }
    /**
     * surround container with
     * @param IHtmlContextContainer $container 
     * @param callable $callable 
     * @param mixed $args 
     * @return bool 
     * @throws EnvironmentArrayException 
     */
    public static function SurroundWith(IHtmlContextContainer $container, $callable, &...$args):bool{
        $sm_context_loading = & self::_RefLoading(); //  $sm_context_loading;
        $c = $container->getContext() ?? igk_die('missing HtmlContext container');
        $v_def = [$c, $container];
        $v_init = false;
        if (!$sm_context_loading || (self::GetCurrentContext() != $c)){
            self::_LoadContextAndInitialize($sm_context_loading, $v_def);
            $v_init = true;
        }
        else {
            self::_LoadContext($sm_context_loading, $v_def);
        } 
        $g = $callable(...$args); 
        self::PopContext($v_init );
        return $g;
    }
}