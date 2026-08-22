<?php
// @author: C.A.D. BONDJE DOUE
// @file: TreatViewContent.php
// @date: 20260728 17:02:25
namespace IGK\Core\View;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Helper\ViewHelper;
use IGK\System\Compilers\BalafonCacheViewCompiler;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\IO\FileHandler;
use IGKEnvironmentConstants;
use TypeError;

/**
 * 
 * @package IGK\Core\View
 * @author C.A.D. BONDJE DOUE
 */
class TreatViewContent
{
    /**
     * 
     * @param BaseController $ctrl 
     * @param string $file 
     * @param bool $no_cache 
     * @return null|mixed 
     */
    public function treat(BaseController $ctrl, string $file, $no_cache = false, ...$params)
    {
       // igk_wln_e('not expired. treat .... sd', ob_get_level());
        $ext = igk_io_path_ext($file);
        $handler = null;
        $args = array_slice(func_get_args(), 3);
        $cache = igk_cache()::view();
        $key = IGKEnvironmentConstants::VIEW_FILE_CACHES;
        igk_environment()->push($key, $file);
        if (!in_array($ext, ['phtml', 'pinc'])) {
            // + | handling response from file handler
            if (($handler = \IGK\System\IO\FileHandler::GetFileHandlerFromExtension('.' . $ext)) instanceof FileHandler) {
                $response = $handler->transform(file_get_contents($file), (object)['ctrl' => $ctrl, 'raw' => ViewHelper::GetViewArgs('data')]);
                $target = $ctrl->getTargetNode();
                if ($response instanceof HtmlItemBase)
                    $target->add($response);
                else if ($response) {
                    $target->text($response);
                    // igk_wl($response);
                }
                igk_environment()->pop($key);
                return;
            }
        }
        $_bindfc = (function () {
            if ((func_num_args() >= 2) && (is_array(func_get_arg(1)))) {
                extract(igk_extract_ref(func_get_arg(1)));
            }
            // + | include view file.
            extract(igk_extract_ref(call_user_func_array([$this, 'getExtraArgs'], [])), EXTR_SKIP);
            return include(func_get_arg(0));
        })->bindTo($ctrl);
        
        if ($no_cache) {
            array_unshift($args, $file);
        } else {
            $_f = $cache->getCacheFilePath($file);
            $_bindfc = BalafonCacheViewCompiler::GetBindViewCompilerHandler($ctrl);
            if ($cache->cacheExpired($file)) {
                // + | ---------------------------------------------------------------
                // + | Build cache view from article file 
                // + | 
                $output = BalafonCacheViewCompiler::Compile($ctrl, $file, $args);
                igk_io_w2file($_f, $output);
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($_f, true);
                    opcache_invalidate($file, true);
                    opcache_invalidate(__FILE__, true);
                }
            }
            array_unshift($args, $_f);
        }
        $response = null;
        try {
            $response = $_bindfc(...$args);
        } catch (TypeError $ex) {
            igk_dev_wln_e("fatal error: " . $ex->getMessage());
            throw $ex;
        } catch (Exception $ex) {
            if (!igk_environment()->no_handle_error && igk_environment()->isDev() && !defined("IGK_TEST_INIT")) {
                $src = $ex->getFile();
                $msg = $ex->getMessage();
                igk_ilog("INC VIEW ERROR: " . $msg);
                $rp = realpath(igk_environment()->last($key));
                $code = $ex->getCode();
                if ($code) {
                    igk_set_header($code); 
                }
                igk_environment()->isDev() && igk_dev_wln_e(
                    implode("\n", [
                        "<html>",
                        "<head><title>Error  : " . $code . "</title></head>",
                        "<body>",
                        "<h2>INC VIEW ERROR</h2>" . $rp,
                        "<div>" . $ex->getMessage() . "</div>",
                        $rp == $src ? $src. ":" . $ex->getLine() : '',
                        implode("<br />", array_map(function ($e) use ($src) {
                            $file = igk_getv($e, "file");
                            $line = igk_getv($e, "line");
                            if ($src == $file) {
                                return "__CACHE__:" . basename($file) . "." . $line;
                            }
                            return implode(":", [empty($file) ? null : igk_io_collapse_path($file) . ':' . $line]);
                        }, $ex->getTrace())),
                        "</body>",
                        "</html>"
                    ])
                );
            }
            ob_end_clean();
            throw $ex;
        } finally {
            igk_environment()->pop($key);
        }
        return $response;
    }
}
