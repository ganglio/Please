<?php

namespace ganglio\tests;

use \ganglio\Please;
use \ganglio\Success;
use \ganglio\Failure;

class PleaseTest extends \PHPUnit_Framework_TestCase
{
    protected $inverse;

    protected function setUp()
    {
        $this->inverse = function ($v) {
            if (empty($v)) {
                throw new \Exception("Division by zero");
            }
            return 1/$v;
        };
    }

    protected function tearDown()
    {
        $this->inverse    = null;
    }

    public function testIsSuccess()
    {
        $res = new Please($this->inverse, 2);
        $this->assertTrue($res->isSuccess);
    }

    public function testSuccessGet()
    {
        $res = new Please($this->inverse, 2);
        $this->assertEquals($res->get(), 0.5);
    }

    public function testOnSuccess()
    {
        $res = new Please($this->inverse, 2);
        $this->assertEquals(
            $res->onSuccess(function ($v) {
                return $v*4;
            })->get(),
            2
        );
    }

    public function testIsFailure()
    {
        $res = new Please($this->inverse, 0);
        $this->assertTrue($res->isFailure);
    }

    /**
     * @expectedException Exception
     */
    public function testFailureGet()
    {
        $res = new Please($this->inverse, 0);
        $res->get();
    }

    public function testOnFailure()
    {
        $res = new Please($this->inverse, 0);
        $this->assertEquals(
            $res->onFailure(function (\Exception $e) {
                return $e->getMessage();
            })->get(),
            "Division by zero"
        );
    }
}
