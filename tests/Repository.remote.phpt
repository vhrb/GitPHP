<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Mockery\Mock;
use Nette\Utils\ArrayHash;
use Tester\Assert;
use Vhrb\Git\Command\Request;
use Vhrb\Git\Command\Response;
use Vhrb\Git\Command\Executor;
use Vhrb\Git\Remote;
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

// ###### REMOTES #######
Assert::equal(new ArrayHash(), $repository->remoteList());


/** @var Mock $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->twice()
	->andReturn(new Response([
		'valid' => TRUE,
		'out' => "test\tgit@github.com:vhrb/git.git\t(fetch)",
	]));

/** @var Executor $executor */
$repository->setExecutor($executor);
Assert::equal(ArrayHash::from([
	'test' => new Remote($repository, 'test', 'git@github.com:vhrb/git.git')
]), $repository->remoteList());


/** @var Mock $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->once();

/** @var Executor $executor */
$repository->setExecutor($executor);
$repository->addRemote('test', 'git@github.com:vhrb/git.git');
Assert::equal(
	new Request([
		'command' => 'git remote add test git@github.com:vhrb/git.git',
	]), $repository->getLastRequest());

Mockery::close();