<?php
// @author: C.A.D. BONDJE DOUE
// @file: DotEnvVarConfiguration.php
// @date: 20260718 13:04:11
namespace IGK\System\IO;


/**
* 
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\IO
*/
class DotEnvVarConfiguration{
    /**
    * auto generate doc.
    * @var ?string
    */
    private $m_expression;

    /**
     * retrieve keys 
     * @var string
     */
    private $m_key;
    /**
    * .ctr
    * @param string $expression
    * @param string $key
    * @return void
    */
    public function __construct(string $expression, string $key)
    {
        $this->m_expression = $expression;
        $this->m_key = $key;
    }
    /**
    * auto generate doc.
    * @return string
    */
    public function getExpression(){
        return $this->m_expression;
    }
    /**
    * auto generate doc.
    * @return void
    */
    public function getKey(){
        return $this->m_key;
    }
    /**
    * get string presentation.
    * @return void
    */
    public function __toString()
    { 
        throw new \Exception('Not implemented');
    }
}