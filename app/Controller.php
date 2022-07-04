<?php

namespace App;

abstract class Controller
{
	public static function init() {}

	public static function exec(): bool|string
	{
		static::init();

		$body = ob_get_contents();
		ob_end_clean();
		return $body;
	}
}