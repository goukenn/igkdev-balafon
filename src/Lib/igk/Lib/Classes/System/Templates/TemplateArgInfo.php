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
    private $args;
    private $creationargs;
    public function setCreationArg($args)
    {
        $this->creationargs = $args;
    }
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
    public function push($params)
    {
        $this->args[$params->getName()] = $params;
    }
}
