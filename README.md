# axync
a smart cooperative multitasking kernel for php7 (only) .

# show me the code !

### Example 1 (simple)
```php
  require "axync.php";

  // our workers builder
	$build = function($seq){
	  // this will be the generated coroutine
	  // yes, it MUST yield !
		return (function() use($seq) {
			for ( $i = 0; $i < 10; $i ++ ) {
				printf("Worker (%d): i'm in the step no. %d \n", $seq, $i);
				yield;
			}
		});
	};  
  
  // create new manager
  (new Axync(
    $build(1),
    $build(2),
    $build(3)
  ))->exec();

  // save your file as ~/tst1.php
  // open your terminal and `php ~/tst1.php` and see the result .
```

### Example 2 (advanced)
```php
  require "axync.php";

  // our workers builder
	$build = function($seq){
	  // this will be the generated coroutine
	  // yes, it MUST yield !
		return (function() use($seq) {
			for ( $i = 0; $i < 10; $i ++ ) {
				printf("Worker (%d): i'm in the step no. %d \n", $seq, $i);
				yield;
			}
		});
	};  
  
  // create new manager
  // and convert to to a new Generator !!
 $manager1 = (new Axync(
    $build(1),
    $build(2),
    $build(3)
  ))->toGenerator();

  // create a new manager that handles new generators
  (new Axync(
    $build(10),
    $build(20),
    $build(30),
    $manager1 // yes, axync support nested axync of axync too ;)
  ))->exec();

  // save your file as ~/tst2.php
  // open your terminal and `php ~/tst2.php` and see the result .
```
