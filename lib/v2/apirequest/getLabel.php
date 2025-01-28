<?php

namespace Iplogic\Beru\V2\ApiRequest;

class getLabel extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * GET https://api.partner.market.yandex.ru/campaigns/{campaignId}/orders/{orderId}/delivery/shipments/{shipmentId}/boxes/{boxId}/label
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/orders/generateOrderLabel
	 *
	 * @param array $arParams = [ORDER_ID, SHIPMENT_ID, BOX_ID]
	 * @return array
	 */
	public function send($arParams = [])
	{
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/orders/" . $arParams["ORDER_ID"] .
			"/delivery/shipments/" . $arParams["SHIPMENT_ID"] . "/boxes/" . $arParams["BOX_ID"] .
			"/label.json?format=A9";
		return $this->query("GET", $this->url . $path, null, false, true);
	}

}