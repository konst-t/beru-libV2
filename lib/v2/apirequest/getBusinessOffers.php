<?php

namespace Iplogic\Beru\V2\ApiRequest;

use \Bitrix\Main\Web\Json;

class getBusinessOffers extends \Iplogic\Beru\V2\ApiRequest
{

	/**
	 * POST https://api.partner.market.yandex.ru/businesses/{businessId}/offer-mappings
	 *
	 * https://yandex.ru/dev/market/partner-api/doc/ru/reference/business-assortment/getOfferMappings
	 *
	 * @param array $arParams = [limit, page_token]
	 * @param array $body = [archived, cardStatuses, categoryIds, offerIds, tags, vendorNames]
	 * @return array
	 */
	public function send($arParams = [], $body = [])
	{
		if( empty($body) ) {
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
		$path = "businesses/" . $this->arProfile["BUSINESS_ID"] . "/offer-mappings" . $stParams;
		return $this->query("POST", $this->url . $path, $data);
	}

}