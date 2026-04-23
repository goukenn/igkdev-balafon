<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKHtmlScriptManager.php
// @date: 20220803 13:48:54
// @desc: 
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\Dom\HtmlScriptNode;

/**
* Represent IGKHtmlScriptManager class
*/
final class IGKHtmlScriptManager extends IGKObject {
    /**
    * Constant: doc flag.
    * @var mixed
    */
    const DOC_FLAG=3;
    /**
    * Constant: jsman assoc table flag.
    * @var mixed
    */
    const JSMAN_ASSOC_TABLE_FLAG=2;
    /**
    * Constant: jsman node.
    * @var mixed
    */
    const JSMAN_NODE=4;
    /**
    * Constant: manager flag.
    * @var mixed
    */
    const MANAGER_FLAG=1;
    /**
    * Constant: script item manager.
    * @var mixed
    */
    const SCRIPT_ITEM_MANAGER=5;
    /**
    * Constant: temporary script.
    * @var mixed
    */
    const TEMPORARY_SCRIPT=6;
    /**
    * Property: f.
    * @var mixed
    */
    private $_f;
    /**
    * Property: scripts.
    * @var mixed
    */
    private $m_scripts = [];
    /**
    * auto generate doc.
    * @param mixed $owner
    */
    public function __construct(){
        $this->_f=new IGKFv(); 
        igk_reg_hook(IGKEvents::HOOK_HTML_HEAD, function($e){
            $v_options = igk_getv($e->args, "options");
            if ($v_options)
            {
                if ( !($v_options->Document instanceof IGKHtmlDoc) ||
                    ($v_options->Document->getScriptManager() !== $this)){
                        return; 
                }
                $src = "";
                foreach($this->m_scripts as $file=>$v){
                    $u = "";
                    $attr = "";
                    if (igk_io_file_exists($file)){
                        $u = IGKResourceUriResolver::getInstance()->resolve($file);
                    } else {
                        $u = $file;
                    }
                    $src .= '<script type="text/javascript" language="javascript" src="'.$u.'"';
                    $src .= $attr;
                    $src.="></script>";
                }                
                echo $src;
            }
        });
    }
    /**
    * display value
    */
    public function __toString(){
        return get_class($this);
    }
    /**
    * register script to script manager
    * @param string $file server script file
    * @param bool $canbeMerged serve on production allow to merge on system rendering script
    * @param string $tag 'priv'| or any tag to identify associate script
    */
    public function addScript($file, $canbeMerged=true, $tag='priv'){  
        if (!($s = igk_getv($this->m_scripts, $file))){
            $item = new HtmlScriptNode();
            $s = compact("canbeMerged", "tag", "item");
            $this->m_scripts[$file] = $s;
        }
        return $s["item"]; 
    } 
    /**
    * clear loaded script
    */
    public function Clear($tag=null){
        throw new NotImplementException(__METHOD__);
    }
    /**
    * auto generate doc.
    */
    public function Flags(){
        return $this->_f;
    }
    /**
    * auto generate doc.
    */
    public function getAssoc(){
        igk_trace();
        throw new NotImplementException(__METHOD__);
    }
    /**
    * auto generate doc.
    * @param mixed $default the default value is null
    * @return mixed
    */
    public function getFlag($n, $default=null){
        return $this->_f->getFlag($n, $default);
    }
    /**
    * auto generate doc.
    */
    public function getManager(){
        return $this->getFlag(self::MANAGER_FLAG);
    }
    /**
    * merge all required scripts
    * @param mixed $zip get or set if required minification
    * @param mixed $files will recieve all merged scripts
    * @deprecated
    */
    public function getMergedContent($zip=0, & $files=null){
        throw new IGKException(__METHOD__. " Not implement");
    }
    /**
    * auto generate doc.
    */
    public function getNode(){
        return $this->getFlag(self::JSMAN_NODE);
    }
    /**
    * auto generate doc.
    * @param mixed $tab the default value is null
    */
    public function getNonMergedContent($tab=null){
        throw new Exception("Not implement = ".__METHOD__);
    }
    /**
    * auto generate doc.
    * @param mixed $file
    */
    public function getScript($file){
        igk_wln("get scripts ".$file);
        $tasc=$this->getAssoc();
        if($tasc && isset($tasc[$file]))
            return $this->$tasc[$file];
        return null;
    }
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
    /**
    * get if this script file is loaded to the document
    */
    public function isLoaded($file){
        return isset($this->m_assocTable[$file]);
    }
}