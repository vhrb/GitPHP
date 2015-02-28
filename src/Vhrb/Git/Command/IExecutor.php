<?php

namespace Vhrb\Git\Command;

interface IExecutor
{
	/**
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function run(Request $request);
}