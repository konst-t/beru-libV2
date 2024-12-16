<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Bitrix\Main\Web\Json;

class getCampaignOffers extends \Iplogic\Beru\V2\ApiRequest
{
	/**
	 * POST https://api.partner.market.yandex.ru/campaigns/{campaignId}/offers
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/assortment/getCampaignOffers
	 *
	 * $arParams = [limit, page_token]
	 * $body = [statuses, categoryIds, offerIds, tags, vendorNames]
	 */
	public function send($arParams = [], $body = "")
	{
		if( $body == "" ) {
			$data = "{}";
		}
		else {
			$data = Json::encode($body);
		}
		$stParams = "";
		$arStParams = [];
		if( isset($arParams["limit"]) ) {
			$arStParams[] = "limit=" . $arParams["limit"];
		}
		if( isset($arParams["page_token"]) ) {
			$arStParams[] = "page_token=" . $arParams["page_token"];
		}
		if( count($arStParams) ) {
			$stParams = "?" . implode("&", $arStParams);
		}
		$path = "campaigns/" . $this->arProfile["COMPAIN_ID"] . "/offers" . $stParams;
		return $this->query("POST", $this->url . $path, $data);
	}

}