<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlRendererOptions.php
// @date: 20220906 00:28:43
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class HtmlRendererOptions{
    var $Source;

    var $LF="";
 
    var $__sanitize = null;

    var $__invoke = null;
    /**
     * indent writing content
     * @var bool
     */
    var $Indent;
    /**
     * is for ajx context 
     * @var bool
     */
    var $AJX; 
    /**
     * stop rendering 
     * @var bool
     */
    var $Stop = 0;

    /**
     * redering context
     * @var string
     */
    var $Context =  HtmlContext::Html;
    /**
     * Writing deep
     * @var int
     */
    var $Depth = 0;
    /**
     * source document 
     * @var ?IGKHtmlDoc
     */
    var $Document;
    var $BodyOnly = 0;
    var $Attachement;
    var $StandAlone = 0;
    var $Cache;
    var $CacheUri = 0 ;
    
    var $CacheUriLevel= 0;
    
    var $flag_no_attrib_escape;

    var $attribute_entity_escape;
    /**
     * array of tab
     * @var array
     */
    var $Tab = [];
    var $Chain;
    /**
     * text only 
     * @var bool
     */
    var $TextOnly;
    /**
     * last rendered node
     * @var mixed
     */
    var $lastRendering;
    /**
     * for ops first eval
     * @var mixed
     */
    var $jsOpsFirstEval;

    /**
     * header to attach to render document
     * @var mixed
     */
    var $header;

    /**
     * rendering context, ?|template| in RenderingContext
     * @var ?string
     */
    var $renderingContext;

    /**
     * skip tags list
     * @var ?array|callable 
     */
    var $skipTags;

    /**
     * 
     * @var for aside items
     */
    var $aside;
 

    private $m_properties;



    public function __set($n, $v){ 
        if (is_null($v)){
            unset($this->m_properties[$n]);
            return;
        }
        $this->m_properties[$n] = $v;
    }
    public function __get($n){        
        igk_getv($this->m_properties, $n);
    }
    public function setRef($n, & $v){
        $this->m_properties[$n] = & $v;
    }
    public function & getRef($n){
        $rg = null;
        if (isset($this->m_properties[$n])){
            $rg = & $this->m_properties[$n];
        }
        return $rg;
    }
}