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

    /**
    * auto generate doc.
    * @var mixed
    */
    var $BodyOnly = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Attachement;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $StandAlone = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $Cache;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $CacheUri = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $CacheUriLevel = 0;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $flag_no_attrib_escape;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $attribute_entity_escape;
    /**
     * array of tab
     * @var array
     */
    var $Tab = [];

    /**
    * auto generate doc.
    * @var mixed
    */
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
    /**
     * store definition properties
     * @var mixed
     */
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
    /**
     * set reference properties
     * @param mixed $n 
     * @param mixed &$v 
     * @return void 
     */

    public function setRef($n, &$v)
    {
        $this->m_properties[$n] = &$v;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function &getRef($n)
    {
        $rg = null;
        if (isset($this->m_properties[$n])) {
            $rg = &$this->m_properties[$n];
        }
        return $rg;
    }

    /**
    * check if isset innaccessible property
    * @param mixed $n
    */
    public function __isset($n){
        return $this->m_properties && key_exists($n, $this->m_properties);
    }
}