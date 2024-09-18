<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssItemInfo.php
// @date: 20240913 09:09:00
namespace IGK\System\Html\Css;
 
use JsonSerializable;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssItemInfo implements JsonSerializable, ICssClassList
{
    var $count;
    var $source;
    var $references;
    var $mediaReferences;
    var $id;
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @return bool 
     * @throws Exception 
     */
    function isPrimary()
    {
        return $this->source && (count($this->source) == 1) && ($this->id == igk_getv($this->source, 0));
    }
    public function isReferenceMedia(int $index)
    {
        return $this->mediaReferences && key_exists($index, $this->mediaReferences);
    }
    public function jsonSerialize(): mixed
    {
        $o = igk_get_object_public_vars($this);
        $o['__isPrimary'] = $this->isPrimary();
        return $o;
    }
}