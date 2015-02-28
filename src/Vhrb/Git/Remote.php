<?php
namespace Vhrb\Git;

use Nette\Object;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

class Remote extends Object
{
	const URI_EXP = '[a-z]+@*.+\.git';

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
		$this->url = $this->validateUrl($url);
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
	 * @param Command $command
	 *
	 * @return bool
	 */
	protected function executeError(Command $command)
	{
		if ($this->throwExceptions === FALSE) return FALSE;
		throw new InvalidStateException($command->getError());
	}

	/**
	 * @param $url
	 *
	 * @return mixed
	 */
	protected function validateUrl($url)
	{
		$url = trim($url);
		if (!Validators::isUrl($url) && !Strings::match($url, '~^' . self::URI_EXP . '$~i')) throw new InvalidArgumentException('Invalid url: ' . $url);

		return $url;
	}

}