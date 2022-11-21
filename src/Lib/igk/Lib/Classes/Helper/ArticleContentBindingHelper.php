<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ArticleContentBindingHelper.php
// @date: 20221115 22:47:41
// @desc: 
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;

class ArticleContentBindingHelper{

    /**
     * get binding article
     * @param BaseController $ctrl 
     * @return false|string 
     */
    public static function GetBindingArticle(BaseController $ctrl, string $article){
        $sysdb = SysDbController::ctrl();
        $p = [$ctrl];
        if ($ctrl != $sysdb){
            $p[] = $sysdb;
        }
        while(count($p)>0){
            $ctrl = array_shift($p);
            if (file_exists($file = $ctrl->getArticle($article))){
                return $file;
            }
        }
        return false;
    }

    /**
     * 
     * @param string $content 
     * @param mixed $args 
     * @return void 
     */
    public static function BindContent(string $content, $args){
        foreach($args as $k=>$v){
            $content = preg_replace_callback(
                "#\{\{\s*".$k."\s*\}\}#"
                ,function()use($v){
                    return $v;
                }
                , $content); 
        }
        return $content;
    }
}
