<?php

namespace IGK\System\WinUI;
use function igk_resources_gets as __;
/**
 * contains view callable 
 * @package IGK\System\WinUI
 * @property static callable @Contact;
 * @method static void ActionBarConfirmDialog() action bar confirm dialog callable
 */
class Views {    
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(static::class, $fc = "View".$name)){
            return [static::class, $fc]; 
        }
        return null;
    }
    public static function ViewContact($n, $info, $key=null){
        $li = $n->li()->setClass("contact-block-item");
        if ($ico = igk_getv($info,"icon")){
            $li->span()->Content = igk_svg_use($ico);
        }
        $s = $li;
        if ($lnk = igk_getv($info,"uri")){
            $s = $li->a($lnk);
        }
        $s->span()->Content = igk_getv($info, "text", $key ? __($key):null);       
    }

    public static function ModelViewLimit($target, $model, callable $callback, $conditions=null, $options=null, $key = "page") {
        $options = $options ?? [];
        $c = $model::count($conditions, $options);
        $pan = null;     
        if ($c>0){ 
            $blimit = igk_getv($options, "Limit", PageLayout::ItemLimits());            
            if ($c > $blimit){
                $pan = new Pagination($blimit, $c);
                $options["Limit"] = $pan->getLimit();
            } 
            if ($r= $model::select_all($conditions, $options)){
                foreach($r as $v){
                    $callback($target, $v);
                }
            }
        }
        return $pan;
    }
    public static function ModelViewHandleLimit($host, $target, $model, callable $callback, $conditions=null, $options=null, $key = "page") {
        $limit = self::ModelViewLimit($target, $model, $callback, $conditions, $options, $key);
        if ($limit){
            $host->add($limit->list());
        }
    }

    public static function ViewActionBarConfirmDialog($a, ?array $options=null){        
        $title = null;
        $title = igk_getv($options, "lb.submit");
 

        $a->input("c.cancel", "button", __("Cancel"))->on("click", "igk.winui.controls.panelDialog.close(); return false;");
        $a->submit()->assertNode(!empty($title))->host(function($a, $title){
            $a["value"] = $title;
        }, $title); 
    }
}