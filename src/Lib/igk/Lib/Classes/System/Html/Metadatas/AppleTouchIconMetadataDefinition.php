<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppleTouchIconMetadataDefinition.php
// @date: 20231221 22:42:16
namespace IGK\System\Html\Metadatas;
/**
* 
* @package IGK\System\Html\Metadatas
*/
class AppleTouchIconMetadataDefinition{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $media;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $href;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $sizes;

    /**
    * auto generate doc.
    * @var mixed
    */
    const MEDIA_IPAD_PORTRAIT = '(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 1)';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MEDIA_IPAD_LANSCAPE = '(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 1)';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MEDIA_IPAD_RETINA_PORTRAIT = '(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)';

    /**
    * auto generate doc.
    * @var mixed
    */
    const MEDIA_IPAD_RETINA_LANDSCAPE= '(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)';
}