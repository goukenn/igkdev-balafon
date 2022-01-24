<?php
// @file: IGKCssDefaultStyle.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Css\ICssStyleContainer;

final class IGKCssDefaultStyle implements ArrayAccess, ICssStyleContainer{
    use \IGK\System\Polyfill\CSSDefaultArrayAccess;
    const COLORS_RULE=5;
    const DECLARED_RULE=1;
    const FILES_BIND_TEMP_RULE=9;
    const FILES_RULE=4;
    const FLAG_RULE=2;
    const FONT_RULE=7;
    const PARAMS_RULE=6;
    const PROPERTIES=0;
    const SYMBOLS_RULE=3;
    const TEMP_FILES_RULE=8;
    private $_;
    ///<summary></summary>
    public function __construct(& $setting){
        $this->_=& $setting;
    }

    public function getProperties() { 
        return igk_getv($this->_, self::PROPERTIES);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    public function __get($n){
        igk_die(__METHOD__." not allowed [{$n}] : ");
    }
    ///<summary></summary>
    public function __serialize(){
        return [serialize(array_filter($this->_))];
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function __set($n, $v){
        igk_die(__METHOD__." not allowed [{$n}]");
    }
    ///<summary>display value</summary>
    public function __toString(){
        return get_class($this)."#items[count(".count($this->_).")]";
    }
    /**
     * return a copy array presentation of this style
     * @return array 
     */
    public function to_array(){  
        // + |------------------------------------------------------
        // + | copy array  
        $o = [];
        foreach($this->_ as $k=>$v){
            $o[$k] = $v;
        }
        return $o;
    }
    public function load_data(array $data){
        $this->_ = [];
        foreach([self::COLORS_RULE,
        self::DECLARED_RULE,
        self::FILES_BIND_TEMP_RULE,
        self::FILES_RULE,
        self::FLAG_RULE,
        self::FONT_RULE,
        self::PARAMS_RULE,
        self::PROPERTIES,
        self::SYMBOLS_RULE,
        self::TEMP_FILES_RULE
        ] as $t){
            if (isset($data[$t]) && is_array($g = $data[$t])){
                $this->_[$t] = $g;
            }
        }
    }
    ///<summary></summary>
    ///<param name="seri"></param>
    public function __unserialize($seri){
        if(is_array($seri)){
            $seri=$seri[0];
        }
        $this->_=unserialize($seri) ?? [];
    }
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="expression"></param>
    public function addRule($name, $expression){
        $rule=& $this->_[self::DECLARED_RULE];
        $rule[$name]=$expression;
    }
    ///<summary></summary>
    public function Clear(){
        if($this->_) while(count($this->_) > 0)
            array_pop($this->_);
    }
    ///<summary></summary>
    public function clearFiles(){
        unset($this->_[self::FILES_RULE]);
    }
    ///<summary></summary>
    public function getAttributes(){
        return igk_getv($this->_, self::PROPERTIES);
    }
    public function getdef(){
        igk_trace();
        igk_wln_e("get def....");
        return igk_getv($this->_, self::PROPERTIES);
    }
    ///<summary></summary>
    public function getBindTempFiles($clear=0){
        $r=igk_getv($this->_, self::FILES_BIND_TEMP_RULE);
        if($r && $clear){
            $this->_[self::FILES_BIND_TEMP_RULE]=null;
        }
        return $r;
    }
    ///<summary>get reference to getCl</summary>
    public function & getCl(){
        $g=& $this->prepareStorage(self::COLORS_RULE);
        return $g;
    }
    ///<summary></summary>
    public function getFiles(){
        return igk_getv($this->_, self::FILES_RULE);
    }
    ///<summary>return registrated font</summary>
    ///<return refout="true"></return>
    public function & getFont(){
        $g=& $this->prepareStorage(self::FONT_RULE);
        return $g;
    }
    ///<summary></summary>
    public function getHasRules(){
        $tab=igk_getv($this->_, self::DECLARED_RULE);
        return $tab && (igk_count($tab) > 0);
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    public function & getParams(){
        $g=& $this->prepareStorage(self::PARAMS_RULE);
        return $g;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    public function & getRules(){
        $g=null;
        if(isset($this->_[self::FLAG_RULE])){
            $g=& $this->_[self::FLAG_RULE];
        }
        else{
            $g=array();
            $this->_[self::FLAG_RULE]=& $g;
        }
        return $g;
    }
    ///<summary></summary>
    ///<param name="lineseparator" default="null"></param>
    ///<param name="doc" default="null"></param>
    public function getRulesString($lineseparator=null, $themeexport=false, $systheme=null){
        $o="";
        $tab=igk_getv($this->_, self::DECLARED_RULE);
        foreach($tab as $k=>$v){
            $v=igk_css_treat($this, $v, $systheme);
            if($v){
                $o .= $k."{".$v."}".$lineseparator;
            }
        }
        return $o;
    }
    ///<summary></summary>
    public function getSymbols(){
        return igk_getv($this->_, self::SYMBOLS_RULE);
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    public function & getTempFiles(){
        $g=& $this->prepareStorage(self::TEMP_FILES_RULE);
        return $g;
    }
    ///<summary></summary>
    ///<param name="id"></param>
    ///<return refout="true"></return>
    private function & prepareStorage($id){
        $g=null;
        if(isset($this->_[$id]))
            $g=& $this->_[$id];
        else{
            $g=array();
            $this->_[$id]=& $g;
        }
        return $g;
    }
    ///<summary>register symbols package</summary>
    public function regSymbol($file){
        $tab=igk_getv($this->_, self::SYMBOLS_RULE) ?? array();
        $tab[$file]=$file;
        $this->_[self::SYMBOLS_RULE]=$tab;
    }
    ///<summary></summary>
    public function resetParams(){
        unset($this->_[self::PARAMS_RULE]);
    }
    ///<summary></summary>
    ///<param name="name"></param>
    public function rmRule($name){
        $rule=& $this->_[self::DECLARED_RULE];
        unset($rule[$name]);
    }
    ///<summary>Bind css Temppory files</summary>
    ///<param name="files"></param>
    public function setBindTempFiles($files){
        if(($files == null) || !is_string($files)){
            unset($this->_[self::FILES_BIND_TEMP_RULE]);
        }
        else{
            $this->_[self::FILES_BIND_TEMP_RULE]=igk_io_collapse_path($files);
        }
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    public function setCl($n, $v){
        $g=& $this->prepareStorage(self::COLORS_RULE);
        $g[$n]=$v;
    }
    ///<summary></summary>
    ///<param name="files"></param>
    public function setFiles($files){
        if(($files == null) || !is_string($files)){
            unset($this->_[self::FILES_RULE]);
        }
        else{
            $rf=igk_io_collapse_path($files);
            $this->_[self::FILES_RULE]=$rf;
        }
    }
}
