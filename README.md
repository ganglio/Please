# Please

Functional and polite implementation of try/catch

[![Latest Stable Version](https://poser.pugx.org/ganglio/Please/v/stable)](https://packagist.org/packages/ganglio/Please)
[![Build Status](https://travis-ci.org/ganglio/Please.svg?branch=master)](https://travis-ci.org/ganglio/Please)
[![codecov.io](http://codecov.io/github/ganglio/Please/coverage.svg?branch=master)](http://codecov.io/github/ganglio/Please?branch=master)
[![Code Climate](https://codeclimate.com/github/ganglio/Please/badges/gpa.svg)](https://codeclimate.com/ganglio/Please)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ganglio/Please/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ganglio/Please/?branch=master)
[![License](https://poser.pugx.org/ganglio/Please/license)](https://packagist.org/packages/ganglio/Please)

## Examples

Let's start with something simple:

```PHP
$inverse = function ($v) {
  if (empty($v)) {
    throw new \Exception("Division by zero");
  }

  return 1/$v;
}
```

Usually what we would do is something like this:

```PHP
try {
  $result = $inverse(0);
} catch (\Exception $e) {
  error_log("Error: " . $e->getMessage());
}
```

Using `Please` we do:

```PHP
$result = new Please($inverse, 0);
```

Now we can check if the callable completed successfully or not.

```PHP
if ($result->isSuccess) {
  echo "The result is: " . $result->get();
}
```

Or we can do it using a callback (much nicer)

```PHP
$result->onSuccess(function ($v) {
  echo "The result is: " . $v;
});
```

Unfortunately we tried to divide by zero so the callable didn't succeeded. We can either get the exception:

```PHP
$exception = $result->get();
```

Or process it with the callback:

```PHP
$result->onFailure(function ($e) {
  error_log("Error: " . $e->getMessage());
});
```

We can even pass two callbacks using `on`:

```PHP
$result->on(
  function ($v) {
    echo "Result: " . $v;
  },
  function ($e) {
    echo "Error: " . $e->getMessage();
  }
);
```

`onSuccess`, `onFailure` and `on` return a new instance of `Please` wrapping the callback. This way, if we want we can do something like this:

```PHP
echo (new Please($inverse, $unknown_divisor))
  ->on(
    function ($v) {
      return "Result: " . $v;
    },
    function ($e) {
      return "Error: " . $e->getMessage;
    }
  )->get();
```