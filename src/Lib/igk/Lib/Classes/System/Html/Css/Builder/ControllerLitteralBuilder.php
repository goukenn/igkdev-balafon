<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerLitteralBuilder.php
// @date: 20230418 09:39:59
namespace IGK\System\Html\Css\Builder;

use igk\devtools\DocumentParser\UriDetector;
use IGK\Helper\IO;
use IGK\System\Html\Css\CssUtils;
use IGK\System\IO\Path;
use IGK\System\Regex\Replacement;
use IGKValidator;

///<summary></summary>
/**
 * help build litteral by entry in css file
 * @package IGK\System\Html\Css\Builder
 */
class ControllerLitteralBuilder
{
    var $outputFile;
    var $controller;
    var $outputDir;

    private $m_resources;

    public function resplaceResPath($path)
    {
        $asset = $this->controller->asset("/", false);
        $rp = new Replacement;
        $rp->add("#/_lib_/Default/R/Img/#", "/core_img/");
        $rp->add("#/_lib_/Default/R/Css/#", "/core_css/");
        $rp->add("#/_lib_/Default/R/JS/#", "/core_js/");
        $rp->add("#/{$asset}#", "/assets/");


        return $rp->replace($path);
    }

    public function build()
    {
        $css = CssUtils::GenCss($this->controller);
        $res = new UriDetector;
        $gtab = $res->cssUrl($css);
        $bdir = igk_io_basedir();
        if ($gtab) {
            foreach ($gtab as $k) {
                if (!IGKValidator::IsUri($k->path)) {
                    $s = Path::CombineAndFlattenPath($bdir, $k->path);
                    if (isset($this->m_resources[$s])) {
                        continue;
                    }
                    $fc = '';
                    if (is_file($s)) {
                        $m = $this->resplaceResPath($s);
                        $g = getimagesize($s);
                        if ($g) {
                            $target = $this->outputDir . igk_str_rm_start($m, $bdir);

                            IO::CreateDir(dirname($target));
                            igk_io_w2file($target, file_get_contents($s));

                            $fc = igk_str_rm_start($target, $this->outputDir);
                            $css = str_replace($k->uri, "url({$fc})", $css);
                        } else {
                            // not a picture : depend on extension 
                            $ext = igk_io_path_ext($s);
                            $fc = "_store_" . $ext;
                            $this->$fc($s);
                        }
                    }

                    $this->m_resources[$s] = $fc;
                }
            }
        }
        $css = preg_replace('/\*#\s*sourceMappingURL\s*=\s*[^\*]+\*\/\//', '', $css);

        igk_io_w2file($this->outputFile, $css);
    }
    protected function _store_css($file)
    {
    }
    protected function _store_js($file)
    {
    }
}
