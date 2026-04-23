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
* Twitter metadata.
* @package IGK\System\Html\Metadatas
*/
class TwitterMetadata extends MetadataGroupEntryBase{
    /**
    * Property: twitter card.
    * @var mixed
    */
    var $twitterCard;
    /**
    * Property: twitter site.
    * @var mixed
    */
    var $twitterSite;
    /**
    * Property: twitter creator.
    * @var mixed
    */
    var $twitterCreator;
    /**
    * Property: twitter title.
    * @var mixed
    */
    var $twitterTitle;
    /**
    * Property: twitter description.
    * @var mixed
    */
    var $twitterDescription;
    /**
    * Property: twitter image.
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