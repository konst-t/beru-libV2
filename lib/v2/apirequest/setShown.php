<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Iplogic\Beru\Control;

class setShown extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * POST https://api.partner.market.yandex.ru/campaigns/{campaignId}/hidden-offers/delete
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/assortment/deleteHiddenOffers
	 *
	 * @param array $offers = [hiddenOffers = [offerId, ...]]
	 * @return array
	 */
	public function send($offers = [])
	{
		$data = Control::jsonEncode($offers);
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/hidden-offers/delete.json";
		return $this->query("POST", $this->url . $path, $data);
	}

}