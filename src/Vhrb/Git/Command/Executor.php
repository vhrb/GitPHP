<?php
namespace Vhrb\Git\Command;

use Nette\Utils\DateTime;
use Vhrb\Git\ExecuteCommandException;

class Executor implements IExecutor
{

	/** @var  DateTime */
	protected $runDate;

	public function run(Request $request)
	{
		$this->runDate = new DateTime();
		$tmpDir = $this->prepareTmpDir();

		$descriptorspec = [
			1 => ["pipe", "w"],  // stdout is a pipe that the child will write to
			2 => ["pipe", "w"],  // stdout is a pipe that the child will write to
		];

		$process = proc_open($request->getCommand(), $descriptorspec, $pipes, $request->getCwd());

		if (!is_resource($process)) {
			throw new ExecuteCommandException('Execute error!');
		}

		$out = trim(stream_get_contents($pipes[1]));
		if ($out) $this->writeLog($tmpDir . 'stdout.txt', $out);
		fclose($pipes[1]);

		$error = trim(stream_get_contents($pipes[2]));
		$this->writeLog($tmpDir . 'error.txt', $error);
		fclose($pipes[2]);

		$exitCode = proc_close($process);
		$valid = $exitCode === 0;
		$this->writeLog($tmpDir . 'process.txt', $request->getCommand() . ' ' . ($valid ? "OK" : "ERROR"));

		return new Response([
			'out' => $out,
			'error' => $error,
			'valid' => $valid,
			'exitCode' => $exitCode,
			'command' => $request->getCommand(),
			'runDate' => $this->runDate,
			'cwd' => $request->getCwd(),
		]);
	}

	/**
	 * @return string
	 */
	protected function prepareTmpDir()
	{
		$tmpDir = __DIR__ . '/../../../../tmp/';
		if (!file_exists($tmpDir)) {
			mkdir($tmpDir);
		}

		return $tmpDir;
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
}