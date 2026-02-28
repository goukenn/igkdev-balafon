<?php
// @author: C.A.D. BONDJE DOUE
// @file: MailPreviewNode.php
// @date: 20250427 08:39:38
namespace IGK\System\Http\Mail;
use IGK\Css\CssThemeResolver;
use IGK\Helper\ViewHelper;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\Traits\HostableItemTrait;
/**
* 
* @package IGK\System\Http\Mail
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
*/
class MailPreviewNode extends HtmlNode
{
    use HostableItemTrait;

    /**
    * Name of tagname.
    * @var mixed
    */
    var $tagname = 'div';

    /**
    * Property: theme resolver.
    * @var mixed
    */
    private $m_theme_resolver;

    /**
    * Collection of resolved list.
    * @var mixed
    */
    var $resolvedList = [];

    /**
    * Initializes.
    */
    protected function initialize()
    {
        $this['class'] = 'igk-winui-preview';
        $resolver = new CssThemeResolver;
        $this->m_theme_resolver = $resolver;
    }

    /**
    * Returns Rendered Childs.
    * @param null|mixed $options
    */
    public function getRenderedChilds($options = null)
    {
        // convert rendering to view 
        $child = parent::getRenderedChilds($options);
        if (count($child)) {
            $mail = igk_create_node('div');
            $mail->setStyle("margin-left: auto; margin-right: auto");
            $resolver = $this->m_theme_resolver;
            $doc = igk_getv($options, 'Document') ?? ViewHelper::CurrentDocument();
            $systheme = $doc->getSystheme();
            $theme = $doc->getTheme();
            $resolver->parent = $systheme;
            $resolver->theme = $theme;
            $systheme->initGlobalDefinition();
            HtmlUtils::CopyNode($mail, $child, function (string $n) use ($options) {
                return new MailNode(
                    $options,
                    function ($i) {
                        return $this->_resolv_class($i);
                    },
                    $n
                );
            });
            return [$mail];
        }
        return [];
    }

    /**
    * auto generate doc.
    * @param mixed $i
    * @return
    */
    private function _resolv_class($i)
    {
        if (isset($this->resolvedList[$i])) {
            return igk_getv($this->resolvedList, $i);
        }
        return $this->resolvedList[$i] = $this->m_theme_resolver->treat("(:." . $i . ")", false);
    }
}