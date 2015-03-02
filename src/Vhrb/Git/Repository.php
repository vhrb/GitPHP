<?php
namespace Vhrb\Git;

use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Vhrb\Git\Command\Executor;
use Vhrb\Git\Command\IExecutor;
use Vhrb\Git\Command\Request;
use Vhrb\Git\Command\Response;

class Repository
{

	/** @var  string */
	protected $path;

	/** @var  NULL|Request */
	protected $lastRequest;

	protected $commandExecutor;

	function __construct($path = NULL, $create = FALSE)
	{
		if ($path) $this->setPath($path, $create);

		return $this;
	}

	/**
	 * @return IExecutor
	 */
	public function getExecutor()
	{
		if ($this->commandExecutor === NULL) $this->commandExecutor = new Executor();

		return $this->commandExecutor;
	}

	/**
	 * @param IExecutor $executor
	 *
	 * @return $this
	 */
	public function setExecutor(IExecutor $executor)
	{
		$this->commandExecutor = $executor;

		return $this;
	}

	/**
	 * @param array $args
	 *
	 * @return Response
	 */
	public function run(array $args)
	{
		$this->lastRequest = new Request();
		$this->lastRequest->setCwd($this->path);
		$this->lastRequest->setCommand(Git::getGitCommand() . ' ' . implode(' ', $args));

		return $this->getExecutor()->run($this->lastRequest);
	}

	/**
	 * @param $path
	 * @param bool $create
	 *
	 * @return $this
	 */
	public function setPath($path, $create = FALSE)
	{
		$this->path = $path;
		$this->validate($create);

		if (!$create) return $this;

		$this->run([
			'init',
			$this->path,
		]);
	}

	protected function validate()
	{
		if (!file_exists($this->path) && empty($this->path)) {
			throw new InvalidArgumentException('Invalid repository path: ' . $this->path);
		}
	}

	/**
	 * @param string $remote
	 *
	 * @return Response
	 */
	public function fetch($remote = 'origin')
	{
		$command = $this->run([
			'fetch',
			$remote,
		]);

		return $command;
	}

	/**
	 * @return Response
	 */
	public function fetchAll()
	{
		return $this->fetch(NULL);
	}

	/**
	 * @param $point
	 *
	 * @return Response
	 */
	public function checkout($point)
	{
		$command = $this->run([
			'checkout',
			$point,
		]);

		return $command;
	}

	/**
	 * @param $point
	 * @param bool $hard
	 *
	 * @return Response
	 */
	public function reset($point, $hard = FALSE)
	{
		$command = $this->run([
			'reset' . ($hard ? ' --hard' : NULL),
			$point,
		]);

		return $command;
	}

	/**
	 * @param $name
	 * @param $url
	 *
	 * @return bool
	 */
	public function addRemote($name, $url)
	{
		return $this->run([
			'remote',
			'add',
			$name,
			$url,
		]);
	}

	/**
	 * @return array
	 */
	public function remoteList()
	{
		$command = $this->run([
			'remote',
			'-v',
		]);

		$remotes = new ArrayHash();
		foreach (explode("\n", $command->getOut()) as $remoteString) {
			if (empty($remoteString)) continue;

			$matches = Strings::match($remoteString, '~([a-z0-9]+)\W(.+)\W(push|fetch)~i');
			if (count($matches) !== 4) throw new InvalidStateException('Invalid remote: ' . $remoteString);

			$name = $matches[1];
			$url = $matches[2];
//			$type = $matches[3];

			if (!$remotes->offsetExists($name)) {
				$remote = new Remote($this, $name, $url);
//				var_dump($name);
				$remotes->$name = $remote;
			}
//			$remote->addType($type);
		}

		return $remotes;
	}

	public function getLastRequest()
	{
		return $this->lastRequest;
	}

}