<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlConditionNode.php
// @date: 20221109 09:21:39
namespace IGK\System\Html\Dom;
use IGK\System\Html\HtmlRenderer;
use IGK\System\Html\RenderingContext;
use IGK\System\IO\StringBuilder;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlConditionNode extends HtmlNode{

    /**
    * Name of tagname.
    * @var mixed
    */
    var $tagname = 'igk:if-condition';

    /**
    * Property: condition.
    * @var mixed
    */
    var $condition;

    /**
    * Sets Sys Attribute.
    * @param mixed $key
    * @param mixed $value
    * @param null|mixed $context
    * @return bool
    */
    public function setSysAttribute($key, $value, $context = null): bool
    {
        if ($key == 'condition'){
            $this->condition = $value;
            return true;
        }
        return parent::setSysAttribute($key, $value);
    }

    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options =null){
        $tab= $this->getRenderedChilds();
        if (!$tab || (count($tab) == 0)){
            return null;
        }
        $src = implode('', array_map(function($a)use($options){
            return HtmlRenderer::Render($a, $options);
        }, $tab));
        if ($options && (igk_getv($options,'renderingContext') == RenderingContext::TEMPLATE)){
            $sb = new StringBuilder;
            $sb->append("<".$this->getTagName());
            if ($t = HtmlRenderer::GetAttributeString($this, $options)){
                $sb->append(" ".$t);
            }
            $sb->append(">");
            $sb->append($src);
            $sb->append("</".$this->getTagName().">");
            return $sb.'';
        }
        if ($this->condition){
            $src = '<?php if ('.$this->condition.') : ?>'.$src;
            $src .= "<?php endif ; ?>";
        }
        // igk_trace();
        // igk_wln_e(__FILE__.":".__LINE__, $this->getAttributes()->to_array(),  
        // "\n-- is visible not in rendering context ", "\n".$src,
        // "\n-- condition".$this->condition
        // );
        return $src;
    }
}