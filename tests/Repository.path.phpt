<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Mockery\Mock;
use Tester\Assert;
use Vhrb\Git\Command\Response;
use Vhrb\Git\Command\Executor;
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
/** @var Repository $repository */
$repository->setPath($path, TRUE);
$repository->setPath($path, FALSE);

Assert::throws(function () use ($repository) {
	$repository->setPath('', TRUE);
}, InvalidArgumentException::class, 'Invalid repository path: ');

Mockery::close();