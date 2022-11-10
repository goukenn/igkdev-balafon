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
    var $key;
    var $value;

    public function __construct(){        
    }

    public function jsonSerialize(): mixed { 
        return json_encode((array)$this);
    } 
    public function __toString()
    {
        return sprintf("%s=%s", $this->key, $this->value);
    }
}