<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Bitrix\Main\Web\Json;

class setOrderStatuses extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * POST https://api.partner.market.yandex.ru/campaigns/{campaignId}/orders/status-update
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/orders/updateOrderStatuses
	 *
	 * @param array $orders = [[id, status, substatus], ...]
	 * @return array
	 */
	public function send($orders = [])
	{
		$data = Json::encode(
			[
				"orders" => $orders,
			]
		);
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/orders/status-update.json";
		return $this->query("POST", $this->url . $path, $data);
	}

}