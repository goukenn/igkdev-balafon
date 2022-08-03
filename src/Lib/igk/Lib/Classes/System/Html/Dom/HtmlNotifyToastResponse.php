<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNotifyToastResponse.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;


class HtmlNotifyToastResponse extends HtmlNode{
    private $m_notifyname;
    protected $tagname = "notify:toast";
    public function getCanAddChilds()
    {
        return false;
    }

    public function __construct($name)
    {
        parent::__construct();
        $this->m_notifyname = $name;
    }
    public function render($options=null){
        $o = null;
        if ($tg = igk_notifyctrl($this->m_notifyname)){
            $tab = $tg->getTab();

            if (count($c = $tab)>0){
                $n = new HtmlNode("div");
                $n->ul()->loop($c)->host(function($n, $i){
                    $n->li()->setClass($i["type"])->Content = $i["msg"];
                });
                ob_start();
                igk_ajx_toast($n->render());
                $o = ob_get_clean();
                
            } 
            $tg->clear();
        }        
        return $o;
    }
}