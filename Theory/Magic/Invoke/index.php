<?php

/**
Метод __invoke() викликається, коли скрипт намагається виконати обєкт як функцію.

__invoke( ...$values): mixed
*/

class CallableClass
{
	public function __invoke(string $somethingString)
	{
		print($somethingString) . PHP_EOL;
	}
}

$object = new CallableClass();
$object('This is a string');
// This is a string

/**
 * https://yiiframework.ru/forum/viewtopic.php?t=62257
 */

class Job 
{
	
	public function __construct(
		public int $count
	) {}
}

class JobHandler
{
	public function __invoke(Job $job)
	{
		echo $job->count . PHP_EOL;
	}
}

class ExtraJob
{
	public function __construct(
		public int $count
	) {}
}

class ExtraJobHandler
{
	public function __invoke(ExtraJob $job)
	{
		echo $job->count . PHP_EOL;
	}
}

/**
 * 
 */
class Consumer
{
	
	public function consume(object $job): void 
	{
		$handler = $this->resolveHandler($job);
		$handler($job);
	}

	private function resolveHandler(object $job): callable
	{
		$name = get_class($job). 'Handler';
		$handler = new $name;

		$handler = is_callable($handler) ? $handler : throw new \Exception('oops');

		return $handler;
	}
}

(new Consumer)->consume(new Job(25));
(new Consumer)->consume(new ExtraJob(50));