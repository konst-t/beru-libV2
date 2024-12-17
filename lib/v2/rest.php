<?php
/**
 *
 *   IN WORK!!! NOT FINISHED
 *
 */


namespace Iplogic\Beru\V2;

use \Bitrix\Main\Application;

use \Iplogic\Beru\V2\ORM\ProfileTable;

/**
 * Processing REST requests to the site
 *
 * Class REST
 * @package Iplogic\Beru\V2
 */
class REST
{
	/**
	 * @var array
	 */
	public $arProfile;

	/**
	 * @var string
	 */
	public $serverMethod;

	/**
	 * @var string
	 */
	public $actionClass;

	/**
	 * @var array
	 */
	protected $error = [];

	function __construct($profileID = false)
	{
		if( $profileID ) {
			$this->arProfile = ProfileTable::getRowById($profileID);
		}
		return;
	}

	public function initMethodFromUrl()
	{
		global $APPLICATION;
		$rsData = ProfileTable::getList(["filter" => ["ACTIVE" => "Y"]]);
		while( $arProfile = $rsData->Fetch() ) {
			if( $arProfile["BASE_URL"] == "" ) {
				continue;
			}
			$length = strlen($arProfile["BASE_URL"]);
			if( substr($APPLICATION->GetCurPage(false), 0, $length) == $arProfile["BASE_URL"] ) {
				$allowedMethods = [
					"cart"         => "Cart",
					"order_accept" => "Accept",
					"order_status" => "Status",
					"stocks"       => "Stocks",
				];
				$this->serverMethod = substr($APPLICATION->GetCurPage(false), $length - 1);
				if( array_key_exists($this->serverMethod, $allowedMethods) ) {
					$this->error = [
						"405",
						"Method Not Allowed",
						"Request method '" . $this->serverMethod . "' not supported",
					];
					return false;
				}
				if( !$this->checkAuthorization() ) {
					return false;
				}
				$this->arProfile = $arProfile;
				$this->actionClass = "\\Iplogic\\Beru\\V2\\REST\\" . $allowedMethods[$this->serverMethod];
				$this->arProfile = ProfileTable::getRowById($arProfile["ID"]);
				return true;
			}
		}
		$this->error = [
			"403",
			"Forbidden",
			"Profile not found",
		];
		return false;
	}

	public function executeMethod()
	{
		// TODO: Перенести из services/index.php
	}

	public function getErrorArray()
	{
		return [
			"error"  => [
				"code"    => (int)$this->error[0],
				"message" => $this->error[2],
			],
			"errors" => [
				[
					"code"    => $this->error[1],
					"message" => $this->error[2],
				],
			],
		];
	}

	public function checkAuthorization()
	{
		$headers = getallheaders();
		$token = "";
		foreach( $headers as $key => $message ) {
			if( strtolower($key) == "authorization" ) {
				$token = $message;
				break;
			}
		}
		unset($headers);
		if( $token == "" ) {
			$this->error = [
				"401",
				"Forbidden",
				"OAuth token is not specified",
			];
			return false;
		}
		if( $token != $this->arProfile["GET_TOKEN"] ) {
			$this->error = [
				"403",
				"Forbidden",
				"Wrong token",
			];
			return false;
		}
		return true;
	}

}