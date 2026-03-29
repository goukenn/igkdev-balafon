<?php
// @file: IGKCssComponentStyle.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\Html\CallableConstants;
/**
* Igkcss component style.
*/
final class IGKCssComponentStyle extends IGKObject{
    /**
    * Property: loaded styles.
    * @var mixed
    */
    private $m_loadedStyles;
    ///.ctr
    /**
    * .ctr
    * @return
    */
    private function __construct(){
        $this->m_loadedStyles=array();
    }
    /**
    * Returns Instance.
    */
    public static function getInstance(){
        $k=igk_get_instance_key(__CLASS__);
        $v=igk_app()->session->getParam($k);
        if(!$v){
            $v=new IGKCssComponentStyle();
            igk_app()->session->setParam($k, $v);
        }
        return $v;
    }
    /**
    * Reg file.
    * @param mixed $file
    * @param null|mixed $host
    */
    public function regFile($file, $host=null){
        if(!igk_io_file_exists($file))
            return null;
        if(isset($this->m_loadedStyles[$file])){
            $ct=igk_html_node_clonenode($this->m_loadedStyles[$file]);
            return $ct;
        }
        $c=igk_create_node("style");
        $c["type"]="text/css";
        $c->setCallback(CallableConstants::CALLABLE_ACCEPT_RENDER, igk_create_expression_callback(<<<EOF

if (igk_env_count('sys://rendering/'.\$file)>1)
	return false;
\$bind->Content = igk_bind_host_css_style_file(\$file, \$extra[0]->Document ?? igk_get_document(\$host),\$host);
return true;
EOF        , array("file"=>$file, "host"=>$host)));
        $c->setCallback("attachDispose", igk_create_expression_callback(<<<EOF
igk_ilog("disposall ");
unset(\$tab[\$file]);
EOF        , array("file"=>$file, "n"=>$c, "tab"=>$this->m_loadedStyles)));
        $this->m_loadedStyles[$file]=$c;
        return $c;
    }
}