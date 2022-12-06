<?php

// @author: C.A.D. BONDJE DOUE
// @filename: PHPDoc.php
// @date: 20221206 09:46:30
// @desc: 


namespace IGK\System\IO\File\Php;
/**
 * the php doc commend helper
 * @package IGK\System\IO\File\Php
 * @method self comment(string $comment) define the top comment
 * @method self var(string $name, ?string $type=null, ?string $comment=null) define the top comment
 */
class PHPDoc{
    public function __toString()
    {
        return $this->getValue();
    }
    public function __get($name)
    {
        return null;
    }
    public function __call($name, $arguments)
    {
        if (isset($arguments[0]))
            $this->$name = $arguments[0];
        return $this;
    }
    /**
     * get value
     * @return string 
     */
    public function getValue():string{
        
        $g = [];
        if ($c = $this->comment){
            $g[] = $c;
        }
        if ($c = $this->var){
            usort($c, function($a, $b){
                return strcmp($a->name, $b->name);
            });
            while(count($c)>0){
                $q = array_shift($c);
                $g[] = sprintf("@var %s %s %s", $q->type, $q->name, $q->comment);
            }
        }
        $sb = "/**";
        $sb .= "\n* ".implode("\n* ", $g)."\n";
        $sb .= "*/";
        return $sb;
    }
    /**
     * define vars
     * @param string $name 
     * @param null|string $type 
     * @param null|string $comment 
     * @return $this 
     */
    public function var(string $name, ?string $type=null, ?string $comment=null){
      $g = null;
      if(property_exists($this, "var")) 
        $g = & $this->var;
    
      if (is_null($g)){
          $this->var = [];
          $g = & $this->var;
      }
      $g[] = (object)compact('name', 'type', 'comment');
      return $this;  
    }
}