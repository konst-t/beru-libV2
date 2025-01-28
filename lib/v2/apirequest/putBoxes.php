<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Iplogic\Beru\V2\Helper;

class putBoxes extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * PUT https://api.partner.market.yandex.ru/campaigns/{campaignId}/orders/{orderId}/boxes
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/orders/setOrderBoxLayout
	 *
	 * @param array $boxes
	 * @param int $orderId
	 * @param bool $delete
	 * @return array
	 */
	public function send($boxes = [], $orderId = 0, $delete = false)
	{
		$arr = ["boxes" => $boxes];
		if( $delete ) {
			$arr["allowRemove"] = true;
		}
		$data = Helper::jsonEncode($arr);
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/orders/" . $orderId . "/boxes.json";
		return $this->query("PUT", $this->url . $path, $data);
	}

}