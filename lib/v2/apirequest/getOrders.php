<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Bitrix\Main\Web\Json;

class getOrders extends \Iplogic\Beru\V2\ApiRequest
{
	/**
	 * GET https://api.partner.market.yandex.ru/campaigns/{campaignId}/orders/{orderId}
	 * GET https://api.partner.market.yandex.ru/campaigns/{campaignId}/orders/
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/orders/getOrder
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/orders/getOrders
	 *
	 * @param array $params = [orderId] | [buyerType, dispatchType, fake, fromDate, hasCis, limit, onlyEstimatedDelivery,
	 * onlyWaitingForCancellationApprove, orderIds, page, pageSize, page_token, status, substatus, supplierShipmentDateFrom,
	 * supplierShipmentDateTo, toDate, updatedAtFrom, updatedAtTo]
	 * @return array
	 */
	public function send($params = [])
	{
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/orders";
		if( isset($params["orderId"]) ) {
			$path = $path . "/" . $params["orderId"] . ".json";
		}
		else {
			$stParams = "";
			$arStParams = [];
			foreach( $params as $param => $val ) {
				$stParams[] = $param . "=" . $val;
			}
			if( count($arStParams) ) {
				$stParams = "?" . implode("&", $arStParams);
			}
			$path = $path . ".json" . $stParams;
		}
		return $this->query("GET", $this->url . $path);
	}

}