<?php
// @file: IGKHtmlStyleValueAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
use IGK\System\Html\Css\CssStyle;
use IGK\System\Html\Dom\HtmlCssValueAttribute;

/**
* auto generate doc.
* @package IGK\System\Html
*/
final class HtmlStyleValueAttribute extends HtmlAttributeValue
{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_o;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $value;
    /**
     * Constructor.
     *
     * @param mixed $target The target HTML node that owns this style attribute
     */

    public function __construct($target)
    {
        $this->m_o = $target;
    }
    /**
     * Return the list of properties to serialize, or empty array when value is unset.
     *
     * @return array Properties to include during serialization
     */

    public function __sleep()
    {
        if (empty($this->value)) {
            return array();
        }
        return array("m_v", "m_o");
    }
    /**
     * Return debug information for the object (empty to suppress internal state).
     *
     * @return array Empty debug info array
     */

    public function __debugInfo()
    {
        return [];
    }
    /**
     * Return the string representation of the style attribute value.
     *
     * @return string The resolved style value
     */

    public function __toString()
    {
        $rv = $this->getValue();
        if ($rv === null) {
            igk_wln_e(__FILE__ . ":" . __LINE__, "someting missing for stylevalue attribute ");
        }
        return $rv;
    }
    /**
     * Restore the object state after unserialization.
     */

    function __wakeup() {}
    /**
     * Compute and return the CSS style attribute value, merging class styles when needed.
     *
     * @param mixed $options Optional rendering options
     * @return string|null The resolved style string, or null if empty
     */

    public function getValue($options = null)
    {
        $opt = IGK_STR_EMPTY;
        $v_value = $this->value;
        if (igk_xml_is_mailoptions($options)) {
            $p = $this->m_o["class"];
            $style = new CssStyle();
            $s = trim($p ? $p->EvalClassStyle() : IGK_STR_EMPTY);
            if ($v_value) {
                $tg = $v_value->getValue($options);
                if ($tg) {
                    $style->load($tg, 0, null);
                    $v_value = null;
                }
            }
            if (!empty($s))
                $style->Load($s, 1, $p);
            $opt .= igk_css_get_style_from_map($this->m_o, $options, $style);
        }
        if (!empty($opt) && !empty($v_value))
            $opt .= " ";
        if (is_object($v_value)) {
            $opt .= $v_value->getValue($options);
        } else {
            $opt = $opt . $v_value;
        }
        return empty($opt) ? null : $opt;
    }
    /**
     * Set the style value, accepting strings, null, or style attribute instances.
     *
     * @param mixed $value The style value to assign
     * @return static|void Returns $this when reassigning from another instance
     */

    public function setValue($value)
    {
        if ($value instanceof HtmlStyleValueAttribute) {
            $this->value = $value->getValue();
            return $this;
        }
        if (($value == null) || is_string($value) || ($value instanceof IHtmlStyleAtribute))
            $this->value = $value;
        else {
            igk_die("no value allowed " . $value . " target :" . get_class($this->m_o));
        }
    }
}