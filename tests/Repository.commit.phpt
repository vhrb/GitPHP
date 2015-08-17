<?php

include_once __DIR__ . '/bootstrap.php';

use Mockery\Mock;
use Tester\Assert;
use Vhrb\Executor\Request;
use Vhrb\Executor\Executor;
use Vhrb\Git\Repository;

$path = __DIR__ . '/../tmp/test';

/** @var Mock $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->once();

$repository = new Repository();
/** @var Executor $executor */
$repository->setExecutor($executor);

test(function () use ($repository) {
	// ##### COMMIT ######
	$repository->commit('"message');
	Assert::equal(
		new Request([
			'command' => 'git commit -m "\"message"',
		]), $repository->getLastRequest());
});

Mockery::close();
