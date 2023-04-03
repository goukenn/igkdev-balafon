<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlCoreJSScriptsNode.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\Core\Traits\ScriptTrait;
use IGK\Helper\IO;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;
use IGK\System\IO\StringBuilder;
use IGKCaches;
use IGKException;
use IGKResourceUriResolver;

/**
 * core script rendering
 * @package IGK\System\Html\Dom
 */
final class HtmlCoreJSScriptsNode extends HtmlNode
{
    use ScriptTrait;

    private static $sm_instance;
    public static function getItem()
    {
        if (self::$sm_instance == null) {
            self::$sm_instance = new self();
        }
        return self::$sm_instance;
    }
    private function __construct(){
        parent::__construct("igk:js-core-script");
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function getCanRenderTag()
    {
        return false;
    }
    protected function _acceptRender($options = null):bool
    {
        return $this->getIsVisible() && igk_getv($options, "Document");        
    }
    protected function _getRenderingChildren($options = null)
    {
        return null;
    }
    public function render($options = null)
    { 
        $tabstop = "";
        $bck_def = false;
        $bck_def = $options->Depth;
        $options->Depth = max(0, $options->Depth - 1);
      
        $tabstop = HtmlRenderer::GetTabStop($options);
        $sb = new StringBuilder();
        $script = self::GetCoreScriptContent($options, igk_environment()->isOPS());
 
        if (igk_environment()->isDev()) {
            $sb->appendLine($tabstop."<!-- core scripts -->");
            $sb->appendLine($script);
            $sb->appendLine($tabstop."<!-- end:core scripts -->");  
        } else {
            // production script
            $sb->appendLine($script);
        }
        if($bck_def)
            $options->Depth = $bck_def;
        return $sb.'';
    }
  
}
