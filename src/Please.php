<?php

namespace ganglio;

class Please
{
    private $result;

    public $isSuccess;
    public $isFailure;

    public function __construct($callable, $args)
    {
        if (!is_array($args)) {
            $args = [$args];
        }
        try {
            $this->result = call_user_func_array($callable, $args);
            $this->isSuccess = true;
            $this->isFailure = false;
        } catch (\Exception $e) {
            $this->result = $e;
            $this->isSuccess = false;
            $this->isFailure = true;
        }
    }

    public function get()
    {
        return $this->result;
    }

    public function onSuccess($callable)
    {
        if ($this->isSuccess) {
            return new Please($callable, $this->result);
        }
    }

    public function onFailure($callable)
    {
        if ($this->isFailure) {
            return new Please($callable, $this->result);
        }
    }

    public function on($successCallable, $failureCallable)
    {
        if ($this->isSuccess) {
            return new Please($successCallable, $this->result);
        }

        if ($this->isFailure) {
            return new Please($failureCallable, $this->result);
        }
    }
}
