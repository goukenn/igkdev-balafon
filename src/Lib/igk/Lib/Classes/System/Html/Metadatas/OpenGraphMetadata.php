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
/**
* auto generate doc.
* @package IGK\System\Html\Metadatas
*/
class OpenGraphMetadata extends MetadataGroupEntryBase{
    /**
    * Property: og title.
    * @var mixed
    */
    var $ogTitle;
    /**
    * Property: og description.
    * @var mixed
    */
    var $ogDescription;
    /**
    * Property: og image.
    * @var mixed
    */
    var $ogImage;
    /**
    * Name of og site name.
    * @var mixed
    */
    var $ogSiteName;
    /**
    * Property: og url.
    * @var mixed
    */
    var $ogUrl;
    /**
    * auto generate doc.
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