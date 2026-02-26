<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PropertyVisibleWatcher.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html;
/**
 * ally visible for property not null
 * @package IGK\System\Html
 */
class PropertyVisibleWatcher{

    /**
    * Property: p.
    * @var mixed
    */
    private $p;

    /**
    * Property: prop.
    * @var mixed
    */
    private $prop;

    /**
    * .ctr
    * @param mixed $c
    * @param string $prop
    */
    public function __construct($c, string $prop){
        $this->p = $c;
        $this->prop = $prop;
    }

    /**
    * Called when an object is used as a function.
    */
    public function __invoke()
    {
        return $this->visible();
    }

    /**
    * Visible.
    */
    public function visible(){
        // igk_wln_e("the p ", $this->p->{$this->prop} );
        return $this->p !==null; 
    }
}