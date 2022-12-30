<?php
// @author: C.A.D. BONDJE DOUE
// @file: DocumentParserResolvedUriAttribute.php
// @date: 20221217 13:17:54
namespace IGK\DocumentParser;

use IGK\System\Html\IHtmlGetValue;
use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\DocumentParser
*/
class DocumentParserResolvedUriAttribute implements IHtmlGetValue{
    var $uri;

    public function __construct(string $m)
    {
        $this->uri = $m;
    }
    public function toString(){
        return $this->uri;
    }
    public function getValue($options = null){
        if ($options){
            if ( igk_getv($options, 'engine') == DocumentParser::ENGINE_NAME){
                $uri = igk_str_rm_start($this->uri, $options->asset_dir);
                return sprintf('<?= $ctrl::asset("%s") ?>', $uri); 
            }
        }
        return $this->uri;
    }

}