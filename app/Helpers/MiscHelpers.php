<?php
namespace App\Helpers;

class MiscHelpers 
{

	/**
	 * Base Url
	 * @return string
	 * 
	*/
	public static function getBaseUrl():string
	{
		return (config('app.env') === 'local') ? '192.168.1.69' : config('app.url');
	}
}