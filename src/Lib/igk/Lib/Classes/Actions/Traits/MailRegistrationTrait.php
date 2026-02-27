<?php
// @author: C.A.D. BONDJE DOUE
// @file: MailRegistrationTrait.php
// @date: 20221115 21:52:14
namespace IGK\Actions\Traits;

/**
* auto generate doc.
* @package IGK\Actions\Traits
*/
trait MailRegistrationTrait{

    /**
    * Returns Mail Registration Message.
    * @param mixed $user
    */
    public function getMailRegistrationMessage($user){
        $ctrl = $this->getController();
        $t = igk_create_node('div');
        if (igk_io_file_exists($file = $ctrl->getArticle('mail_registration'))){
            $t->article($ctrl, $file, [$user]);
        }
        else{
            $str = __('mail_registration.message');
            $t->load($str);
        }
        return $t->render();
    }
}