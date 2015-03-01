<?php
namespace Vhrb\Git;

use Vhrb\Git\Command\Response;
use Nette\Object;
use Vhrb\Git\Utils\Validators;

class Remote extends Object
{

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
		$this->url = Validators::validateUrl($url);
	}

	public function setUrl($url)
	{
		$command = $this->repository->run([
			'remote',
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
			'remote',
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
			'remote',
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