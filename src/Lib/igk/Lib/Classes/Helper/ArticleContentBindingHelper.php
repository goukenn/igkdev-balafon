<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ArticleContentBindingHelper.php
// @date: 20221115 22:47:41
// @desc: 
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\Models\ModelBase;

class ArticleContentBindingHelper{
    const PIPE_ARG_FORMAT = "#\{\{\s*(?P<property>%s)\s*((\|(?P<pipe>[^\}]+))?\s*)?\}\}#";
    /**
     * get system binding array of 
     */
    public static function GetData($data): array{
        $v_result = [];
        if (!is_null($data)){
            if ($data instanceof ModelBase){
                $v_result = $data->to_array();
            }
            else if (is_object($data)){ 
                $v_result = (array)$data;
            } else if (is_array($data)){
                $v_result = $data;
            }
        }
        return $v_result;
    }
    /**
     * get binding article - from current controller or SysDbController
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
     * bind article with pipe binding expression 
     * @param string $content 
     * @param mixed|null $args 
     * @return void 
     */
    public static function BindContent(string $content, $args){
        if ($args){
            foreach($args as $k=>$v){ 
                $rgx = sprintf(self::PIPE_ARG_FORMAT, $k);
                $content = preg_replace_callback(
                    $rgx                
                    ,function($d)use($v){                    
                        if (isset($d['pipe'])){
                            $v = igk_str_pipe_value($v, $d['pipe']);
                        }                   
                        return $v;
                    }
                    , $content); 
            }
        }
        return $content;
    }
}
