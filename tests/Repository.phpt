<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Mockery\Mock;
use Nette\Utils\ArrayHash;
use Tester\Assert;
use Vhrb\Git\Command\Response;
use Vhrb\Git\Command\Executor;
use Vhrb\Git\Repository;

$path = __DIR__ . '/../temp/test';

$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')->andReturn(new Response([
	'valid' => TRUE,
	'out' => '',
]));

/** @var Mock $repository */
$repository = Mockery::mock(Repository::class);
$repository->makePartial()
	->shouldAllowMockingProtectedMethods();

$repository->shouldReceive('validate')
	->twice();

$repository->shouldReceive('getCommandExecutor')
	->twice()
	->andReturn($executor);

/** @var Repository $repository */
$repository->setPath($path, TRUE);
$repository->setPath($path, FALSE);

Assert::equal(new ArrayHash(), $repository->remoteList());
Mockery::close();
