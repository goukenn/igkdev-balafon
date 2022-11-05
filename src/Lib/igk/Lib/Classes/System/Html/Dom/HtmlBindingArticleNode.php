<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlBindingArticleNode.php
// @date: 20221010 12:55:19
namespace IGK\System\Html\Dom;

use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\CompilerConstants;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
*/
class HtmlBindingArticleNode extends HtmlNode{
    var $tagname = "igk:binding-article";
    var $file;
    var $ctrl;
    var $data;
    var $index;
    var $target;
    /**
     * bool caching result 
     * @var false
     */
    var $caching = false;

    // binding counter:

    private static $sm_Count;

    public static function ResetBindingCounter(){
        static::$sm_Count = 0;
    }
    public function __construct(){
        parent::__construct(); 
        $this->index = self::$sm_Count;
        self::$sm_Count++;  
    }
    function getCanRenderTag():bool{
        return false;
    }
    public function getRenderedChilds($options = null)
    {
        return [];
    }
    public function render($options = null)
    {  
        // + | --------------------------------------------------------------------
        // + | RENDER BINDING NODE
        // + |
        if ($this->caching){
            $this->target = igk_create_notagnode();       
            $sb = new StringBuilder;
            $index= intval($this->index); 
            $this->_bind();
            $sb->appendLine("<?php");
            // render binding node 
            $is_array = is_array($this->data) && !isset($this->data["raw"]);
            // + | --------------------------------------------------------------------
            // + | BINDING ARTICLE CONFIGURATION
            // + |
            $param = '$'.CompilerConstants::BINDING_DATA_CONTEXT_VAR;
            $is_array && $sb->appendLine(
                [
                    "foreach(/* render binding node */ {$param}[$index] as \$index=>\$raw):",
                    "\$context_raw = \$raw;"
                ]
            );

            $sb->appendLine("?>".$this->target->render());
            $is_array &&  $sb->append("<?php endforeach;\n?>");       
            return $sb;
        }
    }
    private function _bind(){
        $f = $this->file;
        $ctrl = $this->ctrl;
        $data = $this->data;
        $articleoptions = null;
        $n = $this->target;
        if (is_file($f) && !empty($content = igk_io_read_allfile($f))) {
            $ldcontext = igk_init_binding_context($n, $ctrl, $data);
            $ldcontext->transformToEval = true;
            igk_push_article_chain($f, $ldcontext);
            igk_html_bind_article_content($n, $content, $data, $ctrl, basename($f), true, $ldcontext);
            if ($articleoptions) {
                igk_html_article_options($ctrl, $n, $f);
            }
            igk_pop_article_chain();
            $n->setFlag("NO_CHILD", 1);
        }
    }
 
}