<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlBindingRawTransform.php
// @date: 20221010 14:35:02
namespace IGK\System\Html;


///<summary></summary>
/**
* use to transform binding data
* @package IGK\System\Html
*/
class HtmlBindingRawTransform{
    /**
     * definition name
     * @var string
     */
    var $name="raw";
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

    var $root_context;

    /**
     * controller
     * @var ?BaseController 
     */
    var $controller;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function __toString()
    {
        $s = ltrim($this->data);
        if (strpos($s, "return ")){
            $s = substr($s, 7);
        }
        return empty($this->pipe)?  "<?= $s ?>" : "<?= igk_str_pipe_value($s, '{$this->pipe}') ?>";
    }
}