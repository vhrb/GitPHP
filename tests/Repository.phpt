<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Nette\Utils\ArrayHash;
use Tester\Assert;
use Vhrb\Git\Command\Response;
use Vhrb\Git\Command\Executor;
use Vhrb\Git\InvalidArgumentException;
use Vhrb\Git\Remote;
use Vhrb\Git\Repository;

$path = __DIR__ . '/../tmp/test';

/** @var Executor $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->andReturn(new Response([
		'valid' => TRUE,
		'out' => '',
	]));

$repository = new Repository();
$repository->setExecutor($executor);

/** @var Repository $repository */
$repository->setPath($path, TRUE);
$repository->setPath($path, FALSE);

Assert::throws(function () use ($repository) {
	$repository->setPath('', TRUE);
}, InvalidArgumentException::class); //, 'Invalid repository path: ');

Mockery::close();


Assert::equal(new ArrayHash(), $repository->remoteList());

/** @var Executor $executor */
$executor = Mockery::mock(Executor::class);
$executor->shouldReceive('run')
	->andReturn(new Response([
		'valid' => TRUE,
		'out' => "test\tgit@github.com:vhrb/git.git\t(fetch)",
	]));
$repository->setExecutor($executor);
Assert::equal(ArrayHash::from([
	'test' => new Remote($repository, 'test', 'git@github.com:vhrb/git.git')
]), $repository->remoteList());