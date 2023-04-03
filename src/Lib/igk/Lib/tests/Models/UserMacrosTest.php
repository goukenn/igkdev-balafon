<?php
// @author: C.A.D. BONDJE DOUE
// @file: UserMacrosTest.php
// @date: 20230223 19:44:25
namespace IGK\Tests\Models;

// phpunit -c phpunit.xml.dist ./src/Lib/igk/Lib/Tests/Models/UserMacrosTest.php
use IGK\Tests\BaseTestCase;
use IGKException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\AssertionFailedError;

///<summary></summary>
/**
* 
* @package IGK\Tests\Models
*/
class UserMacrosTest extends BaseTestCase{

    protected function setUp(): void
    {
        $model = \IGK\Models\Users::model();
        $ad = $model->getDataAdapter();
        if (!$ad->canProcess("test")){
            $this->markTestSkipped("adapter required");
        }
    }
    /**
     * 
     * @return void 
     * @throws IGKException 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     * @throws AssertionFailedError 
     */
    public function test_register_query()
    { 
        $tdata = json_decode(<<<JSON
        {
            "firstName":"DummuFirstName",
            "lastName":"DummyLastName",
            "email":"dummyEmail@gmail.com",
            "username":"goukenn",
            "password":"testdummy@123",
            "confirmPass":"testdummy@123",
            "country":"CMR",
            "phone":"+237673173481"
        }
        JSON);
        $model = \IGK\Models\Users::model();
        $ad = $model->getDataAdapter();
        $table =  $model->table();
        $info = $model->getTableColumnInfo();

        // $query = $ad->getGrammar()->createInsertQuery($table, [
        //     'clLogin'=>$tdata->email,
        //     'clPwd'=>$tdata->password,
        //     'clGuid'=>'',

        // ], $info );
        igk_environment()->querydebug = 1;
        $c = igk_environment()->name();
        $query = null;
        $ts = $model::Register([
                'clLogin'=>$tdata->email,
                'clPwd'=>$tdata->password,
                'clGuid'=>null,
        ]
        );

        if ($u = $model->select_row(["clLogin"=>$tdata->email])){
            $s =  $u->clGuid;
            $this->assertTrue(!empty($s));
        } else {
            $this->fail('register dummy user failed');
        }

        // $this->assertEquals($s, $u->to_array());
    }
}