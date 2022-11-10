<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlLoadingContext.php
// @date: 20220707 01:38:40
// @desc: loading context

namespace IGK\System\Html;

/**
 * 
 * @package 
 */
final class HtmlLoadingContext{
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
     * get current loading context
     * @return ?static
     */
    public static function GetCurrentContext(){
        return igk_environment()->peek(self::class);
    }
    public static function PushContext(HtmlLoadingContext $p){
        igk_environment()->push(self::class, $p);   
    }
    public static function PopContext(HtmlLoadingContext $p){
       
        $g = self::GetCurrentContext();
        //if ($g === $p){
            igk_environment()->pop(self::class);
        //}
    }
}