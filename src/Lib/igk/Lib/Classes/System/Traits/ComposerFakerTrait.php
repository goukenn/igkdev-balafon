<?php
// @author: C.A.D. BONDJE DOUE
// @file: ComposerFakerTrait.php
// @date: 20230202 14:21:53
namespace IGK\System\Traits;

use Faker\Generator;
use InvalidArgumentException;

///<summary></summary>
/**
* 
* @package IGK\System\Traits
*/
trait ComposerFakerTrait{
    protected $faker;

    /**
     * 
     * @return Generator 
     * @throws InvalidArgumentException 
     */
    public function getFaker(){
        if (is_null($this->faker)){
            $this->faker = \Faker\Factory::create();
        }
        return $this->faker;
    }
}