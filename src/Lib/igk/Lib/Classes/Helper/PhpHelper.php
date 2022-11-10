<?php

// @author: C.A.D. BONDJE DOUE
// @filename: PhpHelper.php
// @date: 20220601 14:18:34
// @desc: PhpHelp

namespace IGK\Helper;

use ReflectionFunction;

class PhpHelper{
    /**
     * get comment summary
     * @param string $phpDoc 
     * @return void 
     */
    public static function GetCommentSummary(?string $phpDoc=null){
        if (is_null($phpDoc)){
            return null;
        }
        $c = "";
        if (!empty($_doc = $phpDoc)){
            foreach(explode("\n", $_doc) as $ll){
                $ll = trim($ll);
                $ll = ltrim($ll,"/*");
                $ll = ltrim($ll,"*");
                $ll = ltrim($ll);
                if(strpos($ll,"@")===0)
                    continue;
    
                $c .= $ll; 
            }
        }
        return $c;
    }

    /**
     * 
     * @return string 
     */
    public static function HtmlComponentDocumention(){

        require_once IGK_LIB_DIR . "/igk_html_func_items.php";

        $_fcs = get_defined_functions(true)["user"];
        $ln = strlen(IGK_FUNC_NODE_PREFIX);
        sort($_fcs);
        $o = ""; 
        foreach ($_fcs as $f) {
            if (!preg_match("/^igk_html_node_/", $f)) {
                continue;
            }
            $p = strtolower(substr($f, $ln));
            $m = "";
            $r = "self";
            $ref = new ReflectionFunction($f);
            if ($params = $ref->getParameters()) {
                $m = implode(", ", array_filter(array_map(function ($p) {
                    $g = "";
                    $t = "";
                    if ($p->hasType()) {
                        if ($p->isOptional()) {
                            $t .= "?";
                        }
                        $t .= $p->getType()->getName() . " ";
                    }
                    if ($p->isDefaultValueAvailable()) {
                        if ($p->isDefaultValueConstant()) {
                            $g .= "= " . $p->getDefaultValueConstantName();
                        } else {
                            $gg = $p->getDefaultValue();
                            if (is_array($gg)){
                                if (empty($gg)) {
                                    $g.= "= []";
                                }else{
                                    igk_wln_e("default array ".implode("gg:", $gg));
                                }
                            }else{
                                $g .= "= " .(is_null($gg) ? 'null' : "'" . $gg . "'");
                            }
                        }
                    }

                    if ($p->isVariadic()) {
                        $t .= " ...";
                    }

                    return $t . "$".$p->name . $g;
                }, $params)));
                //igk_wln_e($f, $params, $params[0], $m);
            }
            $c = self::GetCommentSummary($ref->getDocComment());
            $o .= "@method {$r} ".$p."($m) {$c}\n"; 
        } 
        return $o;
    }
}
