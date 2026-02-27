<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppleTouchIconMetadataDefinition.php
// @date: 20231221 22:42:16
namespace IGK\System\Html\Metadatas;

/**
* auto generate doc.
* @package IGK\System\Html\Metadatas
*/
class AppleTouchIconMetadataDefinition{

    /**
    * Property: media.
    * @var mixed
    */
    var $media;

    /**
    * Property: href.
    * @var mixed
    */
    var $href;

    /**
    * Property: sizes.
    * @var mixed
    */
    var $sizes;

    /**
    * Constant: media ipad portrait.
    * @var mixed
    */
    const MEDIA_IPAD_PORTRAIT = '(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 1)';

    /**
    * Constant: media ipad lanscape.
    * @var mixed
    */
    const MEDIA_IPAD_LANSCAPE = '(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 1)';

    /**
    * Constant: media ipad retina portrait.
    * @var mixed
    */
    const MEDIA_IPAD_RETINA_PORTRAIT = '(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)';

    /**
    * Constant: media ipad retina landscape.
    * @var mixed
    */
    const MEDIA_IPAD_RETINA_LANDSCAPE= '(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)';
}