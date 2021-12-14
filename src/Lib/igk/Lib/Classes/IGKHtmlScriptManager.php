<?php
///<summary>Represente class: IGKHtmlScriptManager</summary>
/**
* Represente IGKHtmlScriptManager class
*/
final class IGKHtmlScriptManager extends IGKObject {
    const DOC_FLAG=3;
    const JSMAN_ASSOC_TABLE_FLAG=2;
    const JSMAN_NODE=4;
    const MANAGER_FLAG=1;
    const SCRIPT_ITEM_MANAGER=5;
    const TEMPORARY_SCRIPT=6;
    private $_f;
    ///<summary></summary>
    ///<param name="owner"></param>
    /** 
    * @param mixed $owner
    */
    public function __construct(){
        $this->_f=new IGKFv(); 
    }
    ///<summary>display value</summary>
    /**
    * display value
    */
    public function __toString(){
        return get_class($this);
    }
    ///clear cache if script not loaded correctly
    ///<summary>register script to script manager</summary>
    /**
    * register script to script manager
    * @param string $file server script file
    * @param bool $canbeMerged serve on production allow to merge on system rendering script
    * @param string $tag 'priv'| or any tag to identify associate script
    */
    public function addScript($file, $canbeMerged=true, $tag='priv'){  
        // igk_trace();
        // igk_wln_e(__METHOD__, $file.":".igk_sys_request_time());
        // return;

        $local=false;
        $fname=true;
        $f=$file;
        if(!IGKValidator::IsUri($file)){
            $fname=false;
            $f=$file;
            if(file_exists($f)){
                $file=igk_html_uri(igk_realpath($f));
                $local=true;
            }
            else{
                $local=false;
            }
        }
        $tasc=$this->getAssoc();
        if(isset($tasc[$file])){
            return $tasc[$file];
        }
        $g=!$fname && !$local;
        $tasc[$file]=[
            "merge"=>$canbeMerged ? 1: 2,
            "tag"=>$tag
        ];
        $this->_f->updateFlag(self::JSMAN_ASSOC_TABLE_FLAG, $tasc);
        return $tasc[$file];
    }
    ///<summary>current script to new document</summary>
    /**
    * current script to new document
    */
    public function bindScriptTo($document){
        if($document == null)
            return;
        $tasc=$this->getAssoc();
        if($tasc) foreach($tasc as $k=>$v){
            list($t, $m) = igk_array_fill(explode(";", $v), 2);
            $document->addScript($k, $t, $m);
        }
    }
    ///<summary>clear loaded script</summary>
    /**
    * clear loaded script
    */
    public function Clear($tag=null){
        if($tag == null){
            $this->m_assocTable=array();
            $this->m_node->ClearChilds();
            return;
        }
        $t=array();
        foreach($this->m_assocTable as $k=>$v){
            if($v->tag == $tag){
                igk_html_rm($v);
                continue;
            }
            $t[$k]=$v;
        }
        $this->m_assocTable=$t;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function Flags(){
        return $this->_f;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getAssoc(){
        if(($i=$this->getFlag(self::JSMAN_ASSOC_TABLE_FLAG)) == null){
            $i=new IGKHtmlScriptAssocInfo();
            $this->_f->setFlag(self::JSMAN_ASSOC_TABLE_FLAG, $i); 
        }
        return $i;
    }    
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="default" default="null"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $default the default value is null
    * @return mixed 
    */
    public function getFlag($n, $default=null){
        return $this->_f->getFlag($n, $default);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getManager(){
        return $this->getFlag(self::MANAGER_FLAG);
    }
    ///<summary>merge all required scripts</summary>
    ///<param name="zip">get or set if required minification</param>
    ///<param name="files" ref="true">will recieve all merged scripts</param>
    /**
    * merge all required scripts
    * @param mixed $zip get or set if required minification
    * @param mixed $files will recieve all merged scripts
    */
    public function getMergedContent($zip=0, & $files=null){
        // igk_wln_e("merge content");
        $tasc=$this->getAssoc();
        $o="";
        if($tasc){
            $resolver=IGKResourceUriResolver::getInstance();
            foreach($tasc->to_array() as $k=>$v){
                if(!file_exists($k) || !preg_match("/\.js$/", basename($k)))
                    continue;
                $u=$resolver->resolve($k, null, 0);
                $o .= IGK_START_COMMENT."+F:  ". $u."".IGK_END_COMMENT.IGK_LF;
                $g=utf8_decode(igk_io_read_allfile($k));
                if($zip)
                    $g=igk_js_minify($g);
                $o .= $g.IGK_LF;
            }
        }
        return (object)(array("data"=>$o));
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getNode(){
        return $this->getFlag(self::JSMAN_NODE);
    }
    ///<summary></summary>
    ///<param name="tab" default="null"></param>
    /**
    * 
    * @param mixed $tab the default value is null
    */
    public function getNonMergedContent($tab=null){
        $nonMerged=$tab == null ? $this->getMergedContent()->notMerged: $tab->notMerged;
        $o="";
        foreach($nonMerged as $v){
            $o .= $v->render(null);
        }
        return $o;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    /**
    * 
    * @param mixed $file
    */
    public function getScript($file){
        $tasc=$this->getAssoc();
        if($tasc && isset($tasc[$file]))
            return $this->$tasc[$file];
        return null;
    }
    ///<summary>return document tempory script</summary>
    /**
    * return document tempory script
    */
    public function & getTempScripts(){
        $c=$this->_f->getFlag(self::TEMPORARY_SCRIPT);
        if(!$c){
            $c=(object)array();
            $this->_f->setFlag(self::TEMPORARY_SCRIPT, $c);
        }
        return $c;
    }
    ///<summary>get if this script file is loaded to the document</summary>
    /**
    * get if this script file is loaded to the document
    */
    public function isLoaded($file){
        return isset($this->m_assocTable[$file]);
    }
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    /**
    * 
    * @param mixed $option the default value is null
    */
    public function localScriptRenderCallback($option=null){
        if(igk_xml_is_mailoptions($option))
            return;

        // TODO : RENDER SCRIPTS
        return "<!-- render script -->";        

        $g="";
        $_appv = igk_app()->settings->version;
        $count = 0;
        $bindjs=function($m, $u, $liburi, & $root, & $g, $option){  
            
            // igk_wl("<!-- bind script: local script ".$u. " -->\n");
            // return;

            if(igk_io_path_ext($m) == "js"){
                $active='';
                if($liburi == $u){
                    $root .= '<script type="text/javascript" language="javascript" src="'.$u.'"></script>';
                }
                else{
                    $active="defer";
                    if($option && igk_getv($option, "Context") == "xsl")
                        $active .= "=\"true\"";
                    $g .= '<script type="text/javascript" language="javascript" src="'.$u.'" '.$active.' ></script>';
                } 
            }
        }; 
        $tasc=$this->getAssoc();
        $depth=$option->Indent ? str_repeat("\t", max(0, $option->Depth-1)): "";
        if(igk_sys_env_production() || defined("IGK_JS_TRIMSCRIPTS")){
            if(!file_exists($js_uri=igk_core_dist_jscache())){
                $js_uri = igk_io_corejs_uri();
            }
            else{
                $bjs_uri = dirname($js_uri);
                IGKResourceUriResolver::getInstance()->resolve($bjs_uri);
            } 
            // $c=igk_createxmlnode("script");
            // $c["type"]="text/javascript";
            // $c["language"]="javascript";
            // $c["src"]= new IGKHtmlRelativeUriValueAttribute($js_uri);
            // $c->renderAJX($option);
            if (!igk_is_ajx_demand()){ 
                $src = igk_get_balafonjs_src();
                if(empty($src)){
                    igk_wln_e("balafon.js core code is empty;");
                }else {
                    $c=igk_createxmlnode("script");
                    $c["type"]="text/balafon-javascript";
                    $c["language"]="javascript";         
                    $c->Content = implode("", ["//<![CDATA[", 
                        $src,
                        "]]>",                
                    ]);
                    $c->renderAJX($option);
                    $c=igk_createnode("script");
                    $c->Content = igk_js_minify(file_get_contents(__DIR__."/Inc/js/eval.js"));
                    $c->renderAJX($option);
                }
            }
            $bdir=igk_io_basedir(); 
            foreach($tasc->to_array() as $k=>$v){
                if(is_array($v) && (igk_getv($v, "merge") == 2)){
                    
                    $u = IGKResourceUriResolver::getInstance()->resolve($k);
                    // igk_wln_e("render .... ",$u, $_appv);
                    // $u=igk_io_html_link($bb)->getValue();
                    if (strpos("?", $u) === false){
                        $u .="?v=".$_appv;
                    } 
                    $g .= '<script type="text/javascript" language="javascript" src="'.$u.'" defer ></script>';
                }
            }
            igk_wl($g);
            $c=igk_js_get_temp_script_host();
            if($c){
                $c->renderAJX();
            }
            return;
        }
        if($tasc){
            $files=array_keys($tasc->to_array());
            $buri=igk_io_baseuri();
            $libdir=IGK_BALAFON_JS_CORE_FILE;
            $bdir=igk_html_uri(igk_io_basedir());
            $root="";

            $bb="";
            $liburi=igk_io_html_link($libdir)->getValue();

            if(!igk_io_is_subdir(igk_io_applicationdir(), IGK_LIB_DIR)){
                $resolver=IGKResourceUriResolver::getInstance();
                foreach($files as $m){
                    $u=$resolver->resolve($m);
                    $bindjs($m, $u, $liburi, $root, $g, $option);
                }
            }
            else{

                foreach($files as $k=>$b){
                    $u='';
                    if(preg_match("/^\%lib\%/i", $b)){
                        $bb=igk_io_expand_path($b);
                        $u=substr($bb, strlen(IGK_LIB_DIR) + 1);
                        if(preg_match("/^phar:/i", $bb)){
                            $u=igk_io_html_link(str_replace(Phar::running()."/", "", $bb))->getValue();
                        }
                    }
                    else{
                        $u=igk_io_html_link($b)->getValue();
                    }
                    if(empty($u))
                        continue;
                    if (strpos("?", $u) === false){
                        $u .="?v=".$_appv;
                    }
					if ($b == IGK_BALAFON_JS_CORE_FILE){
						 //+ render core js
						 $root .= '<script type="text/javascript" language="javascript" src="'.$u.'"></script>';
					}else {
						$bindjs($b, $u, $liburi, $root, $g, $option);
					}
                }
            }
            if(!empty(trim($s=($root.$g)))){
                $ds="\n<!-- render script -->\n";
                $ds .= $root.$g;
                $ds .= "\n<!-- endrender: script -->\n";
                igk_wl($ds);
            }
        }
    }
}