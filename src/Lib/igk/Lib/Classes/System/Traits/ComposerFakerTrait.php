<?php
// @author: C.A.D. BONDJE DOUE
// @file: ComposerFakerTrait.php
// @date: 20230202 14:21:53
namespace IGK\System\Traits;
use Faker\Generator;
use InvalidArgumentException;
/**
* 
* @package IGK\System\Traits
*/
/**
* auto generate doc.
* @package IGK\System\Traits
*/
trait ComposerFakerTrait{
    /**
    * Property: faker.
    * @var mixed
    */
    protected $faker;
    /**
    * auto generate doc.
    * @return Generator
    */
    public function getFaker(){
        if (is_null($this->faker)){
            $this->faker = \Faker\Factory::create();
        }
        return $this->faker;
    }
}