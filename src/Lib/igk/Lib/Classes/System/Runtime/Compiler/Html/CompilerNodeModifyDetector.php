<?php
// @author: C.A.D. BONDJE DOUE
// @file: CompilerNodeModifyDetector.php
// @date: 20221011 11:17:20
namespace IGK\System\Runtime\Compiler\Html;

use IGK\System\Html\Dom\HtmlNode;

///<summary></summary>
/**
 * detect modification node
 * @package IGK\System\Runtime\Compiler\Html
 * @parametter clearFlag
 */
class CompilerNodeModifyDetector extends HtmlNode
{
    private $m_modify = false;
    protected $tagname = "igk:compiler-modifynode-detector";
    private $m_freezeClearModify = false;
    static $sm_sys_modify = false;
    static $sm_filter_callback = null;

    const CLEAR_FLAG_PARAM = "clearFlag";

    public function clearChilds()
    {
        if (!$this->m_freezeClearModify){
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
        return $this->m_modify || ($this->getChildCount() > 0);
    }
    public function getFreezeClearModify(){
        return $this->m_freezeClearModify;
    }
    public function setFreezeClearModify(bool $freeze){
        $this->m_freezeClearModify = $freeze;
    }
    public function _access_OffsetSet($n, $v)
    {
        $this->m_modify = true;
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
}
