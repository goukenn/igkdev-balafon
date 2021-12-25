<?php


//----------------------------------------------------------------------------
// | default uri handler
//----------------------------------------------------------------------------

use IGK\Helper\IO;
use IGK\Resources\R; 
use function igk_resources_gets as __;


IGKRoutes::Register("^/favicon.ico[%q%]", function(){  
    igk_set_header(200, 
        "ok",
        ["Content-Type: image/png",
        "Cache-Control: max-age=31536000"]
    );
    // header("Content-Type: image/png");
    include(IGK_LIB_DIR."/Default/R/Img/balafon.ico");
    igk_exit();
}
, 1); 
//+ | -----------------------------------------------------------------------------------
//+ | asset balafon preloader
//+ | uri: /assets/Scripts/balafon.js
//+ | ----------------------------------------------------------------------------------- 
IGKRoutes::Register("^/".IGK_RES_FOLDER."/".IGK_SCRIPT_FOLDER."/balafon.js[%q%]", function(){
    $_igk = igk_app();
    $doc= $_igk->Doc;
    if(!$doc){
        igk_set_header(404);
        igk_exit();
    } 
    session_write_close(); 
    $generate_source = "igk_sys_balafon_js";  

    $accept = igk_server()->accepts(["gzip", "deflate"]);
    if (!$accept){
        $src = $generate_source($doc);
        igk_clear_header_list(); 
        header("Content-Type: application/javascript; charset= UTF-8");
        header("Content-Encoding: txt");
        echo $src;
        igk_exit();
    } 
    $sf=igk_core_dist_jscache();
    $resolver=IGKResourceUriResolver::getInstance(); 
    if(file_exists($sf)){
        $resolver->resolve($sf);
        igk_header_set_contenttype("js");
        header("Content-Type: application/javascript; charset= UTF-8");   
        header("Content-Encoding:deflate"); 
        echo file_get_contents($sf); 
        igk_exit();
    }
   
    $src = $generate_source($doc, igk_getr('d'));
    $type = 0;
    if ($_igk->Configs->core_no_zipjs){
        header("Content-Type: application/javascript; charset= UTF-8");
        header("Content-Encoding:deflate");  
        igk_wl($src);
        igk_exit();    
    }
 
    ob_start();
    igk_zip_output($src, 0, $type);
    $c= ob_get_clean(); 
    igk_io_w2file($sf, $c);
    igk_hook(IGKEvents::HOOK_CACHE_RES_CREATED, array("dir"=>$sf, "type"=>"js", "name"=>"balafonjs")); 
    igk_header_set_contenttype("js");
    header("Content-Encoding:deflate");
    igk_header_cache_output(3600 * 24 * 365);   
    igk_wl($c); 
    unset($s, $c);   
    igk_exit();
}
, 0);
IGKRoutes::Register("^/!@res/".IGK_SCRIPT_FOLDER.IGK_REG_ACTION_METH, function($fc, $arg){
    // igk_wln_e("handle ... ".igk_io_request_uri(). " - ".igk_env_count(__FUNCTION__));
    $doc = igk_get_last_rendered_document();
    if(!$doc){
        igk_wln_e("last rendered document is null");
        igk_set_header(500);
        igk_set_error(__FUNCTION__, "no document  found");
        return;
    }
    $fc=igk_getv(explode("?", $fc), 0);
    $key="scripts/".$fc;
    $dir=$doc->getParam($key);
    session_write_close();
    $strict=igk_getr("strict");
    if(empty($dir)){
        igk_set_header(500);
        igk_exit();
    }
    $load_js_res=is_dir($dir) && !$arg;
    if(!$load_js_res && (count($arg) > 0) && file_exists($file=$dir."/".implode("/", $arg))){
        igk_render_resource($file);
        igk_exit();
    }
    if($load_js_res && ($g=igk_io_getfiles($dir, "/\.js$/i"))){
        $bdir=igk_io_fullpath2fulluri($dir);
        sort($g);
        $header="/* name: ".$fc;
        $header .= " */".IGK_LF;
        $o="\"use strict\";".IGK_LF;
        $o .= "(function(){ var _loc_bdir='{$bdir}'; function getLocalScriptUri(){ return _loc_bdir; }; ".IGK_LF;
        foreach($g as $f=>$n){
            $s=str_replace("\"use strict\";", "", igk_io_read_allfile($n));
            if(empty($s))
                continue;
            $ui=igk_io_fullpath2fulluri($n);
            $o .= "(function(){ var _loc_uri='{$ui}'; ".$s."})();";
        }
        $o .= "})();";
        igk_header_no_cache();
        ob_clean();
        header("Content-Type: text/javascript");
        ob_start();
        igk_zip_output($header.igk_js_minify($o));
        $c=ob_get_contents();
        ob_end_clean();
        igk_wl($c);
        igk_flush_data();
        $v_jsdir=igk_io_cacheddist_jsdir();
        $file=$v_jsdir."/".$fc.".js";
        IO::CreateDir($v_jsdir);
        igk_server()->REQUEST_URI="/";
        $access="";
        $notresolved=0;
        if(($f=IGKResourceUriResolver::getInstance()->resolveOnly($v_jsdir, $notresolved)) || !$notresolved){
            igk_hook(IGKEvents::HOOK_CACHE_RES_CREATED, array("dir"=>$f, "type"=>"js"));
        }
        igk_io_w2file($file, $c);
        igk_exit();
    }
    else{
        igk_set_header(404);
        if(igk_is_ajx_demand()){
            igk_wl("[BJS] - Entry Script directory not found :".$fc);
            igk_exit();
        }
    }
    $doc->setParam($key, null);
    igk_exit();
}
, 0);
igk_reg_hook(IGKEvents::HOOK_CACHE_RES_CREATED, function($e){
    $fdir= igk_io_cacheddist_jsdir();
    $access=$fdir."/.htaccess";
    if(!file_exists($access)){
        IO::CreateDir(dirname($access));
        igk_io_w2file($access, implode("\n", array(
                "allow from all",
                "AddType text/javascript js",
                "AddEncoding deflate js",
                "<IfModule mod_headers.c>",
                "Header set Cache-Control \"max-age=31536000\"",
                "</IfModule>"
        )));
    }
    $sdir = dirname($e->args["dir"]); 
    $core_res_regex = "/\.(json|xml|jpeg|png|svg)$/i";
    if ($scripts = igk_environment()->get("ScriptFolder"))
    {
        $lib_res = IGK_LIB_DIR."/Scripts/";
        foreach($scripts as $d){
            foreach(igk_io_getfiles($d, $core_res_regex) as $res){
                if (strpos($res, $lib_res)===0){
                    $bres = $sdir."/".substr($res, strlen($lib_res));
                    if (IO::CreateDir(dirname($bres))){
                        igk_io_symlink(realpath($res),$bres);
                    }
                }
            }
        }
    } 
    igk_internal_reslinkaccess();
});
igk_reg_hook("generateLink", function(){
    igk_internal_reslinkaccess();
});
IGKRoutes::Register("^/".IGK_RES_FOLDER."/".IGK_SCRIPT_FOLDER.IGK_REG_ACTION_METH."[%q%]", function($fc, $arg){
    switch($fc){
        case "Lang":
        header("Access-Control-Allow-Origin: ".igk_getv($_SERVER, "HTTP_ORIGIN", "*"));
        header("Access-Control-Allow-Headers: igk-from,igk-x-requested-with");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        igk_set_env("sys://headers/ignorelist", array(
                "Access-Control-Allow-Origin",
                "Access-Control-Allow-Headers",
                "Access-Control-Allow-Methods"
            ));
        if($m=igk_server()->REQUEST_METHOD != "OPTIONS"){
            $arg=is_array($arg) ? igk_getv($arg, 0): $arg;
            $file=IGK_LIB_DIR."/Scripts/{$fc}";
            if(file_exists($file)){
                igk_io_render_res_file($file, $arg);
            }
            else{
                igk_set_header(404);
            }
        }
        break;
    }
    igk_exit();
}); 

IGKRoutes::Register("^/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/balafon.css[%q%]", function($m=null){
 
    if(defined("IGK_FORCSS"))
        return;   
    defined("IGK_FORCSS") || define("IGK_FORCSS", 1);
    defined("IGK_NO_WEB") || define("IGK_NO_WEB", 1);  
   

    // $d="/".IGK_RES_FOLDER."/".IGK_STYLE_FOLDER."/balafon.css.php";
    // + | change to application directory   
    try{
        igk_css_balafon_index(igk_io_basedir());  
    }catch(Exception $ex){
        header("Content-Type: text/css");
        if (igk_environment()->is("DEV")){
            echo "/* Exception : ".$ex->getMessage(). "*/\n";
        }
        echo "body{background-color: red !important; }";
    }
    igk_exit();
}
, 1); // change here 0 to 1.
IGKRoutes::Register("^/!/lib/(:path+)[%q%]", function($path, $version=null){
    if(is_array($path))
        $path=IGK_LIB_DIR."/".implode("/", $path);
    else
        $path=IGK_LIB_DIR."/".$path;
    /// TASK : Allowed file extension from lib directory

    $allowed=preg_match("/\.(js|css|xml|txt|bmp|png|svg|jpeg|jpg|xsl|pdf|md)$/", $path);
    if(file_exists($path) && $allowed){
        igk_header_content_file($path);
        igk_header_cache_output();
        igk_zip_output(igk_io_read_allfile($path));
        igk_exit();
    }
    else{
        if(!$allowed){
            igk_set_header(403);
            igk_text(';-) 403');
            igk_exit();
        }
    }
    igk_set_header(404);
    igk_exit();
}
, 1);
IGKRoutes::Register("^/robots.txt$", function(){

    $headers = [];
    $a = IGKServer::getInstance()->HTTP_USER_AGENT;    
 
    //if (preg_match("/Chrome-Lighthouse/", $a)){      
    //}
    // if (!preg_match("/".implode("|",
    // ["HTTPie",
    // "Googlebot"])."/", $a))
    //     return 0;
    $f = implode(DIRECTORY_SEPARATOR,  [igk_io_sys_datadir(), "robot.txt"]);
    if (file_exists($f)){
        include($f);
        igk_exit();
    } 
    // disallow all
    igk_set_header(200, "Content-Type:text/plain; charset=UTF8", $headers);
    // igk_header_cache_output(3600*24*365);    
    igk_text(implode("\n",[
        "user-agent: *",
        "allow: /b"
    ]));
    igk_exit();
}, 1);

IGKRoutes::Register("^/(index\.php/)?\{(:guid)\}(/(:path+))?[%q%]", function($guid, $query=null, $version=null){

     
    igk_header_no_cache();
	$uri= igk_io_request_entry();
    $key= igk_get_component_uri_key($guid);
    $tab=igk_app()->session->regUris;
    $handle=false;
	$routes = igk_app()->session->Routes;
	$index = array_search($key, $routes);
	$obj = null;
    $args=$query;
    if(is_string($query))
        $args=explode("/", $query);
	if (!empty($index)){
		$obj["class"] = $index;
	}else if($tab && isset($tab[$key])){
        $obj=$tab[$key];
	}
    if(is_array($obj)){
        $tclass=  explode("/::", $obj["class"]);
        $class = array_shift($tclass); 
        $tclass = implode("" , $tclass);
        
        if (strpos($class, "m:")===0)
        {
            $mod = str_replace(".", "\\", substr($class, 2));
            $mod_instance = igk_require_module($mod);
            $method= igk_getv($args, 0, "handle");
            $args=array_slice($args, 1);
            if ($ob = call_user_func_array([$mod_instance, $method], $args)){
                igk_do_response($ob);
            }
            igk_set_header(500);
	        igk_wln_e(__("failed to handle module action")); 

        }
        // if ($ctrl=igk_getctrl(IGK_CONF_CTRL, false)){
            //     $ctrl::register_autoload();
            // }
            if(!class_exists($class)){
                igk_set_header(500, "temp class not found");
                igk_wln_e("class not exists {$class} ", $tclass, $obj, $index, "routes", $routes);
            }
            if (is_subclass_of( $class, BaseController::class)
             && ($ctrl = igk_getctrl($class, false))){           
                $ctrl::register_autoload();
             }
            else{
                $tclass = null;
                $ctrl = new $class();
            }
            
            $method="index";
            
            R::RegLangCtrl($ctrl);

            if (!empty($tclass) && 
                class_exists($tclass))
            {
                $cl = new $tclass($ctrl);
            }
            else {
                $cl= $ctrl; //new $class();
            } 
            if(count($args) > 0){
                if(method_exists($cl, $args[0])){
                    $method=$args[0];
                    $args=array_slice($args, 1);
                }
            }
            if(method_exists($cl, $method)){
                ob_start();
                if (!igk_do_response($ob = call_user_func_array(array($cl, $method), $args))){
                    igk_wl(ob_get_clean());
                } else {
                    ob_end_clean();                
                }
            }
            else{
                igk_wln("method not found");
                igk_set_header(500, "function not found");
            } 
            igk_exit();
        }
        $cl = null;
        $b=json_decode($tab[$key]);
        if ($b)
         $cl=$b->classpath;
 
        if(!empty($cl) && class_exists($cl, false) && !empty($query)){
            $g=new $cl($b);
            $args=explode("/", $query);
            ob_start();
            $ob=call_user_func_array(array($g, $args[0]), array_slice($args, 1));
            ob_end_clean();
            igk_wl($ob);
        }

    if(igk_getr("__clear")){
        igk_app()->session->regUris=null;
    }
    igk_set_header(500);
    if (igk_environment()->is("DEV")){
	    igk_wl_e(__("failed to handle component action"));
    }
    igk_exit();
}
, 0);