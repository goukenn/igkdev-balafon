<?php
// @file: IGKMenuUtils.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Helper;

use IGK\System\WinUI\Menus\MenuItem;

use function igk_resources_gets as __;


final class MenuUtils{
    ///<summary></summary>
    ///<param name="target"></param>
    ///<param name="table"></param>
    ///<param name="tab"></param>
    public static function BuildDbMenu($target, $table, $tab){
        self::BuildMenu($target, $tab, $menu, $pages);
    }
    ///<summary></summary>
    ///<param name="targetNode"></param>
    ///<param name="tab">array of MenuItem</param>
    ///<param name="menus" ref="true">menu references</param>
    ///<param name="pages" ref="true">pages references</param>
    public static function BuildMenu($targetNode, $tab, & $menus, & $pages){
        $v_list=array();
        $v_rlist=array();
        foreach($tab as $v){
            $n=strtolower($v->Name);
            $p=strtolower(self::GetParentName($v->Name));
            if(isset($v_list[$p])){
                $v_list[$p]->add($v);
            }
            else{
                $v_list[$n]=$v;
                if(isset($v_rlist[$n])){
                    foreach($v_rlist[$n] as $tk=>$tv){
                        $v->add($tv);
                    }
                    unset($v_rlist[$n]);
                }
                else{
                    if(isset($v_rlist[$p])){
                        $tb=$v_rlist[$p];
                        $tb[]=$v;
                    }
                    else
                        $tb=array($v);
                    $v_rlist[$p]=$tb;
                }
            }
        }
        igk_usort($tab, array("MenuItem", "SortMenuByIndex"));
        foreach($tab as $t=>$m){
            if($m->MenuParent == null){
                $menus[$m->Name]=self::InitMenu($targetNode, $m, $pages);
            }
        }
    }
    ///<summary></summary>
    ///<param name="menu"></param>
    public static function GetMenuLevel($menu){
        $q=$menu->MenuParent;
        $i=0;
        while($q !== null){
            $q=$q->MenuParent;
            $i++;
        }
        return $i;
    }
    ///<summary>get parent name of  the menu</summary>
    public static function GetParentName($name){
        $t=explode(".", $name);
        $c=count($t);
        if($c > 1){
            $out=IGK_STR_EMPTY;
            $v_sep=false;
            for($i=0; $i < $c-1; $i++){
                if($v_sep)
                    $out .= ".";
                else
                    $v_sep=true;
                $out .= $t[$i];
            }
            return $out;
        }
        return null;
    }
    ///<summary></summary>
    ///<param name="target"></param>
    ///<param name="menu"></param>
    ///<param name="pages" ref="true"></param>
    public static function InitMenu($target, $menu, & $pages){
        $add_uri=null;
        $node=null;
        if(isset($page)){
            $page=strtolower($menu->CurrentPage);
            if(isset($pages [$page])){
                if(!is_array($pages [$page])){
                    $cp=$pages [$page];
                    $cp->PageIndex=0;
                    $pages[$page]=array($cp);
                    $cp->MenuItem["href"]=$cp->MenuItem["href"];
                }
                $pages [$page][]=$menu;
                $menu->PageIndex=count($pages [$page])-1;
                $add_uri="&pageindex=".$menu->PageIndex;
                if($menu->MenuParent){
                    $add_uri=strtolower("&v=".substr($menu->Name, strlen($menu->MenuParent->Name) + 1));
                }
            }
            else{
                $pages [$page]=$menu;
            }
        }
        if($add_uri)
            ;
        $menu->updateUri($add_uri);
        if($menu->HasChilds){
            $menu->sortChilds();
            $menucl="igk-submenu submenu_".self::GetMenuLevel($menu);
            $v_ul=$menu->Node->add("ul")->setClass($menucl);
            foreach($menu->getChilds() as $k){
                $v=self::InitMenu($v_ul, $k, $pages);
            }
        }
        $target->add("li")->add('a')->setAttribute("href", $menu->getUri())->Content=__("menu.".$menu->Name);
    }
    ///<summary></summary>
    ///<param name="target"></param>
    ///<param name="menuTab"></param>
    public static function InitMenuArray($target, $menuTab){
        $pages=array();
        foreach($menuTab as $k=>$v){
            $c=new MenuItem($k, null, igk_getv($v, "uri"));
            self::InitMenu($target, $c, $pages);
        }
    }
}
