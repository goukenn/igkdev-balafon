<?php
// @author: C.A.D. BONDJE DOUE
// @filename: SvgRenderer.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\SVG;

use Exception;
use IGK\Helper\IO;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\SvgListNode;
use IGKEvents;
use IGKException;
use ReflectionException;

/**
 * document page svg list renderer
 */
class SvgRenderer
{

    /**
    * Path to register path.
    * @var mixed
    */
    public static $RegisterPath = [];

    /**
    * Collection of render list.
    * @var mixed
    */
    private static $sm_renderList = false;

    /**
    * Constant: folder.
    * @var mixed
    */
    const FOLDER = __CLASS__ . "::svgLibFolder";

    /**
    * Constant: render list method.
    * @var mixed
    */
    const RENDER_LIST_METHOD = 'RenderList';
    /**
     * return svg folder 
     * @return mixed 
     */

    public static function GetPath($name, &$class = null)
    {
        if (!empty($name)) {
            $f = self::GetSvgFolder();
            while ($q = array_shift($f)) {
                $d = $q[0]; // directory    
                if (igk_io_cache_file_exists($file = $d . "/" . $name . ".svg", true)) {
                    $class = $q[1];
                    return IO::GetDir($file);
                }
            }
        }
        return false;
    }
    /**
     * return svg key folder 
     * @return array 
     */

    public static function GetSvgFolder()
    {
        $svg_folder = igk_environment()->get(self::FOLDER) ?? [];
        $svg_folder[] = [IGK_LIB_DIR . "/Data/R/svg/icons", "igk"];
        return $svg_folder;
    }
    /**
     * register folder 
     * @param mixed $folder 
     * @return void 
     * @throws EnvironmentArrayException 
     */

    public static function RegisterFolder(string $folder, ?string $targetLib = null)
    {
        if (is_dir($folder)) {
            if (
                !($t = igk_environment()->get(self::FOLDER)) ||
                !in_array($folder, $t)
            ) {
                igk_environment()->push(self::FOLDER, [$folder, $targetLib]);
            }
        }
    }
    /**
     * check if the svg icons exists is registrated folder
     * @param mixed $name 
     * @return bool 
     */

    public static function Exists(string $name)
    {
        $f = self::GetSvgFolder();
        while ($d = array_shift($f)) {
            if (file_exists($d . "/" . $name)) {
                return true;
            }
        }
        return false;
    }

    /**
    * auto generate doc.
    * @param mixed $options
    * @return void
    */

    public static function AcceptRenderList($options)
    {
        if (!self::$sm_renderList) {
            $r = [self::class, self::RENDER_LIST_METHOD];
            if (igk_getv($options, "Document")) {
                igk_reg_hook(IGKEvents::HOOK_HTML_BODY, $r);
                $options->Document->setTempFlag("svg:list", []);
                self::$sm_renderList = true;
            } else if (igk_is_ajx_demand()) {
                igk_reg_hook(IGKEvents::HOOK_AJX_END_RESPONSE, $r);
                self::$sm_renderList = true;
            }
        }
    }
    /**
     * hook callback 
     * @param mixed $e 
     * @return void 
     * @throws Exception 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */

    public static function RenderList($e)
    {
        $options = igk_getv($e->args, "options");
        $is_dev = igk_environment()->isDev();
        echo self::RenderSVGList($options, $is_dev);
        // clear the registrated path
        self::$RegisterPath = [];
    }

    /**
    * Renders SVGList.
    * @param null|mixed $options
    * @param mixed $debug
    * @return string
    */
    public static function RenderSVGList($options=null, $debug = false): string
    {
        ob_start();
        if ($list =  self::$RegisterPath) {

            if ($debug) {
                echo "<!-- SVG LIST -->\n";
            }
            $n = new SvgListNode();
            $n->host(function () use ($list) {
                foreach ($list as $k => $v) {
                    echo "<" . $k . ">";
                    echo "<!-- svg content -->";
                    echo igk_svg_content(igk_io_read_allfile($v));
                    echo "</" . $k . ">";
                }
            });
            echo $n->render($options);
            if ($debug)
                echo "\n<!-- END:SVG LIST -->\n";
        }
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /**
    * Registers Icon.
    * @param mixed $name
    * @param null|mixed $context
    */
    public static function RegisterIcon($name, $context = null)
    {
        return self::svgNewIcons($name);
    }
    private static function svgNewIcons($name)
    {
        $n = new SvgListIconNode($name);
        return $n;
    }
}
