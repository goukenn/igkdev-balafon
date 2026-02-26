<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ConfigurationObject.php
// @date: 20220830 09:47:38
// @desc: object config
namespace IGK\System\IO\Configuration;
use JsonSerializable;
/**
 * configuration object
 * @package IGK\System\IO\Configuration
 */
class ConfigurationObject implements JsonSerializable{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $key;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $value;

    /**
    * .ctr
    */
    public function __construct(){        
    }

    /**
    * auto generate doc.
    * @return mixed
    */
    public function jsonSerialize(): mixed { 
        return json_encode((array)$this);
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return sprintf("%s=%s", $this->key, $this->value);
    }
}