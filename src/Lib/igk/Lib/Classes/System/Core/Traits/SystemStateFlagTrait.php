<?php
// @author: C.A.D. BONDJE DOUE
// @file: SystemStateFlagTrait.php
// @date: 20250801 04:10:37
namespace IGK\System\Core\Traits;


/**
 * 
 * @package IGK\System\Core\Traits
 * @author C.A.D. BONDJE DOUE
 */
trait SystemStateFlagTrait
{
    /**
     * store state flags definition 
     * @var array
     */
    protected $m_flags = [];

    /**
     * 
     * @param string $name 
     * @param mixed $value 
     * @return void 
     */
    public function setFlag(string $name, $value)
    {
        $this->m_flags[$name] = $value;
    }
    /**
     * 
     * @param string|'no-flag'|'line-flag' $name 
     * @return mixed 
     * @throws Exception 
     */
    public function getFlag(string $name)
    {
        return igk_getv($this->m_flags, $name);
    }
    /**
     * 
     * @param string|'no-flag' $name 
     * @return void 
     */
    public function unsetFlag(string $name)
    {
        unset($this->m_flags[$name]);
    }

    /**
    * Isset flag.
    * @param string $name
    * @return string
    */
    public function issetFlag(string $name): string
    {
        return isset($this->m_flags[$name]);
    }

    /**
    * Clears Flags.
    */
    protected function clearFlags()
    {
        $this->m_flags = [];
    }

    /**
    * Loads Flags.
    * @param array $flags
    */
    protected function loadFlags(array $flags)
    {
        $this->m_flags = $flags;
    }

    /**
     * bind flags 
     * @param mixed $flag 
     * @param null|callable $filter 
     * @return void 
     */

    public function updateFlags($flag, ?callable $filter = null)
    {
        $filter = $filter ?? function ($k) {
            return  preg_match('/-flag$/', $k);
        };
        foreach ($flag as $k => $v) {
            if ($filter($k) && $v) {
                $this->setFlag($k, $v);
            }
        }
    }
}
