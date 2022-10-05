<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ContentDependOn.php
// @date: 20220828 23:38:29
// @desc: dependency for compiler

namespace IGK\System\Html\Dom\Dependency;

/**
 * content depency
 * @package IGK\System\Html\Dom
 */
class ContentDependOn{
    var $argName;

    public function render($options = null ){
        return sprintf("<?= %s ?>", $this->argName); 
    }
}
