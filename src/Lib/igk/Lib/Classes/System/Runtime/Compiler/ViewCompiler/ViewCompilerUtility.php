<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewCompilerUtility.php
// @date: 20221025 14:29:43
namespace IGK\System\Runtime\Compiler\ViewCompiler;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\ViewCompiler\Html\CompilerNodeModifyDetector;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewCompilerOptions;
use IGK\System\Runtime\Compiler\ViewCompilerBockInfo;
use IGK\System\ViewEnvironmentArgs;
use IGKException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Runtime\Compiler\ViewCompiler
 */
abstract class ViewCompilerUtility
{
    /**
     * render blocks
     * @param mixed $blocks 
     * @param StringBuilder $sb 
     * @return void 
     */
    public static function RenderBlock($blocks, StringBuilder $sb, $tabstop = "    ")
    {
        /**
         * @var mixed $p 
         */
        $tab = $blocks;
        $p = $q = null;
        $n = [];
        $depth = 0;
        while (count($tab) > 0) {
            $q = array_shift($tab);
            if (is_string($q)) {
                $sb->appendLine(trim($q));
            } else {
                if ($q === $p) {
                    if (!$p->isInnerBlock()) {
                        $depth--;
                        $sb->tabstop = str_repeat($tabstop, $depth);
                        $sb->appendLine($p->endBlock());
                    }
                    array_pop($n);
                    $p = igk_array_peek_last($n);
                    // if ($p = array_pop($n)){
                    //     $n[] = $p;
                    // }
                    continue;
                }
                if ($q->blocks) {
                    $p = $q;
                    $n[] = $p;
                    array_unshift($tab, $p);
                    array_unshift($tab, ...$q->blocks);
                    if ($p->isInnerBlock()) {
                        $c = $depth - 1;
                        $sb->tabstop = str_repeat($tabstop, $c);
                    } else {
                        $depth++;
                    }
                    $sb->appendLine(trim($p->startBlock()));
                    $sb->tabstop = str_repeat($tabstop, $depth);
                }
            }
        }
    }

    /**
     * compile instruction block
     * @param array|mixed $blocks 
     * @param StringBuilder $sb 
     * @param string $header declaration
     * @param mixed|IViewCompilerOptions|object|ViewEnvironmentArgs $options 
     * @param string $header declaration to init used and shared class and structures in the view
     * @param ?array $variables detected variable 
     * @return void 
     * @throws IGKException 
     * @throws Exception 
     * @throws CssParserException 
     */
    public static function CompileBlocks($blocks, StringBuilder $sb, $options, ?string $header = null,
        ?array $variable =null)
    {
        /**
         * @var ?object $p
         */
        $tab = $blocks;
        $p = null;
        $q = null;
        $n = [];
        $buffers = [];
        $depth = 0;

        $blockCompiler = new ViewBlockCompiler();
        $blockCompiler->detector = new CompilerNodeModifyDetector;
        $blockCompiler->controller = $controller ?? SysDbController::ctrl();
        $blockCompiler->header = $header;
        $tagname = null;
        if ($options->t) {
            $attr = HtmlRenderer::GetAttributeArray($options->t, null); // $options->t->getAttributes()->to_array();
            if ($options->t->getCanRenderTag()) {
                $tagname = $options->t->getTagName();
                $sb->appendLine('?><' . $tagname . '%__igk_attribute__%><?php');
            }
            if (!empty($attr))
                $sb->appendLine(sprintf("%s(%s);", '$__igk_attr__', var_export($attr, true)));
        }

        $vars =  (array)$options;
        if ($variable){
            $vars = array_merge($variable, $vars);
        }

        $blockCompiler->variables = $vars;

        $init = CompilerNodeModifyDetector::Init();
        $ss = null;
        while (count($tab) > 0) {
            $q = array_shift($tab);
            if (is_string($q)) {
                if ($src = $blockCompiler->compile(trim($q))) {
                    $sb->appendLine($src);
                } 
            } else {
                if ($q === $p) {
                    if (!empty($ss = $blockCompiler->complete())) {
                        $sb->appendLine($ss);
                    }

                    if (!$sb->isEmpty()) {
                        $sb->tabstop = str_repeat("   ", $depth - 1);
                        $sb->prependLine($p->startBlock());
                        // $sb->tabstop = str_repeat("    ", $depth);
                        if (!$p->isInnerBlock()) { 
                            $depth--;
                            $sb->tabstop = str_repeat("   ", $depth);
                            $sb->appendLine($p->endBlock());
                        }
                    }
                    array_pop($n);
                    $p = igk_array_peek_last($n);
                    $tsb = array_pop($buffers);
                    if ($tsb) {
                        $tsb->append($sb . "");
                    }
                    $sb = $tsb;
                    continue;
                }
                if ($q->blocks) {
                    $p = $q;
                    $n[] = $p;
                    $buffers[] = $sb;

                    if ($ss = $blockCompiler->complete())
                        $sb->appendLine($ss);
                    $sb = new StringBuilder;
                    array_unshift($tab, $p);
                    array_unshift($tab, ...$q->blocks);
                    if ($p->isInnerBlock()) {
                        $c = $depth - 1;
                        $sb->tabstop = str_repeat("    ", $c);
                    } else {
                        $depth++;
                    } 
                }
            }
        }
        // + | complete rendering  
        if ($c = $blockCompiler->complete())
            $sb->appendLine($c);
        if ($tagname) {
            $sb->appendLine("?></" . $tagname . ">");
        }
        if ($init) {
            CompilerNodeModifyDetector::UnInit();
        }
    }
}
