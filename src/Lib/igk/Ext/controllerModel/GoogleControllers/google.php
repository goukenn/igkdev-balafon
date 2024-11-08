<?php
// @file: google.php
// @author: C.A.D. BONDJE DOUE
// description: BALAFON google's utility function. fonts, some components
// version: 1.0
// annotation: none, vertical-bubble, bubble
// default: 

use IGK\Core\Ext\Google\GoogleAPIEndPoints;
use IGK\Core\Ext\Google\GoogleEvents;
use IGK\Core\Ext\Google\IGKGoogleCssUri as GoogleCssUri;
use IGK\Core\Ext\Google\IGKHrefListValue as IGKHrefListValue;
use IGK\Helper\ViewHelper;
use IGK\System\Regex\Replacement;

use function igk_resources_gets as __;
use function igk_curl_post_uri as post_uri;


if (defined('GOOGLE_MODULE')) {
    return;
} else {
    require_once(__DIR__ . "/Lib/Classes/IGKHrefListValue.php");
    require_once(__DIR__ . "/Lib/Classes/IGKGoogleCssUri.php");
    require_once(__DIR__ . "/Lib/Classes/IGKHrefListValue.php");
    require_once(__DIR__ . "/Lib/Classes/GoogleEvents.php");
    require_once(__DIR__ . "/Lib/Classes/GoogleAPIEndPoints.php");
    define('GOOGLE_MODULE', 1);
    define("GOOGLE_URI_REGEX", "/url\s*\((?P<link>[^)]+)\)/");
    define("GOOGLE_SETTINGS_FILE", dirname(__FILE__) . "/Data/configs.json");
    define("IGK_GOOGLE_DEFAULT_PROFILE_PIC", "//lh3.googleusercontent.com/uFp_tsTJboUY7kue5XAsGA=s120");

    /**
     * set theme definition
     * @param mixed $theme 
     * @param mixed $family 
     * @return void 
     */
    function igk_google_css_setfont(&$theme, $family, $extra = "sans-serif")
    {
        $rp = new Replacement;
        $rp->add("/\s+/", " ");
        $rp->add("/[^a-z0-9]+/i", "-");
        $key = $rp->replace($family);
        $n = str_replace(" ", "-", $family);
        if (is_string($extra) && !empty(trim($extra)))
            $extra = ", " . $extra;
        $theme[".google-" . $key] = "font-family:'{$n}'{$extra};";
    }
    ///<summary>add google font to theme</summary>
    ///<param name="document"> document to attach the google font  </param>
    ///<param name="family"> Font's Name</param>
    ///<param name="sizes" default="100;200;400;700;900"> semi - column separated size</param>
    ///<param name="temp" default="1"> attached temporaly</param>
    ///<exemple>igk_google_addfont($doc, 'Roboto');</exemple>
    /**
     * add google font to theme
     * @param mixed $IGKHtmlDoc$doc  document to attach the google font
     * @param mixed $string$family  Font's Name
     * @param mixed $string$size  semi - column separated size
     * @param mixed $bool$temp  attached temporaly
     */
    function igk_google_addfont($doc, $family, $size = null, $temp = 1, $extra = 'sans-serif')
    {
        $size = igk_google_get_font_sizes($family, $size);
        $g = trim($family);
        if (empty($g)) {
            igk_die("font name is empty");
        }
        $key = igk_google_font_api_uri($g, $size);
        $head = $doc->getHead();
        $no_local_install = $doc->noFontInstall || (!igk_server_is_local());
        if ($no_local_install) {
            $head->addDeferCssLink($key, $temp);
        } else {
            $head->addDeferCssLink((object)['callback' => 'igk_google_local_uri_callback', 'params' => [$key, $family], 'refName' => $key], $temp);
        }
        $theme = igk_environment()->isOPS() ?
            $doc->getInlineTheme() :
            $doc->getTheme();
        if ($theme)
            igk_google_css_setfont($theme->def, $family, $extra);
        IGKEvents::hook(GoogleEvents::init_component, "font");
    }
    function igk_google_bindfont($theme, $family, $size = null)
    {
        $g = trim($family);
        if (empty($g)) {
            igk_die("font name is empty");
        }
        $size = igk_google_get_font_sizes($family, $size);
        $rp = new Replacement;
        $rp->add("/\s+/", " ");
        $rp->add("/[^a-z0-9]+/i", "_");
        $n = $rp->replace($family);
        $theme->def[".google-" . $n] = "/* binding ext */ font-family: '{$family}', sans-serif;";
    }
    /**
     * 
     * @param mixed $family 
     * @param mixed $size 
     * @return mixed 
     * @throws Exception 
     */
    function igk_google_get_font_sizes($family, $size = null)
    {
        if ($size === null) {
            $defaultsize = '100;200;400;700;900';
            $familysizes = [
                "roboto" => '100;400;700;900',
                "material+icons" => "400"
            ];
            $size = igk_getv($familysizes, strtolower($family), $defaultsize);
        }
        return $size;
    }
    ///<summary>get the global google's application API_KEY</summary>
    /**
     * 
     */
    function igk_google_apikey()
    {
        return igk_configs()->{IGKGoogleConfigurationSetting::API_KEY};
    }
    ///<summary>get condensed family name for URI </summary>
    /**
     * get condensed family name for URI
     * @param mixed $string$family Font's name definition
     */
    function igk_google_condensedfamilyname($family)
    {
        $s = str_replace(" ", "", $family);
        $s = str_replace(":", "", $s);
        $s = str_replace(";", "x", $s);
        return $s;
    }
    ///<summary>get local file path from family</summary>
    ///<param name="family">family name</param>
    /**
     * get local file path from family
     * @param mixed $family name
     */
    function igk_google_filefromfamily($family)
    {
        return igk_google_get_css_fontfile($family);
    }
    if (!function_exists('igk_google_font_api_uri')) {
        ///<summary>get google uri form</summary>
        /**
         * helper: get google uri form
         * @param ?string $n name of the font 
         * @param ?string $size name of the font 
         * @return string uri
         */
        function igk_google_font_api_uri(?string $n = null, ?string $size = null)
        {
            $s =  GoogleAPIEndPoints::CssEndPoint;
            if ($n) {
                $s .= "?family=" . str_replace(" ", "+", $n);
                if ($size) {
                    $s .= ":" . str_replace(";", ",", $size);
                }
            }
            return $s;
        }
    }
    ///<summary></summary>
    ///<param name="family"></param>
    /**
     * 
     * @param mixed $family
     */
    function igk_google_get_css_fontfile($family)
    {
        return igk_dir(igk_google_get_fontdir() . "/" . igk_google_condensedfamilyname($family) . ".css");
    }
    ///<summary></summary>
    ///<param name="folderid"></param>
    ///<param name="filename"></param>
    /**
     * 
     * @param mixed $folderid
     * @param mixed $filename
     */
    function igk_google_get_drive_uri($folderid, $filename)
    {
        return "//googledrive.com/host/" . $folderid . "/" . $filename;
    }

    if (function_exists("igk_curl_post_uri")) {
        ///<summary>download google font to file</summary>
        ///<return>array of files</return>
        /**
         * download google font to file
         */
        function igk_google_get_font($ft = "Open Sans", $dir = null)
        {
            igk_ilog("get font : " . $ft);
            $files = array();
            $ft = str_replace(" ", "+", $ft);
            $options = "";
            $url = igk_google_font_api_uri($ft, $options);
            $g = post_uri($url);
            if (preg_match_all(GOOGLE_URI_REGEX, $g, $tab) > 0) {
                $dir = $dir ?? igk_google_get_fontdir();
                $dir = "{$dir}/{$ft}";
                igk_io_createdir($dir);
                foreach ($tab["link"] as $v) {
                    if (isset($files[$v])) {
                        continue;
                    }
                    $files[$v] = igk_dir($dir . "/" . basename($v));
                }
            }
            igk_ilog(json_encode(["the output : ", $g]));
            return $files;
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    function igk_google_get_fontdir()
    {
        return igk_dir(igk_io_basedir() . "/" . IGK_RES_FOLDER . "/fonts/google");
    }
    function igk_google_data_dir()
    {
        return implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), IGK_DATA_FOLDER]);
    }

    if (function_exists("igk_curl_post_uri")) {
        ///<summary></summary>
        ///<param name="family"></param>
        ///<param name="sizes"></param>
        /**
         * 
         * @param mixed $family
         * @param mixed $sizes
         */
        function igk_google_installfont($family, $sizes, $file = null)
        {
            $fdir = igk_google_get_fontdir();
            if (!igk_io_createdir($fdir)) {
                return 0;
            }
            $uri = igk_google_font_api_uri($family, $sizes);

            $name = str_replace(" ", "+", $family);
            $_installdir = $fdir . DIRECTORY_SEPARATOR . $name;
            $result = array();
            foreach (explode(';', $sizes) as $k) {
                $huri = igk_google_font_api_uri($family, trim($k));
                $s = post_uri($huri . "&display=swap");
                $info = igk_curl_info();
                if (($ts = $info["Status"]) == 200) {
                    if (preg_match_all(GOOGLE_URI_REGEX, $s, $tab) > 0) {
                        $lnk = $tab["link"];
                        foreach ($lnk as $bs) {
                            $b = post_uri($bs);
                            $sr = str_replace(" ", "+", basename($bs));
                            igk_io_w2file($_installdir . "/" . $sr, $b);
                            $s = str_replace($bs, "./" . $name . "/" . $sr, $s);
                        }
                        igk_google_regfont($uri, $family);
                    }
                    $result[] = $s;
                } else {
                    igk_ilog("queryfailed: uri:" . $huri . " status:" . $ts);
                }
            }
            if (igk_count($result) > 0) {
                $cfam = igk_google_condensedfamilyname($family);
                if ($file === null)
                    $file = igk_google_filefromfamily($family);
                igk_io_w2file($file, implode("\n", $result));
                return 1;
            }
            return 0;
        }
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
     * 
     * @param mixed $n
     */
    function igk_google_jsmap_acceptrender_callback($n)
    {
        return 1;
    }
    ///<summary>convert google uri's font to App font resource</summary>
    /**
     * convert google uri's font to App font resource
     */
    function igk_google_local_uri_callback($uri, $e = null)
    {
        $s = igk_io_baseuri() . "/!@res//getgooglefont?q=" . base64_encode("uri=" . $uri . "&type=css");
        $file = "";
        $tab = [];
        parse_str(igk_getv(parse_url($uri), "query"), $tab);
        $family = igk_getv($tab, "family");
        $file = "";

        if (($file = igk_google_filefromfamily($family)) && file_exists($file)) {
            return new IGKHtmlRelativeUriValueAttribute($file);
        }
        if ($e !== null) {
            $f = igk_google_get_css_fontfile($family);
            return new IGKHrefListValue($s, new GoogleCssUri($f, $uri));
        }
        return $s;
    }
    ///<summary>register file that will respond to uri</summary>
    /**
     * register file that will respond to uri
     */
    function igk_google_regfont($uri, $family)
    {
        $s = igk_google_settings();
        $fonts = igk_conf_get($s, "fonts");
        if (!$fonts || !is_object($fonts)) {
            $fonts = new Stdclass();
            $s->fonts = $fonts;
        }
        $fonts->{$family} = $uri;
        $s->{"fonts"} = $fonts;
        igk_google_store_setting();
    }
    ///<summary>get google settings</summary>
    /**
     * get google settings
     */
    function igk_google_settings()
    {
        return igk_get_env("google://settings", function () {
            $v_file = GOOGLE_SETTINGS_FILE;
            $s = null;

            if (file_exists($v_file)) {
                $str = igk_io_read_allfile($v_file);
                $s = igk_json_parse($str) ?? igk_createobj();
            }
            return $s ?? igk_createobj();
        });
    }
    ///<summary>store balafon controller configuration</summary>
    /**
     * store balafon controller configuration
     */
    function igk_google_store_setting($setting = null)
    {
        $g = igk_google_settings();
        igk_io_w2file(GOOGLE_SETTINGS_FILE, json_encode($g ?? igk_google_settings(),  JSON_FORCE_OBJECT |  JSON_UNESCAPED_SLASHES));
    }

    if (function_exists("igk_curl_post_uri")) {
        ///<summary></summary>
        ///<param name="links"></param>
        ///<param name="download" default="1"></param>
        /**
         * 
         * @param mixed $links
         * @param mixed $download the default value is 1
         */
        function igk_google_zip_fontlist($links, $download = 1)
        {
            $zip = null;
            $temp = tempnam(sys_get_temp_dir(), "fxip");
            foreach ($links as $v) {
                $b = post_uri($v);
                if ($zip === null) {
                    $zip = igk_zip_content($temp, basename($v), $b, 0);
                } else {
                    $zip->addFromString(basename($v), $b);
                }
            }
            $zip->close();
            if ($download) {
                igk_download_file("fonts.zip", $temp);
                unlink($temp);
                return null;
            }
            return $temp;
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    function igk_google_zonectrl()
    {
        $CF = igk_ctrl_zone_init(__FILE__);
        return $CF;
    }
    ///<summary> init google zone </summary>
    /**
     *  init google zone
     */
    function igk_google_zoneinit($g)
    {
        $f = IGK_LIB_DIR . "/../api/google-api-client/vendor/autoload.php";
        require_once($f);
    }
    ///<summary></summary>
    ///<param name="t"></param>
    /**
     * 
     * @param mixed $t
     */
    function igk_html_demo_google_circle_waiter($t)
    {
        $dv = $t->div()->setStyle("height: 200px");
        $dv->div()->Content = __("Please wait...");
        $dv->addgoogleCircleWaiter()->setStyle("display:inline-block; height:100px; width:100%");
    }
    ///'https://local.com/Lib/igk/Ext/ControllerModel/GoogleControllers/Scripts/igk.google.maps.js'
    /**
     */
    function igk_html_demo_google_js_maps($t)
    {
        $n = $t->addGoogleJSMaps("{zoom:15,center:{lat:50.850402, lng:4.357879}}");
        $n->setStyle("height:300px;");
        $t->addCode()->Content = htmlentities(
            <<<EOF
\$t->addGoogleJSMaps("{zoom:15,center:{lat:50.850402, lng:4.357879}}");
EOF
        );
    }
    if (!function_exists('igk_html_demo_google_line_waiter')) {


        ///<summary></summary>
        ///<param name="t"></param>
        /**
         * 
         * @param mixed $t
         */
        function igk_html_demo_google_line_waiter($t)
        {
            $n = igk_html_node_google_line_waiter();
            $t->add($n);
            return $n;
        }
    }
    /**
     * bind google material icons
     * @param mixed $name 
     * @param string $title 
     * @param string $type 
     * @return object 
     * @throws ReflectionException 
     * @throws IGKException 
     */
    function igk_html_node_google_icon($name, $title = "", $type = "span", $class = "material-icons")
    {
        $n = igk_create_node($type);
        $n["title"] = $title;
        $n->setClass($class);
        $n->Content = $name;
        $n->setFlag("NO_CONTENT", 1);
        return $n;
    }

    if (!function_exists('igk_html_node_google_icon_outlined')) {
        function igk_html_node_google_icon_outlined($name, $title = "", $type = "span")
        {
            return igk_html_node_google_icon($name, $title, $type, 'material-icons-outlined');
        }
    }

    if (!function_exists('igk_html_node_hamburger_button_menu')) {
        function igk_html_node_hamburger_button_menu()
        {
            $n = igk_create_node('div');
            $n->google_icon('menu');
            return $n;
        }
    }

    ///<summary></summary>
    ///<param name="t"></param>
    /**
     * 
     * @param mixed $t
     */
    function igk_html_demo_google_mapgeo($t)
    {
        $t->div()->addPanelBox()->addCode()->Content = "\$t->addGoogleMapGeo(\"50.847311,4.355072\");";
        return $t->addGoogleMapGeo("50.847311,4.355072");
    }
    ///<summary></summary>
    /**
     * 
     */
    function igk_html_node_google_circle_waiter()
    {
        $n = igk_create_node();
        $n->setClass("igk-google-circle-waiter");
        return $n;
    }
    ///<summary>add google follows us button</summary>
    ///rel: author or publisher
    ///height: 15,20,24
    /**
     * add google follows us button
     */
    function igk_html_node_google_follow_us_button($id, $height = 15, $rel = "author", $annotation = "none")
    {
        $n = igk_create_xmlnode("g:follow");
        $n["class"] = "g-follow";
        $n["href"] = "https://plus.google.com/" . $id;
        $n["rel"] = $rel;
        $n["annotation"] = $rel;
        $n["height"] = $height;
        $b = igk_html_node_onrendercallback(igk_create_expression_callback(
            <<<EOF
\$doc = igk_getv(\$extra[0], "Document");
if (\$doc){
	\$d = \$doc->addTempScript('https://apis.google.com/js/platform.js',1);
	\$d->activate("async");
	return 1;
}
return 0;
EOF,
            array("n" => $n)
        ));
        $n->add($b);
        return $n;
    }
    ///<summary>add google maps javascript api node</summary>
    /**
     * add google maps javascript api node
     */
    function igk_html_node_google_js_maps($data = null, $apikey = null)
    {
        $apikey = $apikey ?? igk_google_apikey();
        $n = igk_create_node("div");
        $n["class"] = "igk-gmaps";
        $srv = igk_getv(igk_get_services("google"), "googlemap");
        $mapuri = $srv("apiuri", null, (object)["Google" => (object)["ApiKey" => $apikey]]);
        $n->setCallback("AcceptRender", "igk_google_jsmap_acceptrender_callback");
        $mapjs =  IGKResourceUriResolver::getInstance()->resolve(dirname(__FILE__) . '/Scripts/igk.google.maps.js');
        $n->script()->Content = <<<EOF
(function(q){
var b = ['{$mapuri}'];
if (!igk._\$exists('igk.google.maps'))
	b.push('{$mapjs}');
igk.js.require(b).promise(function(h){ns_igk.google.maps.initMap(h);}, q);

})(igk.getParentScript());
EOF;
        $n["igk:data"] = $data ?? "{zoom:7, center:{lat:50.41438075875331, lng:4.904006734252908}}";
        return $n;
    }

    function igk_google_init_css()
    {
        //google - bind local style
        if (!igk_get_env("google::init_global_style")) {
            igk_set_env("google::init_global_style", 1);
            $f = dirname(__FILE__) . "/Styles/igk.google.pgcss"; 
            $theme = ViewHelper::CurrentDocument()->getTheme();
            igk_css_bind_file($theme, null, $f);
        }
    }
    ///<summary></summary>
    /**
     * 
     */
    function igk_html_node_google_line_waiter()
    {
        $n = igk_create_node();
        $n->setClass("igk-google-line-waiter");
        igk_google_init_css();
        return $n;
    }
    ///<summary></summary>
    ///<param name="loc"></param>
    /**
     * 
     * @param mixed $loc
     */
    function igk_html_node_google_mapgeo($loc, $apikey = null)
    {
        $n = igk_create_node("div");
        $n["class"] = "igk-winui-google-map";
        $q = $loc;
        $key = $apikey ?? igk_google_apikey();
        $t = "place";
        $lnk = "https://www.google.com/maps/embed/v1/{$t}?key={$key}&q={$q}";
        $iframe = $n->addHtmlNode("iframe");
        $iframe->setClass("fitw");
        $iframe["frameborder"] = "0";
        $iframe["src"] = $lnk;
        $iframe["onerror"] = "event.target.innerHTML ='---failed to load map---';";
        return $n;
    }

    igk_reg_hook(GoogleEvents::init_component, function () {
        if (!igk_get_env("init_globalfont")) {
            igk_set_env("init_globalfont", 1);
            igk_css_reg_global_style_file(dirname(__FILE__) . "/Styles/igk.google.pgcss", null, null, 1);
        }
    });
    if (isset($_SERVER["REQUEST_URI"])) {
        IGKRoutes::Register(
            "^/!@res/(/)?google/cssfont" . IGK_REG_ACTION_METH . "[%q%]",
            function ($u, $f) {
                @igk_sess_write_close();
                $fdir = igk_google_get_fontdir();
                $file = igk_dir($fdir . $f);
                if (file_exists($file)) {
                    header("Content-Type:text/plain");
                    igk_header_content_file($f);
                    igk_wl(igk_io_read_allfile($file));
                    igk_exit();
                }
                $file = igk_dir($fdir . $u . ".xft");
                if (file_exists($file)) {
                    $g = igk_zip_unzip_entry($file, $f);
                    igk_header_content_file($f);
                    igk_wl($g);
                } else {
                    igk_set_header(400);
                }
                igk_exit();
            },
            1
        );


        if (function_exists("igk_curl_post_uri")) {
            IGKRoutes::Register(
                "^/!@res/(/)?getgooglefont[%q%]",
                function ($c) {
                    @igk_sess_write_close();
                    header("Content-Type: text/css");
                    if (is_array($c)) {
                        $c = igk_getv($c, "query");
                    }
                    $q = array();
                    parse_str($c, $q);
                    $uri = "";
                    parse_str(base64_decode($q["q"]), $q);
                    $uri = $q["uri"];
                    $type = igk_getv($q, "type", "css");

                    $file = "";
                    $tab = [];
                    parse_str(igk_getv(parse_url($uri), "query"), $tab);


                    $family = igk_getv($tab, "family");
                    $tab = explode(":", $family);
                    $f = igk_getv($tab, 0);
                    $g = igk_getv($tab, 1);
                    if (igk_count($tab) < 2) {
                        igk_ilog("not family:" . $family);
                    }
                    $sizes = implode(";", array_filter(explode(',', $g)));

                    $fdir = igk_google_get_fontdir();
                    igk_io_createdir($fdir);

                    if ($family) {
                        if (($file = igk_google_filefromfamily($family))) {

                            if (file_exists($file) || igk_google_installfont($f, $sizes, $file)) {
                                $ref = igk_io_baserelativepath($file);
                                $uri = igk_io_baseuri() . "/" . igk_uri($ref);
                                $tf = igk_io_basedir() . "/" . $ref;
                                if (file_exists($tf)) {
                                    igk_navto($uri);
                                } else {
                                    igk_set_header(RequestResponseCode::NotFound);
                                    igk_exit();
                                }
                            }
                            igk_set_header(500);
                            igk_exit();
                        } else { // install fonts
                            if (igk_is_webapp() || !igk_sys_env_production()) {
                                igk_google_installfont($family, $sizes);
                                header("Content-Type: text/css");
                                igk_set_header(500);
                                igk_wl("/* Can't install google's font to server */");
                                igk_exit();
                            } else {
                                header("Content-Type: text/css");
                                igk_set_header(500, "text/css");
                                igk_wl("/* googlefont not on webapp context {$file} */");
                            }
                        }
                        echo "/* done */";
                        igk_exit();
                    }
                    $guri = $uri . "&display=swap";
                    $s = post_uri($guri);
                    $info = igk_curl_info();
                    if ($info["Status"] != 200) {
                        igk_exit();
                    }
                    if ($s) {
                        if (preg_match_all(GOOGLE_URI_REGEX, $s, $tab) > 0) {
                            $dir = igk_google_get_fontdir();
                            igk_io_createdir($dir);
                        }
                        ob_clean();
                        header("Content-Type: text/css");
                        igk_zip_output($s);
                    } else {
                        igk_ilog("failed to load, " . __FILE__ . ":" . __LINE__);
                    }
                    echo "/* done */";
                    igk_exit();
                },
                1
            );
        }
    }

    igk_register_service("google", "googlemap", function ($cmd, $t, $config = null) {
        switch ($cmd) {
            case "apiuri":
                $c = igk_conf_get($config, "Google/ApiKey");
                return "https://maps.googleapis.com/maps/api/js?key=" . $c;
            case "apikey":
                break;
        }
        return null;
    });
    igk_sys_reg_referencedir(__FILE__, igk_dir(dirname(__FILE__) . "/Data/References"));


    // components
    if (!function_exists('igk_html_node_google_oauth_link')) {

        function igk_html_node_google_oauth_link($tab)
        {
            $n = igk_create_node("a");
            $list = [
                "client_id" => 1,
                "redirect_uri" => 1,
                "response_type" => 1,
                "scope" => 1,
                "access_type" => 1,
                "state" => 1,
                "include_granted_scopes" => 0,
                "login_hint" => 0,
                "prompt" => 0
            ];
            $q = [];
            foreach ($list as $k => $v) {
                if (!array_key_exists($k, $tab)) {
                    if ($v)
                        igk_die("require parameter not present : " . $k);
                    continue;
                }
                $q[$k] = $tab[$k];
            }
            $n["href"] = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query($q);
            return $n;
        }
    }

    if (!function_exists('igk_html_node_google_oth2_button')) {
        function igk_html_node_google_oth2_button($url, $gclient)
        {
            $n = igk_create_node("a");
            $q = http_build_query([
                "client_id" => $gclient->client_id,
                "redirect_uri" => $url,
                "response_type" => "code",
                "access_type" => "online",
                "scope" => $gclient->scope,
                "state" => "crefinfo"
            ]);
            $n["href"] = $gclient->authinfo()->authorization_endpoint . "?" . $q;
            return $n;
        }
    }

    /**
     * add google recaptcha
     * @param ?string $siteKey 
     * @return HtmlItemBase 
     * @throws IGKException 
     */
    function igk_html_node_google_recaptcha(?string $siteKey = null)
    {
        static $renderActions;
        if (!$renderActions) {
            igk_reg_hook(IGKEvents::HOOK_HTML_BEFORE_RENDER_DOC, function ($e) use ($siteKey) {
                $siteKey = \IGK\Helper\ConfigHelper::GetConfig(ViewHelper::CurrentCtrl(), "google.recaptcha_key", $siteKey) ??
                    igk_die("no recaptcha");
                $doc = $e->args['doc'];
                $doc->head->script()->setId("repatcha")
                    ->activate('defer')
                    ->setAttribute("src", GoogleEndPoints::RecaptchaEnterprise . "?hl=" . GoogleEndPoints::GetLang());
            });
            $renderActions = true;
        }
        $siteKey = \IGK\Helper\ConfigHelper::GetConfig(ViewHelper::CurrentCtrl(), "google.recaptcha_key", $siteKey) ??
            igk_die("no recaptcha");
        $n = igk_create_node('div');
        $n->setAttributes([
            "class" => 'g-recaptcha',
            'data-sitekey' => $siteKey,
            "data-theme" => 'dark', // set data theme
            "data-callback" => null, // callback function 
            "data-error-callback" => null, // callback error callback  
        ]);
        return $n;
    }
}
