<?php

namespace Iplogic\Beru\V2\ApiRequest;

class getMPDeliveryList extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * GET https://api.partner.market.yandex.ru/delivery/services
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/api/delivery-services/getDeliveryServices
	 *
	 * @return array
	 */
	public function send() {
		$path = "delivery/services.json";
		return $this->query("GET", $this->url . $path);
	}

}