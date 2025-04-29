<?php

/**
 *  @version 3.00
 */

class CApp
{
	const VERSION = '3.00';
	protected static $key = '';
	protected static $mid = '';
	protected static $rest = 'https://bxrest.highload24.ru:10443/rest/';

	/**
	 * @var $arParams array
	 * $arParams = [
	 *      'method'    => 'some rest method',
	 *      'params'    => []//array params of method
	 * ];
	 * @var $methodApi string
	 * @var $cb string
	 * @return mixed array|string|boolean curl-return or error
	 *
	 */
	protected static function callCurl($arParams, $methodApi, $cb = '')
	{
		if (!static::test()) {
			return [
				'error' => 'Need CApp::newCreatorAppClient(b24CreatorAppKey, memberId)'
			];
		}
		
		$url = static::$rest.$methodApi.'/';
		$arParams['key'] = static::$key;
		$arParams['mid'] = static::$mid;
		if ($methodApi == 'turn' || $methodApi == 'turn_current') {
			$arParams['cb'] = $cb;
		}
		$json = json_encode($arParams, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		
		try
		{
			$obCurl = curl_init();
			curl_setopt($obCurl, CURLOPT_URL, $url);
			curl_setopt($obCurl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($obCurl, CURLOPT_POSTREDIR, 10);
			curl_setopt($obCurl, CURLOPT_USERAGENT, 'Bitrix24 Creator App Client PHP ' . static::VERSION);
			curl_setopt($obCurl, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json'
			]);
			curl_setopt($obCurl, CURLOPT_POST, true);
			curl_setopt($obCurl, CURLOPT_POSTFIELDS, $json);
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

	/**
	 * Generate a request for callCurl()
	 *
	 * @var $arData array
	 * @var $halt int
	 * @return mixed array|string|boolean curl-return or error
	 */
	public static function callBatch($arData, $halt = 0)
	{
		$arResult = [];
		if(is_array($arData))
		{
			$arDataRest = [];
			$i = 0;
			foreach($arData as $key => $data)
			{
				if(!empty($data[ 'method' ]))
				{
					$i++;
					if(50 >= $i)
					{
						$arDataRest[ 'cmd' ][ $key ] = $data[ 'method' ];
						if(!empty($data[ 'params' ]))
						{
							$arDataRest[ 'cmd' ][ $key ] .= '?' . http_build_query($data[ 'params' ]);
						}
					}
				}
			}
			if(!empty($arDataRest))
			{
				$arDataRest[ 'halt' ] = $halt;
				$arPost = [
					'method' => 'batch',
					'params' => $arDataRest
				];
				$arResult = static::callCurl($arPost, 'call');
			}
		}
		return $arResult;
	}

	/**
	 * Generate a request for callCurl()
	 *
	 * @var $method string
	 * @var $params array method params
	 * @var $callback string
	 * @return mixed array|string|boolean curl-return or error
	 */
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

	/**
	 * Generate a request for callCurl()
	 *
	 * @var $arData array
	 * @var $halt int
	 * @var $callback string
	 * @return mixed array|string|boolean curl-return or error
	 */
	public static function turnBatch($arData, $halt = 0, $callback = '')
	{
		$arResult = [];
		if(is_array($arData))
		{
			$arDataRest = [];
			$i = 0;
			foreach($arData as $key => $data)
			{
				if(!empty($data[ 'method' ]))
				{
					$i++;
					if(50 >= $i)
					{
						$arDataRest[ 'cmd' ][ $key ] = $data[ 'method' ];
						if(!empty($data[ 'params' ]))
						{
							$arDataRest[ 'cmd' ][ $key ] .= '?' . http_build_query($data[ 'params' ]);
						}
					}
				}
			}
			if(!empty($arDataRest))
			{
				$arDataRest[ 'halt' ] = $halt;
				$arPost = [
					'method' => 'batch',
					'params' => $arDataRest
				];
				$arResult = static::callCurl($arPost, 'turn', $callback);
			}
		}
		return $arResult;
	}

	/**
	 * Generate a request for callCurl()
	 *
	 * @var $id int
	 * @var $params int 0|1
	 * @var $userId int
	 * @return mixed array|string|boolean curl-return or error
	 */
	public static function result($id, $params = 0, $userId = '')
	{
		$arPost = [
			'id' => (int)$id,
			'params' => $params,
		];

		if (!empty($userId)) {
			$userId = '_'.$userId;
		}

		if (!empty($id)) {
			$result = static::callCurl($arPost, 'result'.$userId);
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

	/**
	 * Convert to json
	 * @return string
	 */
	public static function ConvertToJson($data)
	{
		$return = json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_FORCE_OBJECT);
		return $return;
	}
}

class CAppCurrent extends CApp
{
	protected static $ex = 0;
	protected static $aid = '';
	protected static $rid = '';
	protected static $uid = 0;

	/**
	 * Generate a request for callCurl()
	 *
	 * @var $method string
	 * @var $params array method params
	 * @return mixed array|string|boolean curl-return or error
	 */
	public static function call($method, $params = [])
	{
		if (static::testCurrentUserId()) {
			$arPost = [
				'method' => $method,
				'params' => $params,
				'uid' => static::$uid,
			];
		}
		elseif (static::testCurrent()) {
			$arPost = [
				'method' => $method,
				'params' => $params,
				'ex' => static::$ex,
				'aid' => static::$aid,
				'rid' => static::$rid,
			];
		} else {
			return [
				'error' => 'Need CAppCurrent::newClient(timeOpenApp, authId, refreshId) or CAppCurrent::newClientByUserID(id)'
			];
		}

		if (!empty($method)) {
			$result = static::callCurl($arPost, 'call_current');
			return $result;
		} else {
			return [
				'error' => 'Method not found'
			];
		}
	}

	/**
	 * Generate a request for callCurl()
	 *
	 * @var $method string
	 * @var $params array method params
	 * @var $callback string
	 * @return mixed array|string|boolean curl-return or error
	 */
	public static function turn($method, $params = [], $callback = '')
	{
		if (static::testCurrentUserId()) {
			$arPost = [
				'method' => $method,
				'params' => $params,
				'uid' => static::$uid,
			];
		} else {
			return [
				'error' => 'Need CAppCurrent::newClientByUserID(id)'
			];
		}

		if (!empty($method)) {
			$result = static::callCurl($arPost, 'turn_current', $callback);
			return $result;
		} else {
			return [
				'error' => 'Method not found'
			];
		}
	}
	
	/**
	 * Set a key client
	 *
	 * @var $timestamp int
	 * @var $authId string
	 * @var $refreshId string
	 * @return mixed boolean or null
	 */
	public static function newClient($timestamp, $authId, $refreshId)
	{
		static::$ex = $timestamp+3600;
		static::$aid = $authId;
		static::$rid = $refreshId;
		return true;
	}

	/**
	 * Set a key client
	 *
	 * @var $userId int
	 * @return mixed boolean or null
	 */
	public static function newClientByUserID($userId)
	{
		static::$uid = (int)$userId;
		return true;
	}

	/**
	 * Check for existence of appKey and memberId
	 * @return boolean
	 */
	public static function testCurrent()
	{
		if (static::$ex == 0 || empty(static::$aid) || empty(static::$rid)) {
			return false;
		}
		return true;
	}
	/**
	 * Check for existence of userId
	 * @return boolean
	 */
	public static function testCurrentUserId()
	{
		if (!static::$uid > 0) {
			return false;
		}
		return true;
	}
}

?>
