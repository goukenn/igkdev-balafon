<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppleWebAppMetadata.php
// @date: 20231221 22:10:22
namespace IGK\System\Html\Metadatas;

use IGK\Helper\Activator;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Metadatas
*/
class AppleWebAppMetadata extends MetadataGroupEntryBase{

    /**
     * 
     * @var 'yes' | 'no'
     */
    var $appleWebAppCapable;
    /**
     * web app title 
     * @var mixed
     */
    var $appleWebAppTitle; // -mobile-web-app-title

    /**
     * bar style 
     * @var null|'black-translucent'|'black'|'default'
     */
    var $appleWebAppStatusBarStyle;

    /**
     * 
     * @var null|object|array|AppleTouchIconMetadataDefinition|AppleTouchIconMetadataDefinition[]
     */
    var $appleWebAppStartupImage;

    public function map(): array { 
        return [
            'appleWebAppCapable'=>'apple-mobile-web-app-capable',
            'appleWebAppTitle'=>'apple-mobile-web-app-title',
            'appleWebAppStatusBarStyle'=>'apple-mobile-web-app-style',
            'appleWebAppStartupImage'=>'apple-mobile-web-app-startup-image',
        ];
    }
    public function setProperty(string $n, $v)
    {
        if ($n == 'appleWebAppStartupImage'){
            $v_kill = false;
            if (is_array($v) || is_object($v)){
                if (is_object($v)){
                    $v = [$v];
                }
                $notag = null;
                foreach($v as $m){
                    $t = Activator::CreateNewInstance(AppleTouchIconMetadataDefinition::class, $m);
                    if ($t instanceof AppleTouchIconMetadataDefinition){
                            if (is_null($notag)){
                                $notag = igk_create_notagnode();
                            }
                            $link = $notag->link();
                            $link['rel'] = 'apple-touch-startup-image';
                            $link->setAttributes((array)$t);
                    }
                } 
                if (is_null($notag)){
                    $v_kill = true;
                } else
                     $this->m_def[$n]= $notag;
            }  else {
                $v_kill = true;
            }
                if ($v_kill)
                unset($this->m_def[$n]);
        
            return;
        }

        return parent::setProperty($n, $v);
    
    }

}