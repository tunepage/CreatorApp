<?php

/**
 *  @version 1.00
 */

class CApp
{
	protected static $key = '';
	protected static $mid = '';
	protected static $rest = 'https://bxrest.highload24.ru/rest/';

	/**
	 * @var $arParams array
	 * $arParams = [
	 *      'method'    => 'some rest method',
	 *      'params'    => []//array params of method
	 * ];
	 * @return mixed array|string|boolean curl-return or error
	 *
	 */
	protected static function callCurl($arParams, $method, $cb = '')
	{
		if (!static::test()) {
			return [
				'error' => 'Need CApp::newCreatorAppClient(b24CreatorAppKey, memberId)'
			];
		}
		
		$url = static::$rest.$method.'/';
		$arParams['key'] = static::$key;
		$arParams['mid'] = static::$mid;
		if ($method == 'turn') {
			$arParams['cb'] = $cb;
		}
		$sPostFields = http_build_query($arParams);
	
		try
		{
			$obCurl = curl_init();
			curl_setopt($obCurl, CURLOPT_URL, $url);
			curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($obCurl, CURLOPT_POSTREDIR, 10);
			curl_setopt($obCurl, CURLOPT_USERAGENT, 'Bitrix24 Creator App Client PHP 1.00');
			if($sPostFields)
			{
				curl_setopt($obCurl, CURLOPT_POST, true);
				curl_setopt($obCurl, CURLOPT_POSTFIELDS, $sPostFields);
			}
			curl_setopt($obCurl, CURLOPT_FOLLOWLOCATION, 1);
			$out = curl_exec($obCurl);
			$info = curl_getinfo($obCurl);
			$result = json_decode($out, true);
			curl_close($obCurl);
			return $result;
		}
		catch(Exception $e)
		{
			return [
				'error' => 'exception',
				'error_exception_code' => $e->getCode(),
				'error_information' => $e->getMessage(),
			];
		}
	}

	/**
	 * Generate a request for callCurl()
	 *
	 * @var $method string
	 * @var $params array method params
	 * @return mixed array|string|boolean curl-return or error
	 */

	public static function call($method, $params = [])
	{
		$arPost = [
			'method' => $method,
			'params' => $params
		];

		if (!empty($method)) {
			$result = static::callCurl($arPost, 'call');
			return $result;
		} else {
			return [
				'error' => 'Method not found'
			];
		}
	}

	public static function turn($method, $params = [], $callback = '')
	{
		$arPost = [
			'method' => $method,
			'params' => $params
		];

		if (!empty($method)) {
			$result = static::callCurl($arPost, 'turn', $callback);
			return $result;
		} else {
			return [
				'error' => 'Method not found'
			];
		}
	}

	public static function result($id)
	{
		$arPost = [
			'id' => $id,
		];

		if (!empty($id)) {
			$result = static::callCurl($arPost, 'result');
			return $result;
		} else {
			return [
				'error' => 'id not found'
			];
		}
	}

	/**
	 * Set a key client
	 *
	 * @var $appKey string
	 * @var $memberId string
	 * @return mixed boolean or null
	 */
	public static function newCreatorAppClient($appKey, $memberId)
	{
		static::$key = $appKey;
		static::$mid = $memberId;
		// if (empty(static::$key) || empty(static::$mid)) {
		// 	return null;
		// }
		return true;
	}
	/**
	 * Check for existence of appKey and memberId
	 * @return boolean
	 */
	public static function test()
	{
		if (empty(static::$key) || empty(static::$mid)) {
			return false;
		}
		return true;
	}
}

?>