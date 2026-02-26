<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNotifyToastResponse.php
// @date: 20220803 13:48:56
// @desc:
namespace IGK\System\Html\Dom;

/**
* Html notify toast response.
* @package IGK\System\Html\Dom
*/
class HtmlNotifyToastResponse extends HtmlNode{

    /**
    * Name of notifyname.
    * @var mixed
    */
    private $m_notifyname;

    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "notify:toast";
    /**
     * Indicates that this node does not accept child nodes.
     * @return bool
     */

    public function getCanAddChilds()
    {
        return false;
    }
    /**
     * Constructor.
     * @param string $name The notification channel name to bind this toast to.
     */

    public function __construct($name)
    {
        parent::__construct();
        $this->m_notifyname = $name;
    }
    /**
     * Renders pending toast notifications for the bound notification channel.
     * @param mixed $options Render options.
     * @return string|null
     */

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
