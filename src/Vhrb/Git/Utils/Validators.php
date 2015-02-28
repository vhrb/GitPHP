<?php

namespace Vhrb\Git;

use Nette\Utils\Strings;

abstract class Validators
{
	const URI_EXP = '[a-z]+@*.+\.git';

	/**
	 * @param $url
	 *
	 * @return mixed
	 */
	public static function validateUrl($url)
	{
		$url = trim($url);
		if (!\Nette\Utils\Validators::isUrl($url) && !Strings::match($url, '~^' . self::URI_EXP . '$~i')) throw new InvalidArgumentException('Invalid url: ' . $url);

		return $url;
	}
}
