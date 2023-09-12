<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ExceptionUtils.php
// @date: 20220803 13:48:58
// @desc: 


namespace IGK\Helper;

use Exception;
use IGK\Resources\R;
use IGKResourceUriResolver;

/**
 * 
 */
abstract class ExceptionUtils
{
    /**
     * show exception 
     */
    public static function ShowException(Exception $Ex, $file = null, $line = null, $title = null)
    {
        // + | --------------------------------------------------------------------
        // + | show exception 
        // + |
        // igk_trace();
        // igk_wln_e("handler ...");
        // $ex_output = "";
        // $ex_output .= "<h2>" . $ex->getMessage() . "</h2>";
        // echo $ex_output;  
        if (!$Ex)
            return;
        $content = "";
        $traces = $Ex->getTrace();
        if (igk_is_cmd()){
            igk_show_trace();
            $out = "";
            $out .= "/!\\ ".IGK_PLATEFORM_NAME."-ERROR\n" . IGK_LF;
            if ($title) {
                $out .= $title . IGK_LF;
            }
            $out .= "Message: " . $Ex->getMessage() . IGK_LF;
            $out .= "File: " . $Ex->getFile() . IGK_LF;
            $out .= "Line: " . $Ex->getLine() . IGK_LF;
            $out .= igk_get_exception_eval($Ex, $traces);
            igk_wl($out);
            return;
        }
        if (igk_is_class_subclass_of($Ex, \IGK\System\Http\RequestException::class)) {
            $error = new \IGK\System\Http\ErrorRequestResponse($Ex->getCode());
            $error->message = [
                "request_uri" => igk_io_request_uri(), 
                "display" => $Ex->getMessage(),
            ];
            echo $error->render();
            igk_exit();
        }
        $tab = array();
        $tab["fr"]["title.fatalError"] = "Not found";
        $tab["fr"]["go.home"] = "Accueil";
        $tab["en"]["title.fatalError"] = "Not found";
        $tab["en"]["go.home"] = "Home";
        $r = function ($s) use ($tab) {
            $lg = "en";
            if ($m = strtolower(R::GetCurrentLang())) {
                if (isset($tab[$m]))
                    $lg = $m;
            }
            return $tab[$lg][$s];
        };
        $trace_css = "";
        $trace_css .= igk_io_read_allfile(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/exception.css");
        $trace_css .= igk_io_read_allfile(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/trace.css");
        if ((get_class($Ex) == IGKException::class) || is_subclass_of($Ex, IGKException::class)) {
            if (!($s = $Ex->getCode())) {
                $s = 500;
            }
            igk_set_header($s);
        }
        $content = include(IGK_LIB_DIR . "/Inc/exceptions/content.phtml");
        $balafon_js = "";
        $balafon_src = "";
        $corejs = "";
        if (igk_environment()->isOPS()) {
            $balafon_js = igk_io_corejs_uri();
            $balafon_src = igk_sys_balafon_js();
            $corejs = '<script language="javascript" type="text/javascript" id="balafon.js">' . $balafon_src . '</script>';
        } else {
            $balafon_js = IGKResourceUriResolver::getInstance()->resolve(IGK_BALAFON_JS_CORE_FILE);
            $corejs = '<script language="javascript" type="text/javascript" src="' . $balafon_js . '"></script>';
        }
        $style_link = "";
        if (file_exists($d = igk_io_resourcesdir() . "/Fonts/google/Roboto100,200,400,700,900.css")) {
            $style_link .= "<!-- style link  -->";
            $style_link .= "<link rel=\"stylesheet\" href='" . igk_uri(igk_io_baseuri() . "/" . igk_io_baserelativepath($d)) . "'/>";
            $style_link .= "<!-- end:style link  -->";
            $trace_css .= "body h1{ font-family: 'Roboto', arial, sans-serif; font-weight: 100; }";
        }
        $doc = 0;
        $scripts = <<<EOF
{$corejs}{$style_link}

<script language="javascript" type="text/javascript">
	(function(){
	window.igk_init=function(){
		var q = document.getElementById("tracelist");
		if (!q)return;
		if (!window.igk){
			q.className = q.className+" igk-active";
			return;
		}
		var h = \$igk(q).select('#hd').getItemAt(0).o;
		h.onclick = function(){
			if (!/igk-active/.test(q.className)){
				\$igk(q).addClass("igk-active");
			}
			else{
				//remove
				\$igk(q).rmClass("igk-active");
			}
		};
	};

	//+ igk-tracelist
	(function(){
        if (typeof (window.ns_igk) != 'undefined'){
            ns_igk.ready(function(){
                \$igk(".igk-tracelist").each_all(function(){
                    this.o.scrollTop = 0;
                });
            });
        }
	})();

})();
</script>
EOF;

        if (igk_is_ajx_demand()) {
            $doc = <<<EOF
{$scripts}
<style type="text/css">
{$trace_css}
</style>
<div class='error_view' style="padding-bottom:164px;" >
{$content}
</div>
<script language="javascript" type="text/javascript">window.igk_init(); </script>
EOF;
        } else {
            $doc = <<<EOF
<!DOCTYPE html >
<html>
	<head>
	<title>{$r('title.fatalError')}</title>
    {$scripts}
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Material+Icons&display=swap" rel="stylesheet" />
<style>
{$trace_css}
</style>
</head>
<body class="google-Roboto exception igk-show-exception" onload="javascript:window.igk_init(); return false;">
{$content}
</body>
</html>
EOF;
        }
        if (ob_get_level() > 0)
            ob_end_clean();
        header("Content-Type: text/html");
        header("Cache-Control: no-cache");
        igk_wl($doc);
        igk_exit();
    }
}
