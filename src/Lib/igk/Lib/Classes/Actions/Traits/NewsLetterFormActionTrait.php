<?php
// @author: C.A.D. BONDJE DOUE
// @file: NewsLetterActionTrait.php
// @date: 20221118 17:05:10
namespace IGK\Actions\Traits;
use function igk_resources_gets as __;

///<summary></summary>
/**
* 
* @package IGK\Actions
*/
trait NewsLetterFormActionTrait{
    protected $formFieldEngine;
    
    protected function form_news_letter($form){
        $form['action'] = $this->getController()::uri('stay-in-touch');        
        $form->h2()->Content = __("Stay in touch");
        $form->cref();
        $form->fields([
            "email"=>['type'=>'email', 'required'=>1, 'engine'=>$this->formFieldEngine]
        ]);
    }
}