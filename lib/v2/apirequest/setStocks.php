<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Iplogic\Beru\V2\Helper;

class setStocks extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * PUT https://api.partner.market.yandex.ru/campaigns/{campaignId}/offers/stocks
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/stocks/updateStocks
	 *
	 * @param array $arRequest = [skus = [[sku, items = [count, updatedAt]], ...]]
	 * @return array
	 */
	public function send($arRequest = [])
	{
		$data = Helper::jsonEncode($arRequest);
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/offers/stocks.json";
		return $this->query("PUT", $this->url . $path, $data);
	}

}