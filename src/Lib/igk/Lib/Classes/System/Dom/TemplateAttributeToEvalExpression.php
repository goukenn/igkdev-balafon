<?php
// @author: C.A.D. BONDJE DOUE
// @file: TemplateAttributeToEvalExpression.php
// @date: 20251229 16:05:13
namespace IGK\System\Dom;


/**
* 
* @package IGK\System\Dom
* @author C.A.D. BONDJE DOUE
*/
class TemplateAttributeToEvalExpression{

    /**
    * .ctr
    * @param string $expression
    * @param string $pipe
    */
    public function __construct(protected string $expression , protected string $pipe)
    {
    }
    /**
     * 
     * @return bool 
     */

    public function useAttribName():bool{
        return true;
    }

    /**
    * auto generate doc.
    * @return string
    */
    public function getValue(): string{
        $raw = $this->expression;
        $pipe = trim($this->pipe);
        return '<?= igk_str_pipe_value('.$raw.',\''.$pipe.'\') ?>';
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        throw new \Exception('Not implemented');
    }
}