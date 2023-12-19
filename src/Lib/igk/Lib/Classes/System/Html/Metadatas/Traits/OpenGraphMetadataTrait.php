<?php
// @author: C.A.D. BONDJE DOUE
// @file: OpenGraphMetatadataTrait.php
// @date: 20231127 21:26:15
namespace IGK\System\Html\Metadatas\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Metadatas\Traits
*/
trait OpenGraphMetatadataTrait{
    var $ogTitle;
    var $ogDescription;
    var $ogImage;

    public function render(){
        $data = [
            'ogTitle' => 'og:title',
            'ogDescription'=>'og:description',
            'ogImage'=>'og:image'
        ];
        

        foreach($data as $k=>$v){
            $m = igk_create_node('meta');
            $m['property']= $v;
            $m['content'] = $this->{$k};
        }

    }
}