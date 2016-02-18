<?php
	/**
	 * Axync - a cooperative multitasking kernel for PHP7, lets take PHP out of the box, just for the fun .
	 *
	 * This library uses PHP 7 features to create a simple and smart co-operative multitasking,
	 * you can easily use it as a main loop but also you can use it as a child co-operative multitasking processor,
	 * i created this library for the following reasons:
	 * - just for fun .
	 * - to prove that you can do what you want only if you want .
	 * - the issue isn't with the tool you are using, but <IT IS IN YOU> .
	 * - again just for fun .
	 *
	 * @version 	1.0
	 * @license 	MIT License
	 * @author 		Mohammed Al Ashaal <alash3al.xyz>
	 */
	Class Axync {
		/**
		 * array of registered coroutines
		 * @var 	array 	$coroutines
		 */
		protected $coroutines = [];

		/**
		 * Constructor
		 *
		 * @param 	Callable 	... $params
		 * @return 	$this
		 */
		public function __construct(Callable ... $params) {
			$this->register(...$params);
		}

		/**
		 * register Generatable callbacks (callbacks that return generators)
		 *
		 * @param 	Callable 	... $params
		 * @return 	$this
		 */
		public function register(Callable ... $params) {
			foreach ( $params as $i => $obj ) {
				$this->coroutines[] = $obj;
			}
			return $this;
		}

		/**
		 * execute each registered coroutine once .
		 *
		 * @return 	$this
		 */
		protected function tick() {
			foreach ( $this->coroutines as $i => $co ) {
				if ( is_callable($co) ) {
					$co = $this->coroutines[$i] = $co();
					if ( ! $co instanceof Generator ) {
						throw new InvalidArgumentException(sprintf("The axync worker(%s) not valid", $i));
					}
					$co->rewind();
				} else if ( ! $co->valid() ) {
					unset($this->coroutines[$i]);
				} else {
					$co->next();
				}
			}
		}

		/**
		 * create a generator based tick.
		 *
		 * @return 	Generator
		 */
		protected function tickYield() {
			foreach ( $this->coroutines as $i => $co ) {
				if ( is_callable($co) ) {
					$co = $this->coroutines[$i] = $co();
					if ( ! $co instanceof Generator ) {
						throw new InvalidArgumentException(sprintf("The axync worker(%s) not valid", $i));
					}
					$co->rewind();
				} else if ( ! $co->valid() ) {
					unset($this->coroutines[$i]);
				} else {
					$co->next();
				}
				yield;
			}
		}

		/**
		 * execute each registered coroutine till the end (blocking operation) .
		 *
		 * @return 	$this
		 */
		public function exec() {
			while ( sizeof($this->coroutines) > 0 ) {
				$this->tick();
			}
			return $this;
		}

		/**
		 * create a generator that will execute each registered coroutine (non-blocking operation) .
		 *
		 * @return 	Generator
		 */
		public function toGenerator() {
			return function(){
				while ( sizeof($this->coroutines) > 0 ) {
					yield from $this->tickYield();
				}
			};
		}
	}
