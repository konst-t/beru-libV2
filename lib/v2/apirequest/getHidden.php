<?php

namespace Iplogic\Beru\V2\ApiRequest;

class getHidden extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * GET https://api.partner.market.yandex.ru/campaigns/{campaignId}/hidden-offers
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/assortment/getHiddenOffers
	 *
	 * @return array
	 */
	public function send()
	{
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/hidden-offers.json";
		return $this->query("GET", $this->url . $path);
	}

}