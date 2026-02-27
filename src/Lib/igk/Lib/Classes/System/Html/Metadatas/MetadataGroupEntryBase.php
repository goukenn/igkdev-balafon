<?php
// @author: C.A.D. BONDJE DOUE
// @file: MetadataGroupEntryBase.php
// @date: 20231221 15:12:51
namespace IGK\System\Html\Metadatas;
use IGK\System\IO\StringBuilder;

/**
* auto generate doc.
* @package IGK\System\Html\Metadatas
*/
abstract class MetadataGroupEntryBase{

    /**
    * Property: def.
    * @var mixed
    */
    protected $m_def = [];

    /**
    * Map.
    * @return array
    */
    abstract function map():array;

    /**
    * Renders.
    */
    public function render(){
        $s = new StringBuilder;
        $n = basename(igk_str_rm_last(igk_uri(static::class), "Metadata"));
        $s->appendLine("\n<!-- ".$n." -->");
        foreach($this->m_def as $p){
            $s->appendLine($p->render());
        }
        return $s.'';
    }

    /**
    * Sets Property.
    * @param string $n
    * @param mixed $v
    */
    public function setProperty(string $n, $v){
        $key = igk_getv($this->map(), $n);
        $m = igk_create_node('meta');
        $m['property']= $key;
        $m['content'] = $v; //$this->{$k};
        $this->{$n} = $v;
        $this->m_def[$n] = $m;
    }

    /**
    * Returns Set Data Callback.
    */
    public function getSetDataCallback(){
        return function($v, $n){
            $this->setProperty($n , $v);
        };
    }

    /**
    * Returns true if Dirty.
    */
    public function isDirty(){
        return count($this->m_def);  
    }
}