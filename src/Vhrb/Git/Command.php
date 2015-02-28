<?php
namespace Vhrb\Git;

use Nette\Utils\DateTime;

class Command
{

	/** @var  string */
	protected $command;

	/** @var  bool */
	protected $valid;

	/** @var  string */
	protected $error;

	/** @var  int */
	protected $exitCode;

	/** @var  string */
	protected $out;

	/** @var  DateTime */
	protected $runDate;

	/** @var  string (path) */
	protected $cwd;

	/** @var array */
	protected $args = [];

	function __construct($command = NULL)
	{
		if ($command !== NULL)
			$this->setCommand($command);
	}

	public final function run()
	{
		$this->validate();
		$this->runDate = new DateTime();
		$tmpDir = $this->prepareTmpDir();

		$descriptorspec = [
			1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
			2 => ["pipe", "w"],  // stdout is a pipe that the child will write to
		];

		$process = proc_open($this->command, $descriptorspec, $pipes, $this->cwd);

		if (!is_resource($process)) {
			$this->valid = FALSE;

			throw new ExecuteCommandException('Execute error!');
		}

		$this->out = trim(stream_get_contents($pipes[1]));
		if ($this->getOut()) $this->writeLog($tmpDir . 'stdout.txt', $this->getOut());
		fclose($pipes[1]);

		$this->error = trim(stream_get_contents($pipes[2]));
		$this->writeLog($tmpDir . 'error.txt', $this->getError());
		fclose($pipes[2]);

		$this->exitCode = proc_close($process);
		$this->valid = $this->exitCode === 0;
		$this->writeLog($tmpDir . 'process.txt', $this->command . ' ' . ($this->valid ? "OK" : "ERROR"));

		return $this->valid;
	}

	/**
	 * @param $filename
	 * @param $data
	 */
	protected function writeLog($filename, $data)
	{
		$output = sprintf("[%s] pid=%s: %s\n", $this->runDate->format('Y-m-d H-i-s'), getmygid(), $data);
		file_put_contents($filename, $output, FILE_APPEND | LOCK_EX);
	}

	/**
	 * @return string
	 */
	protected function prepareTmpDir()
	{
		$tmpDir = __DIR__ . '/../../../tmp/';
		if (!file_exists($tmpDir)) {
			mkdir($tmpDir);
		}

		return $tmpDir;
	}

	protected function validate()
	{
		if (empty($this->command) || !is_string($this->command)) throw new InvalidStateException("Invalid command '$this->command'!");
	}

	/**
	 * @param string $cwd
	 */
	public function setCwd($cwd)
	{
		$this->cwd = $cwd;
	}

	/**
	 * @param $command
	 *
	 * @return $this
	 */
	public function setCommand($command)
	{
		$this->command = $command;

		return $this;
	}

	/**
	 * @param $command
	 *
	 * @return $this
	 */
	public function appendCommand($command)
	{
		$this->command .= ' ' . trim($command);

		return $this;
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return $this
	 */
	public function addArgument($name, $value)
	{
		$this->args[trim($name)] = trim($value);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCommand()
	{
		return $this->command;
	}

	/**
	 * @return boolean
	 */
	public function isValid()
	{
		return $this->valid;
	}

	/**
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return string
	 */
	public function getOut()
	{
		return $this->out;
	}


	public function __toString()
	{
		return $this->isValid() ? $this->getOut() : $this->getError();
	}
}