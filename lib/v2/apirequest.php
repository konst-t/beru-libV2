<?

namespace Iplogic\Beru\V2;

use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Web\HttpClient;
use \Bitrix\Main\Web\Json;

use \Iplogic\Beru\V2\ORM\ProfileTable;
use \Iplogic\Beru\V2\ORM\TaskTable;
use \Iplogic\Beru\V2\ORM\ErrorTable;
use \Iplogic\Beru\V2\ORM\ApiLogTable;


/**
 * Sending requests to the Yandex server
 *
 * Class ApiRequest
 * @package Iplogic\Beru\V2
 */
abstract class ApiRequest
{
	/**
	 * @var string
	 */
	public static $moduleID = "iplogic.beru";

	/**
	 * @var array
	 */
	protected $arProfile;

	/**
	 * @var string
	 */
	protected $url = "https://api.partner.market.yandex.ru/";

	/**
	 * @var HttpClient
	 */
	protected $cl;

	/**
	 * @var string[]
	 */
	protected $headers;


	function __construct($profileID = false)
	{
		$this->cl = new HttpClient(['socketTimeout' => 10]);
		$this->headers = [
			"Content-Type" => "application/json; charset=UTF-8",
		];
		if( $profileID ) {
			$this->setProfile(ProfileTable::getByIdFull($profileID));
		}
		return;
	}


	public function setProfile($profile)
	{
		if(is_array($profile)) {
			$this->arProfile = $profile;
		}
		elseif((int)$profile > 0) {
			$this->arProfile = ProfileTable::getByIdFull($profile);
		}
		$this->headers["Authorization"] =
			'OAuth oauth_token="' . $this->arProfile["SEND_TOKEN"] . '", oauth_client_id="' .
			$this->arProfile["CLIENT_ID"] . '"';
		foreach( $this->headers as $key => $val ) {
			$this->cl->setHeader($key, $val);
		}
		return;
	}

	protected function query($type, $path, $data = null, $task = false, $stop_repeating = null)
	{
		$arFields = [
			"PROFILE_ID"   => $this->arProfile["ID"],
			"TYPE"         => "OG",
			"STATE"        => "EX",
			"URL"          => $path,
			"REQUEST_TYPE" => $type,
			"REQUEST"      => $data,
		];
		$EID = ApiLogTable::add($arFields)->getId();
		$this->cl->query($type, $path, Helper::prepareText($data, true, true));
		$state = "RJ";
		if( $this->cl->getStatus() == 200 ) {
			$state = "OK";
		}

		$error = null;
		if( $state == "RJ" ) {
			if( Helper::isJson($this->cl->getResult()) ) {
				$res = Json::decode($this->cl->getResult());
				$error =
					$this->cl->getStatus() . ": " . $res["errors"][0]["code"] . " - " . $res["errors"][0]["message"];
			}
			else {
				$error = $this->cl->getStatus() . "Bad response status";
			}
		}
		$arLogFields = [
			"REQUEST_H" => $this->getRequestHeaders(),
			"STATE"     => $state,
			"RESPOND"   => Helper::fixUnicode($this->cl->getResult()),
			"RESPOND_H" => $this->getRespondHeaders(),
			"STATUS"    => $this->cl->getStatus(),
			"ERROR"     => $error,
		];
		if( $this->cl->getStatus() != 200 ) {
			$response = Json::decode(Helper::fixUnicode($this->cl->getResult()));
			if(
				$this->cl->getStatus() == 500 && $stop_repeating == null
			) {
				if( !$task ) {
					$arFields = [
						"PROFILE_ID"     => $this->arProfile["ID"],
						"UNIX_TIMESTAMP" => time() + Option::get(self::$moduleID, "task_trying_period", 60),
						"TYPE"           => "RQ",
						"STATE"          => "WT",
						"ENTITY_ID"      => $EID,
						"TRYING"         => 0,
					];
					TaskTable::add($arFields);
					$arLogFields["STATE"] = "DF";
				}
				else {
					if( ($task["TRYING"] + 1) >= Option::get(self::$moduleID, "task_trying_num", 3) ) {
						$stop_repeating = true;
						$this->putError($path, $data, $EID);
					}
				}
			}
			else {
				$this->putError($path, $data, $EID);
			}
		}
		$arLogFields["close"] = true;
		ApiLogTable::update($EID, $arLogFields);
		if( Helper::isJson($this->cl->getResult()) ) {
			$body = Json::decode($this->cl->getResult());
		}
		else {
			$body = $this->cl->getResult();
		}
		return [
			"status"         => $this->cl->getStatus(),
			"body"           => $body,
			"stop_repeating" => $stop_repeating,
		];
	}


	private function putError($path, $data, $EID)
	{
		$data = ($data ? $data : "");
		$details = "URL: " . $path . "<br><br>TOKEN: " . $this->arProfile["SEND_TOKEN"] . "<br><br>REQUEST<br><br>"
			. $data . "<br><br>";
		$response = Json::decode($this->cl->getResult());
		$arFields = [
			"PROFILE_ID" => $this->arProfile["ID"],
			"ERROR"      => $this->cl->getStatus() . ": " . $response["errors"][0]["code"] . " - " .
				$response["errors"][0]["message"],
			"DETAILS"    => $details,
			"LOG"        => $EID,
		];
		return ErrorTable::add($arFields);
	}


	private function getRequestHeaders()
	{
		$headers = "";
		foreach( headers_list() as $val ) {
			if( stristr($val, 'Content-Type:') === FALSE ) {
				$headers .= $val . "<br>";
			}
		}
		foreach( $this->headers as $key => $val ) {
			$headers .= $key . ": " . $val . "<br>";
		}
		return $headers;
	}


	private function getRespondHeaders()
	{
		$headers = "";
		foreach( $this->cl->getHeaders()->toArray() as $arHeader ) {
			foreach( $arHeader["values"] as $val ) {
				$headers .= $arHeader["name"] . ": " . $val . "<br>";
			}
		}
		return $headers;
	}

	abstract protected function send();

}
