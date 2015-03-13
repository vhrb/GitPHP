<?php

include_once __DIR__ . '/bootstrap.php';

use Mockery\Mock;
use Nette\Utils\ArrayHash;
use Tester\Assert;
use Vhrb\Executor\Request;
use Vhrb\Executor\Response;
use Vhrb\Executor\Executor;
use Vhrb\Git\Remote;
use Vhrb\Git\Repository;

$repository = new Repository();

test(function () use ($repository) {
	/** @var Mock $executor */
	$executor = Mockery::mock(Executor::class);
	$executor->shouldReceive('run')
		->once()
		->andReturn(new Response([
			'valid' => TRUE,
			'out' => '',
		]));

	/** @var Executor $executor */
	$repository->setExecutor($executor);

	Assert::equal(new ArrayHash(), $repository->remoteList());
});

test(function () use ($repository) {
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
});

test(function () use ($repository) {
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
});

Mockery::close();