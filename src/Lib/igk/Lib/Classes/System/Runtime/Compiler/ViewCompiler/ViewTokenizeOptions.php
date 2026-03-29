<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewTokenizeOptions.php
// @date: 20221021 09:07:40
namespace IGK\System\Runtime\Compiler\ViewCompiler;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\IReadTokenOptions;
use IGK\System\Runtime\Compiler\ReadTokenOptions;
/**
* 
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
/**
* auto generate doc.
* @package IGK\System\Runtime\Compiler\ViewCompiler
*/
class ViewTokenizeOptions extends ReadTokenOptions implements IReadTokenOptions{
    /**
     * start reading source detect <?php code
     * @var false
     */
    var $start = false;
    /**
     * output to generate 
     * @var string
     */
    private $m_output;
    /**
     * buffer to generate
     * @var mixed
     */
    var $buffer='';
    /**
     * store buffer 
     * @var array
     */
    var $buffers = [];
    /**
     * store loaded variable
     * @var array
     */
    var $variables = [];
    /**
     * tokenize
     * @var mixed
     */
    var $flag;
    /**
    * Property: flag options.
    * @var mixed
    */
    var $flagOptions;
    /**
    * Property: skip white space.
    * @var mixed
    */
    var $skipWhiteSpace = 0;
    /**
     * bracket depth counter
     * @var int
     */
    var $depth = 0;
    /**
     * block to read
     * @var ?ViewTokenBlock
     */
    var $block;
    /**
    * Property: comment.
    * @var mixed
    */
    var $comment;
    /**
    * Property: modifiers.
    * @var mixed
    */
    var $modifiers = [];
    /**
    * Property: php doc.
    * @var mixed
    */
    var $phpDoc;
    /**
    * Property: struct info.
    * @var mixed
    */
    var $struct_info;
    /**
    * auto generate doc.
    * @var ?ReadTokenOptions
    */
    var $options;
    /**
    * Outputs.
    * @return ?string
    */
    public function output():?string{
        $sb = new StringBuilder($this->m_output);
        $sb->appendLine("<?php");
        if ($this->buffer)
            $sb->append($this->buffer);
        return $this->m_output;
    }
}