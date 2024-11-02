<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FormBuilder.php
// @date: 20220803 13:48:55
// @desc: 

namespace IGK\System\Html;

use Closure;
use Error;
use Exception;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlCssClassValueAttribute;
use IGK\System\Html\Dom\HtmlItemBase;
use IGKException;
use ReflectionException;
use IGK\System\Html\Forms\FormBuilderComponentTypes as formTypes;
use IGK\System\Html\Forms\IFormInternalIDSupport;

use function igk_resources_gets as __;

/**
 * default form builder
 * @package IGK\System\Html
 */
class FormBuilder
{
    /**
     * get or set the form builder data source. to set dynamic value
     * @var Closure|array|IFormBuilderDataSource
     */
    var $datasource;
    static $ResolvType = [
        "number" => "text",
        "tel" => "text",
        "float" => "text",
        "int" => "text",
        "email" => "email",
        "password" => "password",
        "text" => "text",
        "date" => "date",
        "datetime" => "datetime-local",
        "json" => "text",
        "radio" => "radio",
        "checkbox" => "checkbox",
        "file" => "file",
        "hidden" => "hidden",
        "datetime-local" => "datetime-local"
    ];
    private static $ResolvClass = [
        "float" => "igk-form-control number",
        "double" => "igk-form-control number",
        "number" => 'igk-form-control number',
        "int" => "igk-form-control integer",
        "text" => 'igk-form-control text',
        "mail" => 'igk-form-control mail',
        "url" => 'igk-form-control url',
        "password" => 'igk-form-control password',
        "email" => 'igk-form-control email',
        'datetime' => 'igk-form-control datetime',
        'datetime-local' => 'igk-form-control datetime-local',
    ];

    /**
     * retrieve attribute args
     * @param mixed $attr 
     * @return mixed 
     * @throws Exception 
     */
    private static function _GetAttribArgs($attr)
    {
        $key = null;
        foreach (["attrs", "attribs", "attributes"] as $m) {
            if (isset($attr[$m])) {
                $key = $m;
                break;
            }
        }
        return $key ? igk_getv($attr, $key) : null;
    }
    /**
     * build form fields
     * @param array $formFields 
     * @param int $render 
     * @param mixed $engine 
     * @param string $tag 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function build(array $formFields, $render = 0, $engine = null, $tag = "div")
    {
        $o = "";
        $clprop = new HtmlCssClassValueAttribute();
        $get_attr_key = function ($v) {
            $key = null;
            foreach (["attrs", "attribs", "attributes"] as $m) {
                if (isset($v[$m])) {
                    $key = $m;
                    break;
                }
            }
            return $key;
        };
        if (empty($tag)) {
            $tag = "div";
        }

        $clprop->add("data");

        $load_attr = function ($v, &$o) use ($get_attr_key,  $clprop) {
            $clprop->clear();
            $key = $get_attr_key($v);
            $v_def_form_control = igk_environment()->get("css/default/controlstyle", "igk-form-control form-control");
            if ($key === null) {
                //default engine form control
                $e = igk_get_selected_builder_engine();
                if ($e) {
                    $o .= $e->initAttributes($key, $v, $clprop);
                } else {
                    $clprop->setClasses($v_def_form_control);
                }
                if (!empty($defclass = $clprop->getValue())) {
                    $o .= "class=\"" . $defclass . "\"";
                }
                return;
            }
            $clprop->setClasses($v_def_form_control);
            foreach ($v[$key] as $k => $v) {
                if ($k == 'class') {
                    $clprop->setClasses($v);
                } else {
                    $o .= $k . "=\"" . $v . "\" ";
                }
            }
            if (!empty($defclass = $clprop->getValue())) {
                $o .=  "class=\"" . $defclass . "\" ";
            }
            $o = rtrim($o);
        };
        $bindValue = function (&$o, &$fieldset, $k, $v) use ($get_attr_key, $load_attr, $tag) {
            $v_k_id = null;
            if ($v instanceof IFormInternalIDSupport) {
                $v_k_id = $v->getInternalId();
            }
            if (!is_array($v)) {
                $v = (array)$v;
            }
            $attr_key = $get_attr_key($v);
            $ResolvClass = self::$ResolvClass;
            $ResolvType = self::$ResolvType;
            $v_k_id = $v_k_id ?? $k;

            $_value = is_array($v) && key_exists("value", $v) ? $v["value"] : "";
            if ($attr_key) {
                if (isset($v[$attr_key]["value"])) {
                    $_value = $v[$attr_key]["value"];
                    unset($v[$attr_key]["value"]);
                }
            }
            $_value = $this->_getDataSourceValue($_value, $v_k_id);

            $_type = strtolower(isset($v["type"]) ? $v["type"] : "text");
            $_allow_empty = isset($v["allow_empty"]) ? $v["allow_empty"] : "";
            $_empty_value = isset($v["empty_value"]) ? $v["empty_value"] : "0";

            // + | --------------------------------------------------------------------
            // + | handle special type 
            // + |

            if ($_type == "fieldset") {
                if ($fieldset) {
                    $o .= "</fieldset>";
                    $fieldset = 0;
                }
                $o .= "<fieldset ";
                $load_attr($v, $o);
                $o .= ">";
                if (isset($v["legend"])) {
                    $o .= "<legend>" . $v["legend"] . "</legend>";
                }
                $fieldset = 1;
                return;
            }
            if ($_type === "efieldset") {
                if ($fieldset) {
                    $o .= "</fieldset>";
                    $fieldset = 0;
                }
                return;
            }

            if (preg_match("/\\b(button|submit|reset)\\b/", $_type)) {

                if (method_exists($this, $fc = 'build_' . $_type)) {
                    $args = [&$o, $v];
                    call_user_func_array([$this, $fc], $args);
                }

                return;
            }

            // + | --------------------------------------------------------------------
            // + | build node
            // + |

            $_id = "";
            $t_id = igk_getv($v, "id", $k);
            if ($t_id) {
                $_id = ' id="' . $t_id . '"';
            }
            $_name = "";
            if (isset($v["name"])) {
                $_name = " name=\"" . $v["name"] . "\""; // name=\"{$k}\" ";
            } else {
                $_name = " name=\"{$k}\"";
            }

            $_is_required = isset($v["required"]) ? $v["required"] : 0;

            $label_text = ucfirst(igk_getv($v, "label_text", __($k)));
            if (!$this->isHtmlType($_type) && is_subclass_of($_type, FormBuilderItemAbstractType::class)) {
                $v_ctype = new $_type();
                $v_ctype->setName($k);
                $v_ctype->setAttributes(array_merge($v, [
                    'label_text' => $label_text
                ]));
                $v_ctype->setId($t_id);
                $o .= $v_ctype->render();
                return;
            }
            $_is_div = !preg_match("/(hidden|fieldset|button|submit|reset|datalist)/", $_type);
            $class_style = 'igk-form-group ' . $_type;
            if ($_is_div) {
                $o .= "<" . $tag . " ";
                if ($_is_required) {
                    $class_style .= ' required';
                }
                $o .= "class=\"$class_style\" ";
                $o = rtrim($o) . ">";
            }

            if (!preg_match("/(hidden|fieldset|button|submit|reset|datalist)/", $_type)) {
                $g = HtmlUtils::GetFilteredAttributeString("label", array_merge([
                    'class' => "igk-form-label"
                ], igk_getv($v, 'label_attribs') ?? []));
                $c_id = ($t_id) ? "for='{$t_id}'" : "";
                $o .= "<label {$c_id}$g>" . $label_text . "</label>";
            }
            switch ($_type) {
                case formTypes::Fieldset:
                    break;
                case formTypes::Textarea:
                    $o .= "<textarea{$_name}{$_id}";
                    if (isset($v["placeholder"])) {
                        $o .= " placeholder=\"{$v["placeholder"]}\" ";
                    }
                    $load_attr($v, $o);
                    if ($_is_required) {
                        $o .= " required=\"true\" ";
                    }
                    $o = rtrim($o) . ">{$_value}</textarea>";
                    break;
                case formTypes::RadioGroup:
                    $o .= '<' . $tag . ' style="display:inline-block;">';
                    foreach ($v["data"] as $kk => $vv) {
                        $o .= '<span >' . __($kk) . '</span><input type="radio" name="' . $k . '"' . $_id . ' value="' . $vv . '" />';
                    }
                    $o .= "</{$tag}>";
                    break;

                case formTypes::Datalist:
                    if (empty($_id)) {
                        $_id = " id=\"{$k}\"";
                    }
                    $o .= "<datalist" . $_id;
                    $load_attr($v, $o);
                    $o .= ">";
                    if (isset($v["data"]) && is_array($_tab = $v["data"])) {
                        foreach ($_tab as $row) {
                            $o .= "<option ";
                            $o .= "value=\"{$row['i']}\" ";
                            $o .= ">";
                            $o .= isset($row["t"]) ? __($row["t"]) : "";
                            $o .= "</option>";
                        }
                    }
                    $o .= "</datalist>";
                    break;
                case formTypes::Select:
                    $k_data = "";
                    $bas = isset($v["selected"]) ? $v["selected"] : null;
                    $m_data = null;
                    if (isset($v["data"]) && is_string($m_data = $v["data"])) {
                        $k_data = "data=\"" . $m_data . "\" ";
                    }
                    $_id = ' id="' . $t_id . '"';
                    // if ($bas){
                    //     $k_data.= "selected=\"{$bas}\" ";
                    // }
                    $o .= "<select" . $_name . $_id . $k_data . " ";
                    $load_attr($v, $o);
                    $o .= ">";
                    if ($_allow_empty) {
                        $o .= "<option ";
                        $o .= "value=\"{$_empty_value}\"></option>";
                    }
                    $_tab = $this->_getSelectDataOptions($v_k_id, is_array($m_data) ? $m_data : null);
                    if ($_tab) {
                        usort($_tab, [self::class, '_SelectSortBySorkByText']);
                        foreach ($_tab as $row) {
                            $o .= "<option ";
                            $o .= "value=\"{$row['i']}\" ";
                            if ((isset($bas) && ($bas == $row['i'])) || (igk_getv($row, 'selected'))) {
                                $o .= "selected ";
                            }
                            if (isset($row['data-tip'])) {
                                $o .= "data-tip=\"" . $row['data-tip'] . "\" ";
                            }
                            $o .= ">";
                            $o .= $row["t"];
                            $o .= "</option>";
                        }
                    }
                    $o .= "</select>";
                    break;

                case formTypes::Text:
                case formTypes::Hidden:
                case formTypes::Password:
                default:

                    // $_vt = "";
                    if (!empty($_value) || ($_value == "0")) {
                        $v['value'] = $_value;
                    }
                    // $_vt = "value=\"{$_value}\"";
                    $_otype = igk_getv($ResolvType, $_type, "text");
                    $def_type = igk_getv($ResolvClass, $_type, $_type);
                    $o .= "<input";
                    $keys = ['id', 'value', 'maxLength', 'pattern', 'placeholder'];
                    if ($no_place_holder = in_array($_type, ['checkbox', 'radio'])) {
                        array_pop($keys);
                        $keys[] = 'checked';
                    }
                    $tattrib = ["name" => $k];
                    foreach ($keys as $kk) {
                        $tattrib[strtolower($kk)] = igk_getv($v, $kk);
                    }
                    if (!$no_place_holder && empty($tattrib['placeholder'])) {
                        //igk_wln_e(get_defined_vars());
                        $tattrib['placeholder'] = __($k);
                    }
                    if (isset($v["attribs"]))
                        $tattrib["class"] = igk_getv($v["attribs"], "class") . " +" . $def_type;
                    else {
                        $tattrib["class"] = $def_type;
                    }
                    // + | -------------------------------------------------
                    // + | filter attribs
                    // + |
                    unset($v["attribs"]["class"]);
                    if ($p = igk_getv($v, 'attribs')) {
                        $tattrib = array_merge($tattrib, $p ?? []);
                    }

                    $jp = [
                        "type" => $_otype,
                        "id" => $t_id,
                        "value" => $_value,
                    ] + $tattrib;
                    $attrib = new HtmlFilterAttributeArray($jp);
                    //$attrib["class"] = $v["attribs"]["class"];
                    if ($_is_required) {
                        $attrib["required"] = 1;
                    }
                    $attrib = HtmlUtils::PrefilterAttribute("input", $attrib);
                    $o .= ' ' . HtmlRenderer::GetAttributeArrayToString($attrib);
                    $o .= "/>";

                    if (isset($v["tips"])) {
                        $o .= '<div class="tips">' . $v["tips"] . '</div>';
                    }
                    break;
            }
            if ($_is_div) {
                $o .= "</{$tag}>";
            }
        };
        $fieldset = 0;
        foreach ($formFields as $k => $v) {

            if (is_integer($k)) {
                if ($v == "-") {
                    // add separator
                    $o .= "<div class=\"igk-separator\"></div>";
                    continue;
                }
                if (is_string($v)) {
                    $k = $v;
                }
                if (is_object($v)) {
                    if ($v instanceof Closure) {
                        $v = $v() ?? igk_die('must return an Item or HtmlString');
                        if (is_string($v)) {
                            $o .= $v;
                            continue;
                        }
                    }
                    if ($v instanceof HtmlItemBase) {
                        $o .= $v->render();
                        continue;
                    }
                    // igk_wln($k, $v);
                    igk_die(implode('', [__CLASS__, "object not allowed"]));
                }
            }
            if (($cpos = strrpos($k, "[]")) !== false) {
                // + | --------------------------------------------------------------------
                // + | FORM FIELD DEFINITION
                // + |                
                $name = substr($k, 0, $cpos);
                if (is_array($v)) {
                    $ct = count($v);
                    $jc = 0;
                    for ($i = 0; $i < $ct; $i++) {
                        $b = $v[$i];
                        if ($b instanceof FormFieldAttribute) {
                            $b->attribs;
                            continue;
                        }
                        $b['id'] = $name . str_pad($jc . '', 2, STR_PAD_LEFT, '0');
                        $b["name"] = $k;
                        $bindValue($o, $fieldset, $name, $b);
                        $jc++;
                    }
                } else if ($v instanceof FormFieldAttribute) {
                    $data = $v->attribs;
                    $v_fname = igk_getv($data, 'name', $k);
                    $v_ftype = $data['type'];
                    $data = $data['data'];
                    if ($data) {
                        $c = 0;
                        foreach ($data as $c_data) {
                            $c_data['name'] = $v_fname;
                            $c_data['type'] = $v_ftype;
                            if (empty($c_data['id']))
                                $c_data['id'] =  $name . '-' . $c;
                            $bindValue($o, $fieldset, $v_fname, $c_data);
                            $c++;
                        }
                    }
                }
                continue;
            }
            $bindValue($o, $fieldset, $k, $v);
        }
        if ($fieldset) {
            $o .= "</fieldset>";
        }
        if ($render) {
            echo $o;
        }
        return $o;
    }
    /**
     * check whether a string type is html type
     * @param string $type 
     * @return int|false 
     */
    protected function isHtmlType(string $type)
    {
        return preg_match("/(text|checkbox|password|datetime|email|hidden|fieldset|button|submit|reset|datalist|select|number)/", $type);
    }

    /**
     * 
     * @param mixed $a 
     * @param mixed $b 
     * @return int 
     * @throws Exception 
     */
    protected static function _SelectSortBySorkByText($a, $b)
    {
        return strcmp(igk_getv($a, 't'), igk_getv($b, 't'));
    }

    /**
     * get retrieve value data
     * @param mixed $value 
     * @param mixed $id 
     * @return mixed 
     * @throws Exception 
     */
    protected function _getDataSourceValue($value, $id)
    {
        $_value = $value;
        $_source = $this->datasource;
        if ($_source instanceof IFormBuilderDataSource) {
            $_source = $_source->getDataSource();
        }
        if ($_source instanceof Closure) {
            $_value = $_source($id) ?? $value;
        } else {
            $_value = igk_getv($_source, $id) ?? $value;
        }
        return $_value;
    }
    public function _getSelectDataOptions(string $id, array $def_data = null)
    {
        $_source = $this->datasource;
        if ($_source instanceof IFormBuilderDataSource) {
            if ($_data = $_source->getOptionItems()) {
                return igk_getv($_data, $id) ?? $def_data;
            }
        }
        return $def_data;
    }

    /**
     * build select definition info
     * @param mixed $data 
     * @param mixed $key 
     * @param mixed $value 
     * @return void 
     */
    public static function SelectOptions($data, $key, $value)
    {
        $list = [];
        foreach ($data as $row) {
            $list[] = [
                'i' => $row->{$key},
                't' => $row->{$value}
            ];
        }
        return $list;
    }

    public function build_submit(string &$o, $attrib)
    {
        $_closed = false;
        $o .= '<input type="submit" ';
        $tm = ['class' => 'button submit primary'];

        if ($arg = $attrib ? self::_GetAttribArgs($attrib) : null) {
            if ($cl = igk_getv($arg, 'class')) {
                $tm['class'] = array_unique(array_merge(
                    explode(' ', $tm['class']),
                    explode(' ', $cl)
                ));
                unset($arg['class']);
            }
            $tm = array_merge($tm, $arg);
        }
        self::_LoadAttributes($o, $tm);
        // if ($_closed) {
        //     $o .= "</button>";
        // } else {
            $o .= '/>';
        // }
    }

    public function build_button(string &$o, $attrib)
    {
        $_closed = false;
        $o .= '<input type="button" ';
        $tm = ['class' => 'igk-form-control button primary'];
        $tm['value'] = igk_getv($attrib, 'value');
        if ($arg = $attrib ? self::_GetAttribArgs($attrib) : null) {
            if ($cl = igk_getv($arg, 'class')) {
                $tm['class'] = array_unique(array_merge(
                    explode(' ', $tm['class']),
                    explode(' ', $cl)
                ));
                unset($arg['class']);
            }
            $tm = array_merge($tm, $arg);
        }
        self::_LoadAttributes($o, $tm);
        // if ($_closed) {
        //     $o .= "</button>";
        // } else {
            $o .= '/>';
        // }
    }
    /**
     * load html definition attributes
     * @param string &$o 
     * @param mixed $tm 
     * @return void 
     * @throws Exception 
     * @throws Error 
     * @throws IGKException 
     * @throws CssParserException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    private static function _LoadAttributes(string &$o, $tm)
    {
        $clprop = new HtmlCssClassValueAttribute();
        foreach ($tm as $k => $v) {
            if ($k == 'class') {
                $clprop->setClasses($v);
                continue;
            }
            $o .= $k . "=\"" . htmlentities($v) . "\" ";
        }
        $s = $clprop->getValue();
        if ($s) {
            $o .= 'class="' . $s . '"';
        }
    }
}
