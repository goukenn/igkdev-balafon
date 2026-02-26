<?php
// @author: C.A.D. BONDJE DOUE
// @file: EventArgs.php
// @date: 20260206 13:28:03
namespace IGK\System\Core;


/**
 * 
 * @package IGK\System\Core
 * @author C.A.D. BONDJE DOUE
 */
class EventArgs
{

    /**
    * Property: empty.
    * @var mixed
    */
    private  static $Empty;

    /**
    * Property: props.
    * @var mixed
    */
    private $m_props;
    /**
     * retrieve the keys to define
     * @param string $key 
     * @return mixed 
     */

    public function get(string $key){
        return igk_getv($this->m_props, $key);
    }
    /**
     * get empty object 
     * @return mixed 
     */

    public static function Empty()
    {
        if (empty(self::$Empty)) {
            self::$Empty = new static;
        }
        return self::$Empty;
    }

    /**
    * .ctr
    * @param null|mixed $props
    */
    public function __construct($props=null)
    {
        $this->m_props = $props ?? [];
    }
}
