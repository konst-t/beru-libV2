<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Iplogic\Beru\Control;

class setHidden extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * POST https://api.partner.market.yandex.ru/campaigns/{campaignId}/hidden-offers
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/assortment/addHiddenOffers
	 *
	 * @param array $offers = offerId array
	 * @return array
	 */
	public function send($offers = [])
	{
		$data = Control::jsonEncode($offers);
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/hidden-offers.json";
		return $this->query("POST", $this->url . $path, $data);
	}

}