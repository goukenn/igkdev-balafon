<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Paginator.php
// @date: 20220729 16:03:22
// @desc: 


namespace IGK\System\WinUI;

use IGK\System\Html\HtmlUtils;
use IGK\System\IO\StringBuilder;

/**
 * simple pagination class helper
 */
class Paginator{
    private $page;
    private $maxEntry;
    private $entries;
    public function Paginator($maxEntry, $queryTag="p"){
        $this->maxEntry = $maxEntry;
        $this->page = igk_getr($queryTag, 1);

    }   
    public function get_links(){
        $c = igk_create_node("ul");
        return $c->render();
    }
    public function get_limit_raw(){
        $p = $this->page - 1;
        return [
            ($p * $this->maxEntry),
            ($this->page * $this->maxEntry) + $this->maxEntry
        ];
    }
    public function get_limit(){
        $c = $this->get_limit_raw();
        return sprintf("Limit %s,%s", $c[0], $c[1]);
    }
    public function set_total($v){
        $this->maxEntry = $v;
    }
    public function page_links(){
        // TODO: Generate page links 
        $sb = new StringBuilder;
        // $sb->appendLine("generate page links --");
        $i =1;
        $attribs = "";
        $attribs = HtmlUtils::GetFilteredAttributeString("li", [
            'class'=>"igk-paginator-item"
        ]);
        $url = "";
        $s = "<li".$attribs.">";
        $s.= "<a class=\"link\" href=\"$url\" >$i</a>";
        $s.= "</li>";
        $sb->append($s);
        return $sb.'';
    }
}