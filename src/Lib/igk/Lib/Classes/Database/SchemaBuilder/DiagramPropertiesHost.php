<?php
 
// @author: C.A.D. BONDJE DOUE
// @filename: d.php
// @date: 20220531 13:34:45
// @desc: 

namespace IGK\Database\SchemaBuilder;
  
use IGK\Helper\Activator;

/**
 * base diagrame property host
 * @package igk\db\schemaBuilder
 */
abstract class DiagramPropertiesHost{
    protected $m_properties;
    protected $prefix;
     /**
     * get last property description 
     * @var mixed
     */
    protected $m_last;
    
    public function getLastProperty(){
        return $this->m_last;
    }
    public function addProperties(?array $DiagramProperties ){
        $_key = IGK_FD_NAME;
        foreach(array_values($DiagramProperties) as $k){
            if ($this->prefix){
                $p = igk_getv($k, $_key);
                if (is_object($k)){
                    $k->clName = $this->prefix.$p;
                }
                else{
                    $k[$_key] = $this->prefix.$p;
                }
            }
            if (!($k instanceof DiagramEntityColumnInfo)){ // convert to diagram entity info
                $k = Activator::CreateNewInstance(DiagramEntityColumnInfo::class, $k);
            }
            $this->m_properties[$k->clName] = $k;
            $this->m_last = $k;         
        } 
        return $this;
    }
    /**
     * return a copy of properties
     * @return mixed 
     */
    public function getProperties(){
        return $this->m_properties;
    }
}
