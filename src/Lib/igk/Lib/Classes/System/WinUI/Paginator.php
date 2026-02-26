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

    /**
    * auto generate doc.
    * @var mixed
    */
    private $page;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $maxEntry;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $entries;

    /**
    * auto generate doc.
    * @param mixed $maxEntry
    * @param mixed $queryTag
    */
    public function Paginator($maxEntry, $queryTag="p"){
        $this->maxEntry = $maxEntry;
        $this->page = igk_getr($queryTag, 1);
    }

    /**
    * auto generate doc.
    */
    public function get_links(){
        $c = igk_create_node("ul");
        return $c->render();
    }

    /**
    * auto generate doc.
    */
    public function get_limit_raw(){
        $p = $this->page - 1;
        return [
            ($p * $this->maxEntry),
            ($this->page * $this->maxEntry) + $this->maxEntry
        ];
    }

    /**
    * auto generate doc.
    */
    public function get_limit(){
        $c = $this->get_limit_raw();
        return sprintf("Limit %s,%s", $c[0], $c[1]);
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */
    public function set_total($v){
        $this->maxEntry = $v;
    }

    /**
    * auto generate doc.
    */
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