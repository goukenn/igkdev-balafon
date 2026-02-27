<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssItemInfo.php
// @date: 20240913 09:09:00
namespace IGK\System\Html\Css;
use JsonSerializable;
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/

/**
* auto generate doc.
* @package IGK\System\Html\Css
*/
class CssItemInfo implements JsonSerializable, ICssClassList
{

    /**
    * Count: count.
    * @var mixed
    */
    var $count;

    /**
    * Property: source.
    * @var mixed
    */
    var $source;

    /**
    * Property: references.
    * @var mixed
    */
    var $references;

    /**
    * Property: media references.
    * @var mixed
    */
    var $mediaReferences;

    /**
    * Identifier: id.
    * @var mixed
    */
    var $id;

    /**
    * .ctr
    * @param string $id
    */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
    * auto generate doc.
    * @return bool
    */

    function isPrimary()
    {
        return $this->source && (count($this->source) == 1) && ($this->id == igk_getv($this->source, 0));
    }

    /**
    * Returns true if Reference Media.
    * @param int $index
    */
    public function isReferenceMedia(int $index)
    {
        return $this->mediaReferences && key_exists($index, $this->mediaReferences);
    }

    /**
    * Json serialize.
    * @return mixed
    */
    public function jsonSerialize(): mixed
    {
        $o = igk_get_object_public_vars($this);
        $o['__isPrimary'] = $this->isPrimary();
        return $o;
    }
}