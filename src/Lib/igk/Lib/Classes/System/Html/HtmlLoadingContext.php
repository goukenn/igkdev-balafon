<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLoadingContext.php
// @date: 20220707 01:38:40
// @desc: loading context

namespace IGK\System\Html;

use IGK\System\Exceptions\EnvironmentArrayException;

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
    
    /**
     * get current loading context
     * @return ?static
     */
    public static function GetCurrentContext(){
        return igk_environment()->peek(self::class);
    }
    /**
     * push loading context
     * @param HtmlLoadingContext $p 
     * @return void 
     * @throws EnvironmentArrayException 
     */
    public static function PushContext(HtmlLoadingContext $p){
        igk_environment()->push(self::class, $p); 
        $p->initialize();  
    }
    /**
     * pop loading context
     * @return void 
     */
    public static function PopContext(){
       
        // $g = self::GetCurrentContext();
        //if ($g === $p){
        if ($c = igk_environment()->pop(self::class)){
            $c->uninitialize();
        }
        //}
    }
    protected function initialize(){

    }
    protected function uninitialize(){

    }
    /**
     * surround container with
     * @param IHtmlContextContainer $container 
     * @param mixed $callable 
     * @param mixed $args 
     * @return mixed 
     * @throws EnvironmentArrayException 
     */
    public static function SurroundWith(IHtmlContextContainer $container, $callable, &...$args){
        $c = $container->getContext();
        self::PushContext($c);
        $g = $callable(...$args);
        self::PopContext();
        return $g;
    }
}