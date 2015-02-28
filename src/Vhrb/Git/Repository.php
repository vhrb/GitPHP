<?php
namespace Vhrb\Git;

use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

class Repository
{

	/** @var  string */
	protected $path;

	/** @var  NULL|Command */
	protected $lastCommand;

	function __construct($path, $create = FALSE)
	{
		$this->setPath($path, $create);

		return $this;
	}

	public function run(array $args)
	{
		$this->lastCommand = new Command();
		$this->lastCommand->setCwd($this->path);
		$this->lastCommand->setCommand(Git::getGitCommand() . ' ' . implode(' ', $args));
		$this->lastCommand->run();

		return $this->lastCommand;
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

	/**
	 * @param bool $create
	 */
	protected function validate($create = FALSE)
	{
		if (!file_exists($this->path)) {
			if (!empty($this->path) && $create) {
				mkdir($this->path);
			}
			else {
				throw new InvalidArgumentException('Invalid repository path: ' . $this->path);
			}
		}
	}

	/**
	 * @param string $remote
	 *
	 * @return string
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
	 * @return string
	 */
	public function fetchAll()
	{
		$command = $this->run([
			'fetch',
		]);

		return $command;
	}

	public function checkout($branch)
	{
		$command = $this->run([
			'checkout',
			$branch,
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

}