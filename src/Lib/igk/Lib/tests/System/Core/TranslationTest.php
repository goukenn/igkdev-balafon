<?php
// @author: C.A.D. BONDJE DOUE
// @file: TranslationTest.php
// @date: 20260207 10:10:34
namespace IGK\Tests\System\Core;
use IGK\Resources\R;
use IGK\Tests\BaseTestCase;
use function igk_resources_gets as __;

/**
* 
* @package IGK\Tests\System\Core
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\Tests\System\Core
*/
class TranslationTest extends BaseTestCase{
    /**
    * auto generate doc.
    * @return void
    */
    public function test_translation_enum_litteral(){
        $ts = [
            'info'=>'info',
            'enum.info'=>''
        ];
        R::ClearLang(false);
        $this->assertEquals(
            'info',
            __('enum.info')
        );
        R::AddLang('enum.info', 'information');
        $this->assertEquals(
            'information',
            __('enum.info')
        );
        R::Reload();
    }
}