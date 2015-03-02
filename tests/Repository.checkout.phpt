<?php

include_once __DIR__ . '/bootstrap.php';

use Mockery\Mock;
use Tester\Assert;
use Vhrb\Git\Command\Request;
use Vhrb\Git\Command\Executor;
use Vhrb\Git\Repository;

$path = __DIR__ . '/../tmp/test';

/** @var Mock $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')->once();

$repository = new Repository();
/** @var Executor $executor */
$repository->setExecutor($executor);

// ##### CHECKOUT ######
test(function () use($repository) {
	$repository->checkout('master');
	Assert::equal(
		new Request([
			'cwd' => NULL,
			'command' => 'git checkout master',
		]), $repository->getLastRequest());

	Mockery::close();
}
);