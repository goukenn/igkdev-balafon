<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerNodeModifyDetector.php
// @date: 20221011 11:17:20
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;

use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewCompilerArgument;
use IGKException;

///<summary></summary>
/**
 * detect modification node
 * @package IGK\System\Runtime\Compiler\Html
 * @parametter clearFlag
 */
class CompilerNodeModifyDetector extends HtmlNode
{
    private $m_modify = false;
    /**
     * detected document
     * @var mixed
     */
    private $m_document;
    protected $tagname = "igk:compiler-modifynode-detector";
    private $m_freezeClearModify = false;
    static $sm_sys_modify = false;
    static $sm_filter_callback = null;

    /**
     * detecting class array modification
     * @var mixed
     */
    private $m_class_array =[];

    const CLEAR_FLAG_PARAM = "clearFlag";

    /**
     * set the compiler document
     * @param null|IViewCompilerArgument $value 
     * @return void 
     */
    public function setDocument(?IViewCompilerArgument $value=null){
        $this->m_document = $value;
    }
    /**
     * get the compiler document
     * @return null|IViewCompilerArgument 
     */
    public function getDocument():?IViewCompilerArgument{
        return $this->m_document;
    }
    /**
     * clear and modify attribute
     * @return HtmlNode 
     * @throws IGKException 
     */
    public function clearChilds()
    {        
        $this->m_class_array = [];
        $this->m_attributes->clear();
        if (!$this->m_freezeClearModify) {
            $this->m_modify  = false;
        } else {
            $this->setParam(self::CLEAR_FLAG_PARAM, true);
        }
        return parent::clearChilds();
    }
    public function __get($n)
    {
        return null;
    }
    public function __set($n, $v)
    {
        $this->m_modify = true;
        return $this;
    }
    /**
     * detect node modification
     * @return bool 
     */
    public function getModify(): bool
    {
        return $this->m_modify || ($this->getChildCount() > 0) || (igk_count($this->m_attributes) > 0);
    }
    public function getFreezeClearModify()
    {
        return $this->m_freezeClearModify;
    }
    public function setFreezeClearModify(bool $freeze)
    {
        $this->m_freezeClearModify = $freeze;
    }
    public function _access_OffsetSet($n, $v)
    {
        $this->m_modify = true;
        if ($n=="class"){
            if (!in_array($v, $this->m_class_array)){
                $this->m_class_array[] = $v;
            }
            $this->m_attributes[$n] = implode(" ", $this->m_class_array);
            return;
        }

        return parent::_access_OffsetSet($n, $v);
    }
    /**
     * init node modication to handle node creation detection
     * @return void 
     */
    public static function Init(): bool
    {
        if (!self::$sm_filter_callback) {
            self::$sm_filter_callback = function ($e) {
                return self::_PrefilterCreateNode($e);
            };
            igk_reg_hook(\IGKEvents::FILTER_PRE_CREATE_ELEMENT, self::$sm_filter_callback);
            return true;
        }
        return false;
    }
    private static function _PrefilterCreateNode($e)
    {
        self::$sm_sys_modify = true;
        return null;
    }
    /**
     * remove node modification detection
     * @return void 
     */
    public static function UnInit()
    {
        (self::$sm_filter_callback) && igk_unreg_hook(\IGKEvents::FILTER_PRE_CREATE_ELEMENT, self::$sm_filter_callback);
        self::$sm_sys_modify = false;
        self::$sm_filter_callback = null;
    }
    /** 
     * system modifity on compilation
     */
    public static function SysModify(): bool
    {
        return self::$sm_sys_modify;
    }
    public function getCanRenderTag()
    {
        return false;
    }
    public function setContent($v)
    {
        $this->m_modify = true;
        return $this->text($v);
    } 
}
