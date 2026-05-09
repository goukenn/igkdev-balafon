<?php
// @author: C.A.D. BONDJE DOUE
// @file: MarkdownConverterStates.php
// @date: 20260426 19:04:16
namespace IGK\System\IO\Markdown;

use IGK\Helper\StringUtility;

/**
 * 
 * @package IGK\System\IO\Markdown
 * @author C.A.D. BONDJE DOUE
 */
class MarkdownConverterStates
{
    const INIT_NODE_PREFIX = '_init_node_';
    const PREPEND_NODE_FORMAT = '_prepend_node_%s_item';
    const APPEND_NODE_PREFIX_FORMAT = '_append_node_%s_item';
    protected function _canCreateSubContainerListener()
    {
        return null;
    }
    /**
     * 
     * @param mixed $converter 
     * @param mixed $info 
     * @return void 
     */
    public function treatSubDefinition($converter, IMarkdownSubListTreatmentInfo $info)
    {
        extract(igk_extract_assoc($info, 'type|canCreateSubContainerListener|moveToQuoteDepthListener|handleNullParentListener|depth|parent|value|subcontainer*|state*|currentNode*'), EXTR_REFS);
        $canCreateSubContainerListener = $this->_canCreateSubContainerListener($info) ?? $canCreateSubContainerListener;
        $info->canCreateSubContainer = $canCreateSubContainerListener($type, $depth);
        $func_name = StringUtility::FuncName($type);

        if ($info->canCreateSubContainer) {
            if (is_null($parent) && $handleNullParentListener) {
                $handleNullParentListener($type);
            }
            $n = $this->createNode($type, $depth);
            if ($converter->supportCounter){
                $counter = $converter->getCounter($type);
                $n['class'] = $type.'-id-'.$counter;
            }
            $subcontainer = (object)[
                'node' => $n,
                'depth' => $depth,
                'parent' => $parent,
                'parent_state' => $state,
                'subquote_parent' => $subcontainer,
            ];
            if (is_null($parent)) {
                $currentNode = $n;
            } else {
                $parent->add($n);
            }
        } else {
            if (false === $moveToQuoteDepthListener($subcontainer, $depth)) {
                if (method_exists($this, $fc = sprintf(self::PREPEND_NODE_FORMAT, $func_name))) {
                    call_user_func_array([$this, $fc], [$subcontainer->node]);
                } else
                    $subconainer->node->br();
            }
        }
        if (method_exists($this, $fc = sprintf(self::APPEND_NODE_PREFIX_FORMAT,$func_name ))){
            call_user_func_array([$this, $fc], [$subcontainer->node, $value, $converter]);
        } else 
            $subcontainer->node->text($value);
        $state = $type;
    }
    public function createNode(string $type, int $depth)
    {
        if (method_exists($this, $fc = self::INIT_NODE_PREFIX . StringUtility::FuncName(strtolower($type)))) {
            return call_user_func([$this, $fc], [$depth]);
        }
        $ul = igk_create_node('ul');
        $ul->setAttributes(['class' => 'sublist-' . $depth]);
        return $ul;
    }
    public function _prepend_node_sublist_item($n) {
    }
    public function _append_node_sublist_item($n, $value, $converter) {
        $li = $n->li();
        if ($attr = $converter->getStyleAttributes('li')){
            $li->setAttributes($attr);
        }
        $li->text($value);
    }
}
