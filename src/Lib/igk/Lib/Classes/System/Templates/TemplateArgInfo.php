<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TemplateArgInfo.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Templates;
use IGK\Controllers\BaseController;
/**
 * template argument information
 * @package IGK\System\Templates
 */
class TemplateArgInfo
{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $args;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $creationargs;

    /**
    * auto generate doc.
    * @param mixed $args
    */
    public function setCreationArg($args)
    {
        $this->creationargs = $args;
    }

    /**
    * auto generate doc.
    */
    public function getArgs()
    {
        if ($this->creationargs) {
            $l = [];
            foreach ($this->creationargs as $v) {
                if (is_string($v)) {
                    if (strpos($v, ",") !== false)
                        $l[] = '"' . $v . '"';
                    else {
                        $l[] = $v;
                    }
                } else if (is_object($v)) {
                    if ($v instanceof BaseController) {
                        $l[] = "[[:@ctrl]]";
                    }
                }
            }
            return htmlentities(implode(", ", $l));
        }
        return implode(", ", array_keys($this->args));
        //return "@@ctrl, @@args";
    }

    /**
    * auto generate doc.
    * @param mixed $params
    */
    public function push($params)
    {
        $this->args[$params->getName()] = $params;
    }
}