<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Bitrix\Main\Web\Json;

class setOrderStatus extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * PUT https://api.partner.market.yandex.ru/campaigns/{campaignId}/orders/{orderId}/status
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/orders/updateOrderStatus
	 *
	 * @param int $orderId
	 * @param string $status
	 * @param string $substatus
	 * @return array
	 */
	public function send($orderId = 0, $status = null, $substatus = null)
	{
		$data = Json::encode(
			[
				"order" => [
					"status"    => $status,
					"substatus" => $substatus,
				],
			]
		);
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/orders/" . $orderId . "/status.json";
		return $this->query("PUT", $this->url . $path, $data);
	}

}