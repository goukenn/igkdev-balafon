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
class DotEnvVarConfiguration{
    /**
     * 
     * @var ?string
     */
    private $m_expression;

    /**
     * retrieve keys 
     * @var string
     */
    private $m_key;

    public function __construct(string $expression, string $key)
    {
        $this->m_expression = $expression;
        $this->m_key = $key;
    }
    /**
     * 
     * @return string 
     */
    public function getExpression(){
        return $this->m_expression;
    }
    public function getKey(){
        return $this->m_key;
    }
    public function __toString()
    { 
        throw new \Exception('Not implemented');
    }
}