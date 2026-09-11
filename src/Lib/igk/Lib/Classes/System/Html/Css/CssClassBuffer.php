<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassBuffer.php
// @date: 20260904 12:25:05
namespace IGK\System\Html\Css;
/**
* auto generate doc.
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Html\Css
*/
class CssClassBuffer
{
    /**
    * auto generate doc.
    * @var mixed
    * @return void
    */
    private $m_data;
    /**
    * .ctr
    * @return void
    */
    private function __construct()
    {
        $this->m_data = [];
    }
    /**
    * auto generate doc.
    * @return CssClassBuffer
    */
    public static function getInstance()
    {
        return igk_environment()->createClassInstance(static::class, function () {
            return new static;
        });
    }
    /**
     * load definition rendering 
     * @param array|string $values 
     * @return void 
     */
    public function loadBuffer($values)
    {
        if (is_string($values)) {
            $values = array_filter(explode(' ', $values));
        } else {
            $values = array_keys(array_filter($values));
        }
        $v_d = &$this->m_data;
        while (count($values) > 0) {
            $q = trim(array_shift($values), '+ ');
            if (igk_str_startwith($q, '-'))
                continue;
            if (!isset($v_d[$q])) {
                $v_d[$q] = 0;
            }
            $v_d[$q]++;
        }
    }
    /**
     * retrieve data 
     * @return array 
     */
    public function to_array()
    {
        return $this->m_data;
    }
    /**
     * clear data 
     * @return void 
     */
    public function clear()
    {
        $this->m_data = [];
    }
    /**
     * render Extra definition 
     * @param mixed $doc 
     * @param mixed $options {lf:string} 
     * @return string 
     */
    public function renderExtraDefinition(?IGKHtmlDoc $doc=null, $options=null)
    {
        $css_def = $this;
        $doc = $doc ?? igk_app()->getDoc() ?? igk_die("to render extra class definition, we require a document");
        $tab = $css_def->to_array();
        ksort($tab);
        CssUtils::InitSysGlobal($doc);
        $systheme = $doc->getSysTheme();
        foreach ($tab as $k => $v) {
            if (isset($systheme['.' . $k])) {
                unset($tab[$k]);
            }
        }
        $detector = new CssClassNameDetector;
        $references = [];
        $detector->map([]);
        $tab = array_filter($tab);
        if (!empty($tab)){
            $detector->loadReferences(array_keys($tab), $references);
        } 
        return $detector->renderToCss($references, $options);
    }
}
