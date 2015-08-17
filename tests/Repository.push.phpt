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
	->twice();

$repository = new Repository();
/** @var Executor $executor */
$repository->setExecutor($executor);

test(function () use ($repository) {
	// ##### PUSH ######
	$repository->push('origin', 'master');
	Assert::equal(
		new Request([
			'command' => 'git push origin master',
		]), $repository->getLastRequest());

	// ##### PUSH --all ######
	$repository->push('origin');
	Assert::equal(
		new Request([
			'command' => 'git push origin --all',
		]), $repository->getLastRequest());
});

Mockery::close();
