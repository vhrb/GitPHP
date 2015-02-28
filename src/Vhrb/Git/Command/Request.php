<?php
namespace Vhrb\Git\Command;

class Request
{

	/** @var  string */
	protected $command;

	/** @var  string (path) */
	protected $cwd;

	/** @var array */
//	protected $args = [];

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
	public function getCwd()
	{
		return $this->cwd;
	}

	/**
	 * @param string $cwd
	 */
	public function setCwd($cwd)
	{
		$this->cwd = $cwd;
	}

	public function __toString()
	{
		return $this->getCommand();
	}

	/**
	 * @return string
	 */
	public function getCommand()
	{
		return $this->command;
	}

	/**
	 * @param string $command
	 */
	public function setCommand($command)
	{
		$this->command = $command;
	}
}