<?php

namespace Iplogic\Beru\V2\ApiRequest;

class getBoxes extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * GET https://api.partner.market.yandex.ru/campaigns/{campaignId}/orders/{orderId}/delivery/labels/data
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/orders/getOrderLabelsData
	 *
	 * @param int $orderId
	 * @return array
	 */
	public function send($orderId = 0)
	{
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/orders/" . $orderId . "/delivery/labels/data.json";
		return $this->query("GET", $this->url . $path, null, false, true);
	}

}