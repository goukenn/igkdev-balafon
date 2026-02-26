<?php
// @author: C.A.D. BONDJE DOUE
// @file: OpenGraphMetadata.php
// @date: 20231127 21:26:15
namespace IGK\System\Html\Metadatas;
use Base;
use IGK\System\IO\StringBuilder;
/**
* 
* @package IGK\System\Html\Metadatas\Traits
*/
class OpenGraphMetadata extends MetadataGroupEntryBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ogTitle;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ogDescription;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ogImage;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ogSiteName;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $ogUrl;
    /**
     * 
     * @var ?string website
     */
    var $ogType;
    /**
     * mapping properties
     * @return array 
     */

    public function map():array{
        return [
            'ogTitle' => 'og:title',
            'ogDescription'=>'og:description',
            'ogImage'=>'og:image',
            'ogSiteName'=>'og:site_name',
            'ogType'=>'og:type',
            'ogUrl'=>'og:url'
        ];
    }
}