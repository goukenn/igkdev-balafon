<?php

namespace IGK\System\Html\Metadatas;


  /**
     * The Facebook AppLinks metadata for the document.
     * @example
     * ```tsx
     * { ios: { appStoreId: "123456789", url: "https://example.com" }, android: { packageName: "com.example", url: "https://example.com" } }
     *
     * <meta property="al:ios:app_store_id" content="123456789" />
     * <meta property="al:ios:url" content="https://example.com" />
     * <meta property="al:android:package" content="com.example" />
     * <meta property="al:android:url" content="https://example.com" />
     * ```
     *
*/
class AppLinkMetadata extends MetadataGroupEntryBase{
    var $alIOSAppId;  
    var $alIOSUrl;  
    var $alAndroidPackage;  
    var $alAndroidUrl;  

    public function map():array{
        return [
            'alIOSAppId'=>'al:ios:app_store_id',  
            'alIOSUrl'=>'al:ios:url',  
            'alAndroidPackage'=>'al:android:package',  
            'alAndroidUrl'=>'al:android:url',  
        ];
    }

}