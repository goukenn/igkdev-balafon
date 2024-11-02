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
    protected $p_prefix;
    protected $m_properties;
    protected $m_resolveLinkColumn;
     /**
     * get last property description 
     * @var mixed
     */
    protected $m_last;
    
    public function getLastProperty(){
        return $this->m_last;
    }
    /**
     *  */
    public function addProperties(?array $DiagramProperties ){
        $_key = IGK_FD_NAME;
        foreach(array_values($DiagramProperties) as $k){
            if ($this->p_prefix){
                $p = igk_getv($k, $_key);
                if (is_object($k)){
                    $k->clName = $this->p_prefix.$p;
                }
                else{
                    $k[$_key] = $this->p_prefix.$p;
                }
            }
            if (!($k instanceof DiagramEntityColumnInfo)){ // convert to diagram entity info
                $k = Activator::CreateNewInstance(DiagramEntityColumnInfo::class, $k);
            }
            $this->m_properties[$k->clName] = $k;
            $this->m_last = $k;   
            
            // if k request resolution 
            if (DiagramHelper::IsRequestLinkResolution($k)){
                if (is_null($this->m_resolveLinkColumn)){
                    $this->m_resolveLinkColumn = [];
                }
                $this->m_resolveLinkColumn[] = $k;
            }

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
    /**
     * 
     * @return ?array 
     */
    public function getResolveLinks(){
        return $this->m_resolveLinkColumn;
    }
}
