<?php

include_once __DIR__ . '/bootstrap.php';

use Mockery\Mock;
use Tester\Assert;
use Vhrb\Executor\Response;
use Vhrb\Executor\Executor;
use Vhrb\Git\InvalidArgumentException;
use Vhrb\Git\Repository;

$path = __DIR__ . '/../tmp/test';

/** @var Mock $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->once()
	->andReturn(new Response([
		'valid' => TRUE,
		'out' => '',
	]));

$repository = new Repository();
/** @var Executor $executor */
$repository->setExecutor($executor);

// ###### PATH #######
test(function () use ($repository, $path) {
	/** @var Repository $repository */
	$repository->setPath($path, TRUE);
	$repository->setPath($path, FALSE);

	Assert::throws(function () use ($repository) {
		$repository->setPath('', TRUE);
	}, InvalidArgumentException::class, 'Invalid repository path: ');

	Mockery::close();
});