<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Mockery\Mock;
use Tester\Assert;
use Vhrb\Git\Command\Request;
use Vhrb\Git\Command\Executor;
use Vhrb\Git\Repository;

$path = __DIR__ . '/../tmp/test';

/** @var Mock $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->twice();

$repository = new Repository();
/** @var Executor $executor */
$repository->setExecutor($executor);

// ##### RESET ######
$repository->reset('0a2b3c');
Assert::equal(
	new Request([
		'command' => 'git reset 0a2b3c',
	]), $repository->getLastRequest());

$repository->reset('0a2b3c', TRUE);
Assert::equal(
	new Request([
		'command' => 'git reset --hard 0a2b3c',
	]), $repository->getLastRequest());

Mockery::close();