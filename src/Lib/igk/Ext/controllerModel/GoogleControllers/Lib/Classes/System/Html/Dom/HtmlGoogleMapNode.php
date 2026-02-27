<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKHtmlGoogleMapNodeItem.php
// @date: 20220909 08:33:48
// @desc: 


use IGK\System\Html\Dom\HtmlNode;

/**
* Represent IGKHtmlGoogleMapNodeItem class
*/
final class HtmlGoogleMapNode extends HtmlNode{

    /**
    * Property: key.
    * @var mixed
    */
    private $m_key;

    /**
    * Property: location.
    * @var mixed
    */
    private $m_location;

    /**
    * Property: query.
    * @var mixed
    */
    private $m_query;

    /**
    * Type of type.
    * @var mixed
    */
    private $m_type;

    /**
    * auto generate doc.
    * @param mixed $options the default value is null
    */

    protected function __acceptRender($options=null){
        $this->initView();
        return parent::_acceptRender($options);
    }

    /**
    * auto generate doc.
    */
    public function __construct(?string $key=null){
        parent::__construct("div");
        $this["class"]="igk-winui-google-map";
        $this->m_type="place";
        $this->m_key= $key ?? igk_configs()->get("google.map.key");
    }
    /**
    * retrieve the stored key
    */

    public function getKey(){
        return $this->m_key;
    }

    /**
    * auto generate doc.
    */
    public function getLocation(){
        return $this->m_location;
    }

    /**
    * auto generate doc.
    */
    public function getQuery(){
        return $this->m_query;
    }

    /**
    * auto generate doc.
    */
    public function getType(){
        return $this->m_type;
    }

    /**
    * auto generate doc.
    */
    public function initView(){
        $this->clearChilds();
        $key=$this->getKey();
        $t=$this->getType();
        $q=$this->Location;
        $lnk="https://www.google.com/maps/embed/v1/{$t}?key={$key}&q={$q}";
        if(igk_sys_env_production() && $lnk){
            $s=<<<EOF
<iframe class="no-border1 googlemap_map fitw" src="{$lnk}" frameborder="0" ></iframe>
EOF;
            $this->Load($s);
        }
    }
    ///<summary></summary>
    ///<param name="v"></param>

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setKey($v){
        $this->m_key=$v;
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setLocation($v){
        $this->m_location=$v;
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setQuery($v){
        $this->m_query=$v;
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setType($v){
        $this->m_type=$v;
        return $this;
    }
} 