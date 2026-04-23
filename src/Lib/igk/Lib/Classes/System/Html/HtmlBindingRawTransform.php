<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlBindingRawTransform.php
// @date: 20221010 14:35:02
namespace IGK\System\Html;

/**
 * use to transform binding data
 * @package IGK\System\Html
 */
class HtmlBindingRawTransform
{
    /**
     * definition name
     * @var string
     */
    var $name = "raw";
    /**
     * data to evaluate
     * @var mixed
     */
    var $data;
    /**
     * attache pipe
     * @var mixed
     */
    var $pipe;
    /**
    * Property: root context.
    * @var mixed
    */
    var $root_context;
    /**
     * controller
     * @var ?BaseController 
     */
    var $controller;
    /**
     * data key
     * @var mixed
     */
    var $key;
    /**
    * .ctr
    * @param string $name
    */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    /**
    * get string presentation.
    */
    public function __toString()
    {
        if (is_string($this->data)) {
            $s = ltrim($this->data ?? '');
            if (strpos($s, "return ")!==false) {
                $s = substr($s, 7);
            }
            return empty($this->pipe) ?  "<?= $s ?>" : "<?= igk_str_pipe_value($s, '{$this->pipe}') ?>";
        } else {
            if (!is_null($this->key)){
                $s = '$'.$this->name;
                return empty($this->pipe) ?  "<?= $s ?>" : "<?= igk_str_pipe_value($s, '{$this->pipe}') ?>";
            }
            return '[data:transform ::: '.json_encode($this->data).']';
        }
    }
}