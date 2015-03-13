<?php
namespace Vhrb\Git;

use Vhrb\Executor\Response;
use Nette\Object;

class Remote extends Object
{
	const COMMAND_NAME = 'remote';

	/** @var Response[] */
	private static $show = [];

	/**
	 * @var
	 */
	protected $name;

	/**
	 * @var
	 */
	protected $url;

	/**
	 * @var
	 */
	protected $repository;

	/**
	 * @var array
	 */
//	protected $type = [];

	/**
	 * @var bool
	 */
	protected $throwExceptions = TRUE;

	/**
	 * @var array
	 */
	public $onError = [];

	/**
	 * @var array
	 */
	public $onSuccess = [];

	public function __construct(Repository $repository, $name, $url)
	{
		$this->repository = $repository;
		$this->name = $name;

		$url = trim($url);
		if ($this->isGitUrl($url)) $this->url = $url;
		else throw new InvalidArgumentException("This is not a valid source path / URL '$url'");
	}

	public function isGitUrl($url)
	{
		$url = trim($url);
		if (isset(self::$show[$url])) return self::$show[$url]->isValid();

		self::$show[$url] = $command = $this->repository->run([
				self::COMMAND_NAME,
				'show',
				$url,
			]
		);

		return $command->isValid();
	}

	public function setUrl($url)
	{
		$command = $this->repository->run([
			self::COMMAND_NAME,
			'set-url',
			$this->name,
			$url,
		]);

		if ($command->isValid()) $this->url = $url;
		else {
			$this->onError($this, $command);

			return $this->executeError($command);
		}

		$this->onSuccess($this, $command);

		return $this;
	}

	public function setName($name)
	{
		$command = $this->repository->run([
			self::COMMAND_NAME,
			'rename',
			$this->name,
			$name,
		]);
		if ($command->isValid()) $this->name = $name;
		else {
			$this->onError($this, $command);

			return $this->executeError($command);
		}

		$this->onSuccess($this, $command);

		return $this;
	}

	public function fetch()
	{
		return $this->repository->fetch($this->name);
	}

//	public function push()
//	{
//		return $this->repository->push($this->name);
//	}

	public function remove()
	{
		$command = $this->repository->run([
			self::COMMAND_NAME,
			'rm',
			$this->name,
		]);

		return $command->isValid();
	}

	/**
	 * @param Response $command
	 *
	 * @return bool
	 */
	protected function executeError(Response $command)
	{
		if ($this->throwExceptions === FALSE) return FALSE;
		throw new InvalidStateException($command->getError());
	}

}