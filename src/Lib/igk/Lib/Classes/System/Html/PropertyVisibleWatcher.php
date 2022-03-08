<?php
namespace IGK\System\Html;

/**
 * ally visible for property not null
 * @package IGK\System\Html
 */
class PropertyVisibleWatcher{
    private $p;
    private $prop;
    public function __construct($c, string $prop){
        $this->p = $c;
        $this->prop = $prop;
    }
    public function __invoke()
    {
        return $this->visible();
    }
    public function visible(){
        // igk_wln_e("the p ", $this->p->{$this->prop} );
        return $this->p !==null; 
    }
}