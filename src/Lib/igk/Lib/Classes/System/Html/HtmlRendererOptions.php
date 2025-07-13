<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlRendererOptions.php
// @date: 20220906 00:28:43
namespace IGK\System\Html;
use IGKException;
/**
 * html rendering option 
 * @package IGK\System\Html
 */
class HtmlRendererOptions
{
    /**
     * get or set the rendering source 
     * @var mixed
     */
    var $Source;
    /**
     * get or set line feed separatror 
     * @var string
     */
    var $LF = "";
    /**
     * store current namespace context
     * @var mixed
     */
    var $NamespaceContext;
    /**
     * namespace target sournce owner
     * @var mixed
     */
    var $NamespaceSource;
    /**
     * engine use to render 
     * @var ?IHtmlEngineRenderer
     */
    var $Engine;
    /**
     * use to mark renderer with sanitize rendering
     * @var null|bool|int
     */
    var $sanitizeRendering = null;
    /**
     * invocation list deletegate
     * @var mixed
     */
    var $__invoke = null;
    /**
     * filter element
     * @var ?callable (HtmlNode a)=>bool
     */
    var $filterListener;
    /**
     * preserving attribute declaration order
     * @var ?bool 
     */
    var $PreserveAttribOrder;
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
    var $CacheUri = 0;
    var $CacheUriLevel = 0;
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
     * skip node read flag when rendering engine is use 
     * @var ?bool
     */
    var $skipEngineNode;
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
    /**
     * set extra property
     * @param mixed $n 
     * @param mixed $v 
     * @return void 
     */
    public function __set($n, $v)
    {
        if (is_null($v)) {
            unset($this->m_properties[$n]);
            return;
        }
        $this->m_properties[$n] = $v;
    }
    /**
     * get extrat properties
     * @param mixed $n 
     * @return mixed 
     * @throws IGKException 
     */
    public function __get($n)
    {
        return igk_getv($this->m_properties, $n);
    }
    public function setRef($n, &$v)
    {
        $this->m_properties[$n] = &$v;
    }
    public function &getRef($n)
    {
        $rg = null;
        if (isset($this->m_properties[$n])) {
            $rg = &$this->m_properties[$n];
        }
        return $rg;
    }
    public function __isset($n){
        return $this->m_properties && key_exists($n, $this->m_properties);
    }
}