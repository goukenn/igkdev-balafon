<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlLayoutViewInclusion.php
// @date: 20230319 08:20:07
namespace IGK\System\Html\Dom;
use IGK\Controllers\BaseController;
use IGK\Helper\ViewHelper;
use IGK\System\Html\HtmlNodeBuilder;
/**
 * 
 * @package IGK\System\Html\Dom
 */
class HtmlLayoutViewInclusion extends HtmlNode
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $path;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ctrl;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $tagname = "igk:view-include";

    /**
    * auto generate doc.
    * @var mixed
    */
    var $args;

    /**
    * auto generate doc.
    */
    public function getCanRenderTag()
    {
        return false;
    }

    /**
    * .ctr
    * @param string $path
    * @param BaseController $ctrl
    */
    public function __construct(string $path, BaseController $ctrl)
    {
        parent::__construct();
        $this->path = $path;
        $this->ctrl = $ctrl;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null): bool
    {
        if ($this->getIsVisible() && ($path = $this->getPath()) ) {
            $this->clear();        
            $context = ViewHelper::GetViewContextArgs();            
            $t = $context['t'] = igk_create_notagnode(); 
            $context['builder'] = new HtmlNodeBuilder($t);            
            ob_start();
            ViewHelper::Inc($path, $context); 
            $s = $t->render().ob_get_contents();            
            ob_end_clean();
            if (!empty($s)) { 
                parent::text($s);
                return true;
            }
        }
        return false;
    }

    /**
    * auto generate doc.
    */
    public function getPath(){
        $p = $this->path;
        if (igk_str_startwith($this->path, "@")){
            if (preg_match("/^@(?P<name>[^\/]+)(\/)?/i", $p, $tab)){
                $c = igk_str_rm_start($p, $tab[0]);
                switch(strtolower($tab['name'])){
                    case 'layout':{
                        $layout = $this->ctrl->getViewLoader();
                        return $layout->dir()."/".$c;
                    }
                    break;
                    default:
                        return $this->ctrl->getViewDir()."/".$c;
                }
            }
        }
    }
}