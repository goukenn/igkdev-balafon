<?php

// @author: C.A.D. BONDJE DOUE
// @filename: HtmlBingingContextOptions.php
// @date: 20221018 12:24:32
// @desc: 

namespace IGK\System\Html;

/**
 * 
 * @package IGK\System\Html
 */
class HtmlBindingContextOptions extends HtmlLoadingContextOptions{
    /**
     * data type
     * @var mixed
     */
    var $_data_type;
    /**
     * binding type
     * @var ?string
     */
    var $type;

    var $key;

    var $value;
}
