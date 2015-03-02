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
$repository = new Repository();

// ###### FETCH #######
/** @var Mock $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->twice();

/** @var Executor $executor */
$repository->setExecutor($executor);

test(function () use ($repository) {
	$repository->fetch();
	Assert::equal(
		new Request([
			'command' => 'git fetch origin',
		]), $repository->getLastRequest());

	$repository->fetchAll();
	Assert::equal(
		new Request([
			'command' => 'git fetch ',
		]), $repository->getLastRequest());

	Mockery::close();
});