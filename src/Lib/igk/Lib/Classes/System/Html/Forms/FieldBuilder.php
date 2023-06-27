<?php
// @author: C.A.D. BONDJE DOUE
// @file: FieldBuilder.php
// @date: 20230516 14:49:08
namespace IGK\System\Html\Forms;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html\Forms
 */
class FieldBuilder implements IteratorAggregate
{

    const LengthFields =  ['text', 'password','textarea'];

    private $m_data = [];

    /**
     * current fields
     * @var mixed
     */
    private $m_current;


    public function to_array()
    {
        return $this->m_data;
    }
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->m_data);
    }

    private function _add(string $key, ?array $attribs)
    {
        $this->m_current = [];
        if ($attribs){
            $this->m_current['attribs'] = $attribs;
        }
        $this->m_data[$key] = &$this->m_current;
        return $this->m_current;
    }

    public function text(string $name, ?array $attribs = null)
    {
        $this->_add($name, $attribs);
        return $this;
    }
    public function password(string $name, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'password';
        return $this;
    }
    public function radio(string $name, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'radio';
        return $this;
    }
    public function checkbox(string $name, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'checkbox';
        return $this;
    }

    public function datetime(string $name, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'datetime';
        return $this;
    }
    public function email(string $name, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'email';
        return $this;
    }
    public function number(string $name, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'number';
        return $this;
    }
    /**
     * add select combobox
     * @param string $name 
     * @param mixed $data 
     * @param null|array $attribs 
     * @return $this 
     */
    public function select(string $name, $data, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'select';
        return $this;
    }
    /**
     * add hidden input
     * @param string $name 
     * @param mixed $data 
     * @param null|array $attribs 
     * @return $this 
     */
    public function hidden(string $name, $data, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'hidden';
        return $this;
    }
    /**
     * add datalist input
     * @param string $name 
     * @param mixed $data 
     * @param null|array $attribs 
     * @return $this 
     */
    public function datalist(string $name, $data, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'hidden';
        return $this;
    }
    /**
     * add text area
     * @param string $name 
     * @param null|array $attribs 
     * @return $this 
     */
    public function textarea(string $name, ?array $attribs = null)
    {
        $this->m_current = $this->_add($name, $attribs);
        $this->m_current["type"] = 'textarea';
        return $this;
    }


    /**
     * mark field set
     * @param null|string $caption 
     * @return $this 
     */
    public function fieldset(?string $caption = null)
    {
        $m =  ['type' => 'fieldset'];
        if ($caption) {
            $m['legend'] = $caption;
        }
        $this->m_data[] = $m; // ['type'=>'fieldset'];
        // $this->m_current = null;
        unset($this->m_current);
        $this->m_current = null;

        return $this;
    }
    /**
     * mark end fieldset 
     * @return $this 
     */
    public function endfieldset()
    {
        if ($this->m_current) {
            $this->m_data[] = ['type' => 'efieldset'];
            unset($this->m_current);
            $this->m_current = null;
        }
        return $this;
    }



    // setter 
    public function placeholder(string $n)
    {
        if ($this->m_current) {
            if (in_array(igk_getv($this->m_current, 'type', 'text'), ['text', 'password','textarea'])) {
                $this->m_current['placeholder'] = $n;
            }
        }
        return $this;
    }
    public function maxLength(string $n)
    {
        if ($this->m_current) {
            if (in_array(igk_getv($this->m_current, 'type', 'text'), self::LengthFields)) {
                $this->m_current['maxlength'] = $n;
            }
        }
        return $this;
    }

    public function label(string $text)
    {
        $this->m_current["text"] = $text;
        return $this;
    }
    public function id(string $id)
    {
        if ($this->m_current) {
            $this->m_current['id'] = $id;
        }
        return $this;
    }

    public function allowEmpty(?bool $allow)
    {
        if ($this->m_current) {
            $this->m_current['allow_empty'] = $allow;
        }
        return $this;
    }
    /**
     * set selection empty value
     * @param mixed $data 
     * @return $this 
     */
    public function emptyValue($data)
    {
        if ($this->m_current) {
            $this->m_current['empty_value'] = $data;
        }
        return $this;
    }

    /**
     * html items actions bars
     * @param array|callable of fields action bar
     */
    public function actionbar($fields)
    {
        $action = igk_html_node_actionbar($fields);
        $this->m_data[] = $action;
        return $this;
    }

    /**
     * set text fields validation pattern
     * @param null|string $pattern 
     * @return static
     */
    public function pattern(?string $pattern = null){
        if ($this->m_current) {
            if (in_array(igk_getv($this->m_current, 'type', 'text'), self::LengthFields)) {
                $this->m_current['pattern'] = $pattern;
            }
        }
        return $this;
    }
}
