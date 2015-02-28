<?php
namespace Vhrb\Git\Command;

use Nette\Utils\DateTime;

class Response
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

	public function __construct(array $data = NULL)
	{
		if ($data !== NULL)
			$this->populate($data);
	}

	private function populate($data)
	{
		foreach ($data as $name => $value) {
			$this->$name = $value;
		}
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

	/**
	 * @return int
	 */
	public function getExitCode()
	{
		return $this->exitCode;
	}

	/**
	 * @return DateTime
	 */
	public function getRunDate()
	{
		return $this->runDate;
	}

	/**
	 * @return string
	 */
	public function getCwd()
	{
		return $this->cwd;
	}

	public function __toString()
	{
		return $this->isValid() ? $this->getOut() : $this->getError();
	}
}