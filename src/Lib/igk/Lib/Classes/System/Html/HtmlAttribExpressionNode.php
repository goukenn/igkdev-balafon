<?php
// @file: IGKHtmlAttribExpressionNode.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\XML\XmlNode;

/**
* auto generate doc.
* @package IGK\System\Html
*/
class HtmlAttribExpressionNode extends XmlNode
{

    /**
    * auto generate doc.
    * @var mixed
    */
    var  $node_args;

    /**
    * auto generate doc.
    * @var mixed
    */
    var  $target_node;
    /**
     * Constructor.
     *
     * @param HtmlItemBase $cnode The target HTML node to bind attributes to.
     * @param array        $c     The context arguments for attribute expressions.
     */

    public function __construct(HtmlItemBase $cnode, array $c)
    {
        parent::__construct(IGK_ENGINE_ATTR_EXPRESSION_NODE);
        $this->node_args = $c;
        $this->target_node = $cnode;
    }
    /**
     * Indicates whether child nodes can be added to this node.
     *
     * @return bool
     */

    public function getCanAddChild()
    {
        return false;
    }
    /**
     * Processes and applies attribute expressions to the target node after loading.
     */

    public function loadingComplete()
    {
        $context = null;
        $m = $this->Attributes->to_array();
        $_p = [];
        $_g = explode("|", "*for|*visible");
        $context = $this->node_args ?? igk_get_attrib_raw_context($context);
        $p = $this->target_node;
        foreach ($m as $k => $t) {
            // ignore attribute binding
            if (in_array($k, $_g))
                continue;
            if ($k[0] == "*") {
                $t = igk_template_get_piped_value($t, $context);
                $k = ltrim($k, "*");
            }
            $_p[$k] = $t;
        }
        if (count($_p) > 0) {
            // + append attribute
            $p->setAttributes($_p);
        }
        $this->node_args = null;
        $this->dispose();
    }
}
