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
class TwitterMetadata extends MetadataGroupEntryBase{
    var $twitterCard;
    var $twitterSite;
    var $twitterCreator;
    var $twitterTitle;
    var $twitterDescription;
    var $twitterImage;

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