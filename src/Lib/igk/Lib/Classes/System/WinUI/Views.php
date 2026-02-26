<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Views.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\WinUI;
use function igk_resources_gets as __;
/**
 * contains view callable 
 * @package IGK\System\WinUI
 * @property static callable @Contact;
 * @method static void ActionBarConfirmDialog() action bar confirm dialog callable
 */
class Views {

    /**
    * Triggered when calling an inaccessible or undefined static method.
    * @param mixed $name
    * @param mixed $arguments
    */
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(static::class, $fc = "View".$name)){
            return [static::class, $fc]; 
        }
        return null;
    }

    /**
    * View contact.
    * @param mixed $n
    * @param mixed $info
    * @param null|mixed $key
    */
    public static function ViewContact($n, $info, $key=null){
        $li = $n->li()->setClass("contact-block-item");
        $s = $li;
        if ($lnk = igk_getv($info,"uri")){
            $s = $li->a($lnk);
        }
        if ($ico = igk_getv($info,"icon")){
            $s->span()->Content = igk_svg_use($ico);
        }
        $s->span()->Content = igk_getv($info, "text", $key ? __($key):null);       
    }

    /**
    * Model view limit.
    * @param mixed $target
    * @param mixed $model
    * @param callable $callback
    * @param null|mixed $conditions
    * @param null|mixed $options
    * @param mixed $key
    */
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

    /**
    * Model view handle limit.
    * @param mixed $host
    * @param mixed $target
    * @param mixed $model
    * @param callable $callback
    * @param null|mixed $conditions
    * @param null|mixed $options
    * @param mixed $key
    */
    public static function ModelViewHandleLimit($host, $target, $model, callable $callback, $conditions=null, $options=null, $key = "page") {
        $limit = self::ModelViewLimit($target, $model, $callback, $conditions, $options, $key);
        if ($limit){
            $host->add($limit->list());
        }
    }

    /**
    * View action bar confirm dialog.
    * @param mixed $a
    * @param null|array $options
    */
    public static function ViewActionBarConfirmDialog($a, ?array $options=null){        
        $title = null;
        $title = igk_getv($options, "lb.submit");
        $a->input("c.cancel", "button", __("Cancel"))->on("click", "igk.winui.controls.panelDialog.close(); return false;");
        $a->submit()->assertNode(!empty($title))->host(function($a, $title){
            $a["value"] = $title;
        }, $title); 
    }
}