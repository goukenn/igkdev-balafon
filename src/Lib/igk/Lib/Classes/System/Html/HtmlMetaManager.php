<?php
// @file: IGKHtmlMetaManager.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
use IGKEvents;
use IGKException;
use IGKObject;

/**
 * manage document meta
 * @package IGK\System\Html
 */
final class HtmlMetaManager extends IGKObject{
    /**
    * Constant: attr content.
    * @var mixed
    */
    const ATTR_CONTENT="content";
    // + | IE tools charset must be specified first
    /**
    * Constant: meta charset.
    * @var mixed
    */
    const META_CHARSET = 0;
    /**
    * Constant: meta author.
    * @var mixed
    */
    const META_AUTHOR=0x1;
    /**
    * Constant: meta content type.
    * @var mixed
    */
    const META_CONTENT_TYPE=self::META_AUTHOR + 0x4;
    /**
    * Constant: meta copyright.
    * @var mixed
    */
    const META_COPYRIGHT=self::META_AUTHOR + 0x1;
    /**
    * Constant: meta desc.
    * @var mixed
    */
    const META_DESC=self::META_AUTHOR + 0x2;
    /**
    * Constant: meta keywords.
    * @var mixed
    */
    const META_KEYWORDS=self::META_AUTHOR + 0x3;
    /**
    * Constant: meta generator.
    * @var mixed
    */
    const META_GENERATOR=self::META_AUTHOR + 0x5;
    /**
    * Constant: meta viewport.
    * @var mixed
    */
    const META_VIEWPORT=self::META_AUTHOR + 0x6;
    /**
    * Constant: meta lastupdate.
    * @var mixed
    */
    const META_LASTUPDATE=self::META_AUTHOR + 0x7;
    /**
    * Constant: meta color scheme.
    * @var mixed
    */
    const META_COLOR_SCHEME=self::META_AUTHOR + 0x8;
    /**
     * single meta name
     * @var mixed
     */
    private $m_metas;
    /**
     * for key metas
     * @var mixed
     */
    private $m_key_metas = [];
    /**
    * .ctr
    */
    public function __construct(){
        $this->_initMetas();
    }
    /**
    * Custom serialization logic.
    */
    public function __serialize(){
        $g=array();
        foreach($this->m_metas as $k=>$v){
            if(isset($v['changed']) && $v['changed']){
                $t=array_slice($v, 0);
                unset($t["changed"]);
                $g[$k]=$t;
            }
        }
        if(count($g) > 0)
            return json_encode($g);
        return '';
    }
    /**
    * get string presentation.
    */
    public function __toString(){
        return __CLASS__;
    }
    /**
    * Custom unserialization logic.
    * @param mixed $s
    */
    public function __unserialize($s){
        $this->_initMetas();
        if(!empty($s) && ($tab=json_decode($s))){
            foreach($tab as $k=>$v){
                unset($v->changed);
                $this->m_metas[$k]=(array)$v;
            }
        }
    }
    /**
    * auto generate doc.
    * @return
    */
    private function _initMetas(){
        $cnf=igk_app()->configs;
        $this->m_metas=array();
        $this->m_metas[self::META_CHARSET]= ["charset"=>"utf-8"];
        $this->m_metas[self::META_AUTHOR]=array("name"=>"author", self::ATTR_CONTENT=>IGK_AUTHOR);
        $this->m_metas[self::META_COPYRIGHT]=array(
            "name"=>"copyright",
            self::ATTR_CONTENT=>$cnf->meta_copyright
        );
        $this->m_metas[self::META_DESC]=array(
            "name"=>"Description",
            self::ATTR_CONTENT=>$cnf->meta_description
        );
        $this->m_metas[self::META_KEYWORDS]=array(
            "name"=>"Keywords",
            self::ATTR_CONTENT=>$cnf->meta_keywords
        );
        $this->m_metas[self::META_GENERATOR]=array(
            "name"=>"generator",
            self::ATTR_CONTENT=>igk_app_version()
        );
        $this->m_metas[self::META_VIEWPORT]=array(
            "name"=>"viewport",
            self::ATTR_CONTENT=> "width=device-width, initial-scale=1"
        ); 
    }
    /**
     * register meta definition base 
     * @param string $name 
     * @param mixed $meta 
     * @return int 
     * @throws IGKException 
     */
    public function addMeta(string $name, $meta){
        $bmeta=igk_getv($this->m_metas, $name);
        // + | --------------------------------------------------------------------
        // + | remove meta form definition or update the meta string
        // + |
        if($bmeta && ($bmeta !== $meta)){
            unset($this->m_metas[$name]);
        } 
        if($meta && !isset($this->m_metas[$name])){
            if (is_string($meta)){
                $m = new \IGK\System\Html\Dom\HtmlNode("meta");
                $m["name"] = $name;
                $m["content"] = $meta;
                $meta = $m;
            } else if (is_array($meta)){
                $meta = (object)["attributes"=>$meta];
            }
            $this->m_metas[$name]= HtmlUtils::GetAttributes($meta->attributes);
            $this->m_metas[$name]["changed"]=1;
            return 1;
        }
        return 0;
    }
    /**
    * Returns Author.
    */
    public function getAuthor(){
        return HtmlUtils::GetValue($this->m_metas[self::META_AUTHOR][self::ATTR_CONTENT]);
    }
    /**
    * Returns Content Type.
    */
    public function getContentType(){
        return HtmlUtils::GetValue($this->m_metas[self::META_CONTENT_TYPE][self::ATTR_CONTENT]);
    }
    /**
    * Returns Copyright.
    */
    public function getCopyright(){
        return HtmlUtils::GetValue($this->m_metas[self::META_COPYRIGHT][self::ATTR_CONTENT]);
    }
    /**
    * Returns Description.
    */
    public function getDescription(){
        return HtmlUtils::GetValue($this->m_metas[self::META_DESC][self::ATTR_CONTENT]);
    }
    /**
    * Returns Keywords.
    */
    public function getKeywords(){
        return HtmlUtils::GetValue($this->m_metas[self::META_KEYWORDS][self::ATTR_CONTENT]);
    }
    /**
    * Returns Meta By Id.
    * @param mixed $name
    */
    public function getMetaById($name){
        if(isset($this->m_metas[$name])){
            return $this->m_metas[$name];
        }
        return null;
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options=null){
        $LF="";
        $o="";
        $DEPTH = "";
        if (igk_getv($options, "Indent")){
            $LF = "\n";
            $DEPTH = str_repeat("\t", $options->Depth);
        }
        foreach($this->m_metas as $k=>$v){
            $o .= $LF.$DEPTH."<meta ";
            foreach($v as $k=>$v){
                if($k == "changed")
                    continue;
                $o .= $k."=\"".HtmlUtils::GetAttributeValue($v)."\" ";
            }
            $o .= "/>";
        }
        foreach($this->m_key_metas as $k => $v){
            while(count($v)>0){
                $q = array_shift($v);
                $o .= $LF.$DEPTH."<meta ";
                $o .= sprintf("name=\"%s\"", HtmlUtils::GetAttributeValue($k));                
                $o .= " ".HtmlRenderer::GetAttributeArrayToString($q);
                $o .= "/>";
            }
        }
        $o .= igk_ob_get_func(function($options){
            igk_hook(IGKEvents::HOOK_HTML_META, [$options, $this]);
        },[$options] );
        return $o;
    }
    /**
    * auto generate doc.
    * @param array $attributes assoc array
    * @return void
    */
    public function appendKeyMeta($name, array $attributes){
        if (!isset($this->m_key_metas[$name])){
            $this->m_key_metas[$name] = [];
        }
        $this->m_key_metas[$name][] = $attributes;
        return $this;
    }
    /**
    * Clears Key Meta.
    */
    public function clearKeyMeta(){
        $this->m_key_metas = [];
    }
    /**
    * Rm meta.
    * @param mixed $name
    */
    public function rmMeta($name){
        if(isset($this->m_metas[$name])){
            unset($this->m_metas[$name]);
        }
    }
    /**
    * Sets Attribute.
    * @param mixed $key
    * @param mixed $attr
    * @param mixed $value
    */
    public function setAttribute($key, $attr, $value){
        $this->m_metas[$key][$attr]=$value;
        $this->m_metas[$key]["changed"]=1;
    }
    /**
    * Sets Charset.
    * @param string $charset
    */
    public function setCharset(string $charset){
        $this->m_metas[self::META_CHARSET] = ['charset'=>$charset];
    }
    /**
    * Sets Author.
    * @param mixed $value
    */
    public function setAuthor($value){
        $this->updateContent(self::META_AUTHOR, $value);
    }
    /**
    * Sets Content Type.
    * @param mixed $value
    */
    public function setContentType($value){
        $this->updateContent(self::META_CONTENT_TYPE, $value);
    }
    /**
    * Sets Copyright.
    * @param mixed $value
    */
    public function setCopyright($value){
        $this->updateContent(self::META_COPYRIGHT, $value);
    }
    /**
    * Sets Last Update.
    * @param mixed $value
    */
    public function setLastUpdate($value){
        $this->updateContent(self::META_LASTUPDATE, $value);
    }
    /**
    * Sets Description.
    * @param mixed $value
    */
    public function setDescription($value){
        $this->updateContent(self::META_DESC, $value);
    }
    /**
    * Sets Keywords.
    * @param mixed $value
    */
    public function setKeywords($value){
        if (is_null($value)){
            unset($this->m_metas[self::META_KEYWORDS]);
        }else 
            $this->updateContent(self::META_KEYWORDS, $value);
    }
    /**
    * Updates Content.
    * @param mixed $key
    * @param mixed $value
    */
    public function updateContent($key, $value){
        if(!isset($this->m_metas[$key][self::ATTR_CONTENT]) || ($this->m_metas[$key][self::ATTR_CONTENT] != $value)){
            $this->m_metas[$key][self::ATTR_CONTENT]=$value;
            $this->m_metas[$key]["changed"]=1;
        }
    }
    /**
     * get meta content value
     * @param mixed $name 
     * @return mixed 
     * @throws IGKException 
     */
    public function get($name){
        foreach($this->m_metas as $t){
            if (igk_getv($t, "name")==$name){
                return igk_getv($t,self::ATTR_CONTENT);
            }
        } 
    }
}