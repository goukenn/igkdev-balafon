<?php
// @author: C.A.D. BONDJE DOUE
// @file: CoreDocMetadata.php
// @date: 20231221 15:20:59
namespace IGK\System\Html\Metadatas;

use IGK\Helper\Activator;
use IGK\Helper\StringUtility;
use ReflectionProperty;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Metadatas
*/
class CoreDocMetadata extends MetadataGroupEntryBase{

    var $applicationName;
    var $generator;
    var $themeColor;
    var $colorScheme;
    var $creator;
    var $publisher;
    var $robots;
    var $alternates;
    var $icons;
    var $manifest;
    var $classification;

    var $category;
    
    var $other;
    var $archives;
    var $abstract;
    var $itunes;
    var $assets;
    var $bookmarks;
    var $appleItunesApp;
    /**
     * 
     * @var null|array|IGK\System\Html\Metadatas\formatDetection
     */
    var $formatDetection;

    public function map(): array {
        static $mapped = null;
        if (is_null($mapped)){

            $mem =  igk_reflection_get_private_member(static::class, ReflectionProperty::IS_PUBLIC);
            
            $c = array_fill_keys($mem, $mem);
            
            array_map(function($i, $v)use(& $c){
                $c[$v] = strtolower(StringUtility::GetSnakeKebab($v, true));
            }, $c, $mem);
            $mapped =  $c;
        }
        return $mapped; 
    }
    public function setProperty(string $n, $v)
    {
        switch($n){
            case 'manifest':
                $notag = igk_create_notagnode();
               
                $link = $notag->link();  
                $link['rel'] = 'manifest';
                $link['href'] = $v;
                $this->m_def[$n]= $notag;
                $this->icons = $v;
                return;
            case 'icons':
                $notag = igk_create_notagnode();
                if (is_array($v)){ 
                    foreach($v as $t){
                        $m = Activator::CreateNewInstance(CoreIconDescriptorMetaData::class, $t);
                       if ($m instanceof CoreIconDescriptorMetaData){
                            $link = $notag->link();
                            $link['rel'] = $m->rel; 
                            $link['href'] = $m->href;; 
                        } 
                    }
                } else if ( is_object($v) &&  ( $v = (Activator::CreateNewInstance(CoreIconMetaData::class, $v)))){
                    if ($v->icon){
                        $link = $notag->link();
                        $link['rel'] ="icon"; 
                        $link['href'] = $v->icon;
                    }
                    if ($v->apple){
                        $link = $notag->link();
                        $link['rel'] ="apple-touch-icon"; 
                        $link['href'] = $v->apple;
                    }  
                }
                else {
                    $link = $notag->link();  
                    $link['rel'] = 'icon';
                    $link['href'] = $v; 
                }  
                $this->m_def[$n]= $notag;
                $this->icons = $v;
                return;
            default:
            if (method_exists($this, $fc = 'handle_'.$n)){

                $this->$n = $this->$fc($v, $n);
                return;
            }
            break;
        }
        parent::setProperty($n, $v);
    }
    public function handle_archives($v, $n){
        $notag = igk_create_notagnode();
        $link = $notag->link();
        $this->m_def[$n] = $notag;
        $link['rel'] = $n; 
        $link['href'] = $v;
        return $v;
    }

    public function handle_formatDetection($v, $n){
        $s = '';
        if(is_array($v)){
            $c = '';
            foreach($v as $k=>$v){
                $s.= $c.$k.'='.($v? 'yes':'no');
                $c = ',';
            } 
            parent::setProperty($n, $s);
            return $s;
        }

    }
    public function bindMetaDef($name, $content){

    }
}