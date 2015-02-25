<?php
namespace Vhrb\Git;

class Git
{
	protected static $gitCommand = 'git';

	/** @var  Repository */
	protected $repository;

	/**
	 * @param $args
	 *
	 * @return Command
	 */
	public function runCommand($args)
	{
		$c = new Command();
		$c->setCommand(self::$gitCommand . ' ' . $args);
		$c->run();

		return $c;
	}

	/**
	 * @return Repository
	 */
	public function getRepository()
	{
		if ($this->repository === NULL)
			throw new InvalidStateException('First create repository!');

		return $this->repository;
	}

	/**
	 * @param $path
	 * @param bool $init
	 *
	 * @return Repository
	 */
	public function createRepository($path, $init = FALSE)
	{
		return $this->repository = new Repository($path, $init);
	}

	/**
	 * @param string $commandName
	 */
	public function setGitCommand($commandName)
	{
		self::$gitCommand = $commandName;
	}

	/**
	 * @return string
	 */
	public static function getGitCommand()
	{
		return self::$gitCommand;
	}
}