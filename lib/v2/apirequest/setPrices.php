<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Iplogic\Beru\Control;

class setPrices extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * POST https://api.partner.market.yandex.ru/campaigns/{campaignId}/offer-prices/updates
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/assortment/updatePrices
	 *
	 * @param array $offers = [[offerId, price = [currencyId, discountBase, value]], ...]
	 * @return array
	 */
	public function send($offers = [])
	{
		$data = Control::jsonEncode($offers);
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/offer-prices/updates.json";
		return $this->query("POST", $this->url . $path, $data);
	}

}