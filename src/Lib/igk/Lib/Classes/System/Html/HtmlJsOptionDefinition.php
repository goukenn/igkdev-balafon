<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlJsOptionDefinition.php
// @date: 20230429 23:03:49
namespace IGK\System\Html;

use IGK\Helper\BalafonJSHelper;

///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class HtmlJsOptionDefinition implements IHtmlGetValue{

    private $m_callbable;
    private $m_name;
    public function __construct(string $name, \Closure $options)
    {
        (empty(trim($name)) || preg_match("/[^0-9\.a-z_]/i", $name ) ) && igk_die("name not valid");
        $this->m_name = $name;
        $this->m_callbable = $options;
    }

    public function getValue($options = null) {
        $name = $this->m_name;
        $o = $this->m_callbable;
        $r = $o($options);
        if ($r){
            $r = BalafonJSHelper::Stringify($r);			
        } else {
            $r = '{}';
        } 
        return sprintf('igk.system.defineOption("%s", %s)', $name, $r);
    }
}