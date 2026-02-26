<?php
// @file: IGKHtmlA.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use IGK\System\Html\HtmlUtils;
use function igk_resources_gets as __;

/**
* Html anode.
* @package IGK\System\Html\Dom
*/
class HtmlANode extends HtmlNode
{

    /**
    * Properties: href, rdef.
    * @var mixed
    */
    private $m_href, $m_rdef;

    /**
    * Property: domain link.
    * @var mixed
    */
    var $domainLink;
    /**
     * Prepares href, onclick, and title attributes before rendering the anchor node.
     *
     * @param mixed $option Rendering options.
     * @return bool
     */

    protected function _acceptRender($option = null):bool
    {
        if (!$this->getIsVisible())
            return false;
        if ($this["onclick"] == null) {
            $bck = $this["href"]->getUri();
            $kr = (is_string($bck) ? $bck : HtmlUtils::GetValue($bck, $option)) ?? '';
            if (strpos(trim($kr), "javascript") === 0) {
                $this["onclick"] = $kr . " return false;";
                $this->m_rdef = 1;
            }
        }
        if (!defined('IGK_TEST_INIT') && !igk_environment()->isOPS() && (!$this["alt"] && !$this["name"] && !$this["title"])) {
            if ($s = $this->getContent()) {
                if (is_object($s)) {
                    $s = ":object";
                } 
                $_ass =  sprintf(__("link to %s"), $s);
                // !$this["alt"] && ($this["alt"] = $_ass);
                // !$this["name"] && ($this["name"] = $_ass);
                // important title for accessibility 
                !$this["title"] && ($this["title"] = $_ass);
            }
        }
        return true;
    }
    /**
     * Constructor.
     *
     * @param string $uri The href URI for the anchor element.
     */

    public function __construct($uri = "#")
    {
        parent::__construct("a");
        $this->m_href = new HtmlAHref($this);
        $this->domainLink = 0;
        parent::offsetSet("href", $this->m_href);
        $this->m_href->setValue($uri);
    }

    /**
    * Render complete.
    * @param null|mixed $option
    */

    protected function __RenderComplete($option = null)
    {
        if ($this->m_rdef == 1) {
            $this["onclick"] = null;
            $this->m_rdef = 0;
        }
    }

    /**
    * Offset set.
    * @param mixed $k
    * @param mixed $v
    * @return void
    */

    public function offsetSet($k, $v): void
    {
        if ($k == "href") {
            if ($this->m_href !== $v) {
                $this->m_href->setValue($v);
                return;
            } else
                igk_die("can't set the href to the same value");
        }
        parent::offsetSet($k, $v);
    }
}