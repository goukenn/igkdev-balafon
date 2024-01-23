<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppleTouchIconMetadataDefinition.php
// @date: 20231221 22:42:16
namespace IGK\System\Html\Metadatas;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Metadatas
*/
class AppleTouchIconMetadataDefinition{
    var $media;
    var $href;
    var $sizes;

    const MEDIA_IPAD_PORTRAIT = '(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 1)';
    const MEDIA_IPAD_LANSCAPE = '(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 1)';
    const MEDIA_IPAD_RETINA_PORTRAIT = '(device-width: 768px) and (device-height: 1024px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)';
    const MEDIA_IPAD_RETINA_LANDSCAPE= '(device-width: 768px) and (device-height: 1024px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)';

}