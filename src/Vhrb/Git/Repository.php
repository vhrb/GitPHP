<?php
namespace Vhrb\Git;

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


	protected function run(array $args)
	{
		$this->lastCommand = new Command();
		$this->lastCommand->setCwd($this->path);
		$this->lastCommand->setCommand(implode(' ', $args));
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
			Git::getGitCommand(),
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
			Git::getGitCommand(),
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
			Git::getGitCommand(),
			'fetch',
		]);

		return $command;
	}

	public function checkout($branch)
	{
		$command = $this->run([
			Git::getGitCommand(),
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
			Git::getGitCommand(),
			'remote',
			'add',
			$name,
			$url,
		]);
	}

	/**
	 * @return array
	 */
	public function listRemote()
	{
		$out = $this->run([
			Git::getGitCommand(),
			'remote',
			'-v',
		])->getOut();

		return explode("\n", $out);
	}
}