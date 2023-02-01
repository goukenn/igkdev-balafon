<?php
// @author: C.A.D. BONDJE DOUE
// @file: ContentCallback.php
// @date: 20230104 11:05:55
namespace IGK\System\Html;


///<summary></summary>
/**
* use to set callback expression value
* @package IGK\System\Html
* @exemple $div()->Content = ContentCallback::Create($callback)
*/
class ContentCallback implements IHtmlGetValue{
    private $m_callable;

    public function __construct(callable $callback){
        $this->m_callable = $callback;
    }
    public function getValue($options = null) {
        $fc = $this->m_callable;
        return $fc($options);
    }
    /**
     * create an instance
     * @param callable $callback 
     * @return ContentCallback 
     */
    public static function Create(callable $callback){
        return new self($callback);
    }
}