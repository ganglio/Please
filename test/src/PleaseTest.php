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
            2,
            $res->onSuccess(function ($v) {
                return $v*4;
            })->get()
        );
    }

    public function testIsFailure()
    {
        $res = new Please($this->inverse, 0);
        $this->assertTrue($res->isFailure);
    }

    public function testFailureGet()
    {
        $res = new Please($this->inverse, 0);
        $this->assertTrue($res->get() instanceof \Exception);
    }

    public function testOnFailure()
    {
        $res = new Please($this->inverse, 0);
        $this->assertEquals(
            "Division by zero",
            $res->onFailure(function (\Exception $e) {
                return $e->getMessage();
            })->get()
        );
    }

    public function testOn()
    {
        $res = new Please($this->inverse, 0);
        $this->assertEquals(
            "Error: Division by zero",
            $res->on(
                function ($v) {
                    return "Result:" . $v;
                },
                function ($e) {
                    return "Error: " .  $e->getMessage();
                }
            )->get()
        );
    }

    public function testABitMore()
    {
        $strings = [
            'not json',
            '{"a":3,"b":4}',
            'still not json',
            '{"c":5}',
        ];

        $json_decoder_exception = function ($string) {
            $out = json_decode($string);
            if (json_last_error() != JSON_ERROR_NONE) {
                throw new \Exception("Invalid JSON");
            }
            return $out;
        };

        $results = array_filter(
            array_map(function ($s) use ($json_decoder_exception) {
                return new Please($json_decoder_exception, $s);
            }, $strings),
            function ($e) {
                return $e->isSuccess;
            }
        );

        $this->assertEquals(
            2,
            count($results)
        );
    }
}
