<?php
// @author: C.A.D. BONDJE DOUE
// @file: OpenGraphMetadata.php
// @date: 20231127 21:26:15
namespace IGK\System\Html\Metadatas;

use Base;
use IGK\System\IO\StringBuilder;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Metadatas\Traits
*/
class OpenGraphMetadata extends MetadataGroupEntryBase{
    var $ogTitle;
    var $ogDescription;
    var $ogImage; 
    var $ogSiteName;
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