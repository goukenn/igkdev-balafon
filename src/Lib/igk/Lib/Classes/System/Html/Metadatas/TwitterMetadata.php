<?php
namespace IGK\System\Html\Metadatas;
/*
* <meta name="twitter:card" content="summary_large_image" />
* <meta name="twitter:site" content="@site" />
* <meta name="twitter:creator" content="@creator" />
* <meta name="twitter:title" content="My Website" />
* <meta name="twitter:description" content="My Website Description" />
* <meta name="twitter:image" content="https://example.com/og.png" />
* ```
*/

/**
* auto generate doc.
* @package IGK\System\Html\Metadatas
*/
class TwitterMetadata extends MetadataGroupEntryBase{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitterCard;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitterSite;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitterCreator;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitterTitle;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitterDescription;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $twitterImage;
    /**
     * Return the mapping of property names to Twitter meta tag names.
     * @return array
     */

    public function map():array{
        return [
            'twitterSite' => 'twitter:site',
            'twitterCard' => 'twitter:card',
            'twitterDescription'=>'twitter:description',
            'twitterImage'=>'twitter:image',
            'twitterType'=>'twitter:type',
            'twitterUrl'=>'twitter:url',
            'twitterCreator'=>'twitter:creator'
        ];
    }
}
