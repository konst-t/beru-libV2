<?php

namespace Iplogic\Beru\V2;

/**
 * Getting information about products
 *
 * Class Product
 * @package Iplogic\Beru\V2
 */
class Product extends ORM\ProductTable
{

	public static function getProductIdBySku($SKU_ID, $arProfile)
	{
		$arProdIBlock = \CCatalogSKU::GetInfoByProductIBlock($arProfile["IBLOCK_ID"]);
		if( is_array($arProdIBlock) ) {
			$offerIBlockID = $arProdIBlock["IBLOCK_ID"];
		}
		if( !isset($arProfile["PROP"]["SHOP_SKU_ID"]) ) {
			return false;
		}
		$ident = $arProfile["PROP"]["SHOP_SKU_ID"];
		$arFilter = ["ACTIVE" => "Y"];
		if( $ident["TYPE"] == "element_fields" ) {
			$arFilter[$ident["VALUE"]] = $SKU_ID;
		}
		else {
			$arFilter["PROPERTY_" . $ident["VALUE"]] = $SKU_ID;
		}
		$arEl = false;
		if( $offerIBlockID ) {
			$arFilter["IBLOCK_ID"] = $offerIBlockID;
			$rsData = \CIBlockElement::getList([], $arFilter);
			$arEl = $rsData->Fetch();
		}
		if( !$arEl ) {
			$arFilter["IBLOCK_ID"] = $arProfile["IBLOCK_ID"];
			$rsData = \CIBlockElement::getList([], $arFilter);
			$arEl = $rsData->Fetch();
		}
		if( !$arEl ) {
			return false;
		}
		return $arEl["ID"];
	}


	public static function getSkuByProductId($ID, $arProfile)
	{
		$SKU_ID = "";
		$rsData = \CIBlockElement::getList([], ["ID" => $ID]);
		$arEl = $rsData->Fetch();
		if( !$arEl ) {
			return false;
		}
		$ident = $arProfile["PROP"]["SHOP_SKU_ID"];
		if( $ident["TYPE"] == "element_fields" ) {
			$SKU_ID = $arEl[$ident["VALUE"]];
		}
		else {
			$rsProp = \CIBlockElement::GetProperty($arEl["IBLOCK_ID"], $arEl["ID"]);
			while( $arProp = $rsProp->Fetch() ) {
				if( $arProp["CODE"] == $ident["VALUE"] ) {
					$SKU_ID = $arProp["VALUE"];
				}
			}
		}

		if( $SKU_ID != "" ) {
			return self::getSKU($SKU_ID, $arProfile);
		}
		return false;

	}


	/**
	 * Collects an array of product cache from the modules catalog, sale and iblock
	 *
	 * @param $SKU_ID
	 * @param $arProfile
	 * @param array $arSelect
	 * @param bool $no_cache
	 * @return array|bool|mixed
	 */
	public static function getSKU($SKU_ID, $arProfile, $arSelect = [], $no_cache = false)
	{
		if( !$no_cache ) {
			if( $product = Product::getBySkuId($SKU_ID, $arProfile["ID"]) ) {
				if( $product["DETAILS"] != "" ) {
					return unserialize($product["DETAILS"]);
				}
			}
		}
		$service = ["CML2_LINK", "VAT", "ELEMENT_NAME", "ELEMENT_XML_ID", "SECTION_ID", "URL"];
		if( !count($arSelect) ) {
			foreach( $arProfile["PROP"] as $p ) {
				$arSelect[] = $p["NAME"];
			}
			$arSelect = array_merge($service, $arSelect);
		}
		$accord = [];
		foreach( $arSelect as $key => $prop ) {
			if( !isset($arProfile["PROP"][$prop]) && !in_array($prop, $service) ) {
				unset($arSelect[$key]);
			}
			else {
				$prop = $arProfile["PROP"][$prop];
				if( is_array($accord[$prop["TYPE"]]) ) {
					if( !in_array($prop["VALUE"], $accord[$prop["TYPE"]]) ) {
						$accord[$prop["TYPE"]][] = $prop["VALUE"];
					}
				}
				else {
					$accord[$prop["TYPE"]][] = $prop["VALUE"];
				}
			}
		}
		$offerIBlockID = false;
		$arProdIBlock = \CCatalogSKU::GetInfoByProductIBlock($arProfile["IBLOCK_ID"]);
		if( is_array($arProdIBlock) ) {
			$offerIBlockID = $arProdIBlock["IBLOCK_ID"];
		}
		$ident = $arProfile["PROP"]["SHOP_SKU_ID"];
		$arFilter = ["ACTIVE" => "Y"];
		if( $ident["TYPE"] == "element_fields" ) {
			$arFilter[$ident["VALUE"]] = $SKU_ID;
		}
		else {
			$arFilter["PROPERTY_" . $ident["VALUE"]] = $SKU_ID;
		}
		$arEl = false;
		if( $offerIBlockID ) {
			$arFilter["IBLOCK_ID"] = $offerIBlockID;
			$rsData = \CIBlockElement::getList([], $arFilter);
			$arEl = $rsData->Fetch();
		}
		if( !$arEl ) {
			$arFilter["IBLOCK_ID"] = $arProfile["IBLOCK_ID"];
			$rsData = \CIBlockElement::getList([], $arFilter);
			$arEl = $rsData->Fetch();
		}
		if( !$arEl ) {
			return false;
		}
		$is_offer = false;
		if( $arEl["IBLOCK_ID"] == $offerIBlockID ) {
			$is_offer = true;
		}

		$po = "PRODUCT";
		if( $is_offer ) {
			$po = "OFFER";
		}
		$rsProp = \CIBlockElement::GetProperty($arEl["IBLOCK_ID"], $arEl["ID"]);
		while( $arProp = $rsProp->Fetch() ) {
			$arEl["PROPERTIES"][$po][$arProp["CODE"]]["TYPE"] = $arProp["PROPERTY_TYPE"];
			if( $arProp["CODE"] == "CML2_LINK" ) {
				$CML2_LINK = $arProp["VALUE"];
			}
			$value = $arProp["VALUE"];
			if( $arProp["PROPERTY_TYPE"] == "L" ) {
				$value = $arProp["VALUE_ENUM"];
			}
			if( $arProp["MULTIPLE"] == "Y" ) {
				$arEl["PROPERTIES"][$po][$arProp["CODE"]]["VALUE"][] = $value;
			}
			else {
				$arEl["PROPERTIES"][$po][$arProp["CODE"]]["VALUE"] = $value;
			}
		}
		if( $is_offer ) {
			$po = "PRODUCT";
			$rsProp = \CIBlockElement::GetProperty($arProfile["IBLOCK_ID"], $CML2_LINK);
			while( $arProp = $rsProp->Fetch() ) {
				$arEl["PROPERTIES"][$po][$arProp["CODE"]]["TYPE"] = $arProp["PROPERTY_TYPE"];
				$value = $arProp["VALUE"];
				if( $arProp["PROPERTY_TYPE"] == "L" ) {
					$value = $arProp["VALUE_ENUM"];
				}
				if( $arProp["MULTIPLE"] == "Y" ) {
					$arEl["PROPERTIES"][$po][$arProp["CODE"]]["VALUE"][] = $value;
				}
				else {
					$arEl["PROPERTIES"][$po][$arProp["CODE"]]["VALUE"] = $value;
				}
			}
		}

		if( $is_offer ) {
			if( $CML2_LINK > 0 ) {
				$rsParent = \CIBlockElement::getById($CML2_LINK);
				$arParent = $rsParent->Fetch();
				$section = $arParent["IBLOCK_SECTION_ID"];
				$code = $arParent["CODE"];
				$url = $arParent["DETAIL_PAGE_URL"];
			}
		}
		else {
			$section = $arEl["IBLOCK_SECTION_ID"];
			$code = $arEl["CODE"];
			$url = $arEl["DETAIL_PAGE_URL"];
		}
		if( stristr($url, '#SECTION_CODE#') !== FALSE ) {
			$res = \CIblockSection::getList([], ["ID" => $section, "CHECK_PERMISSIONS" => "N"], false, ["CODE"]);
			$sec = $res->Fetch();
			$section_code = $sec["CODE"];
		}
		if( stristr($url, '#SECTION_CODE_PATH#') !== FALSE ) {
			$section_p = [];
			while( $section > 0 ) {
				$res = \CIblockSection::getList(
					[],
					["ID" => $section, "CHECK_PERMISSIONS" => "N"],
					false,
					["CODE", "IBLOCK_SECTION_ID"]
				);
				$sec = $res->Fetch();
				$section_p[] = $sec["CODE"];

				if( $sec["IBLOCK_SECTION_ID"] == "" ) {
					$section = 0;
				}
				else {
					$section = $sec["IBLOCK_SECTION_ID"];
				}
			}
			$section_path = implode("/", $section_p);
		}
		$url = str_replace(
			[
				"#SITE_DIR#",
				"#CODE#",
				"#ELEMENT_ID#",
				"#ELEMENT_CODE#",
				"#SECTION_ID#",
				"#SECTION_CODE#",
				"#SECTION_CODE_PATH#",
			],
			[
				"https://" . Helper::getOption("domen"),
				$code,
				$arEl["ID"],
				$code,
				$section,
				$section_code,
				$section_path,
			],
			$url
		);
		if( array_key_exists("stores", $accord) ) {
			$rsStore =
				\CCatalogStoreProduct::GetList([], ['PRODUCT_ID' => $arEl["ID"]], false, false, ['AMOUNT', 'STORE_ID']);
			while( $arStore = $rsStore->Fetch() ) {
				$arStores[$arStore['STORE_ID']] = $arStore['AMOUNT'];
			}
		}
		if(
			array_key_exists("product_fields", $accord) || in_array("VAT", $arSelect) ||
			array_key_exists("from_product", $accord)
		) {
			$arProduct = \CCatalogProduct::GetByID($arEl["ID"]);
		}
		if( array_key_exists("prices", $accord) ) {
			$arPrices_ = \Bitrix\Catalog\PriceTable::getList(["filter" => ["PRODUCT_ID" => $arEl["ID"]]])->fetchAll();
			foreach( $arPrices_ as $price ) {
				$arPrices[$price["CATALOG_GROUP_ID"]] = $price["PRICE"];
			}
		}
		if( array_key_exists("min_discount_price", $accord) ) {
			$arMinPrice = \CCatalogProduct::GetOptimalPrice($arEl["ID"]);
			$minPrice = $arMinPrice["RESULT_PRICE"]["DISCOUNT_PRICE"];
		}
		$arResult = [
			"SHOP_SKU_ID" => $SKU_ID,
			"PRODUCT_ID"  => $arEl["ID"],
			"IS_OFFER"    => $is_offer,
			"SECTION_ID"  => $section,
			"URL"         => $url,
		];
		foreach( $arSelect as $prop ) {
			if( $is_offer && $prop == "CML2_LINK" ) {
				if( $CML2_LINK > 0 ) {
					$arResult["CML2_LINK"] = $CML2_LINK;
				}
				else {
					$rsProp = \CIBlockElement::GetProperty($arEl["IBLOCK_ID"], $arEl["ID"]);
					while( $arProp = $rsProp->Fetch() ) {
						if( $arProp["CODE"] == "CML2_LINK" ) {
							$arResult["CML2_LINK"] = $arProp["VALUE"];
						}
					}
				}
			}

			switch( $arProfile["PROP"][$prop]["TYPE"] ) {

				case 'element_fields':
					$arResult[$prop] = $arEl[$arProfile["PROP"][$prop]["VALUE"]];
					if(
						$arProfile["PROP"][$prop]["VALUE"] == "PREVIEW_PICTURE"
						|| $arProfile["PROP"][$prop]["VALUE"] == "DETAIL_PICTURE"
					) {
						$arResult[$prop] = "https://" . Helper::getOption("domen") . \CFile::GetPath($arResult[$prop]);
					}
					break;

				case 'product_props':
					$arResult[$prop] =
						$arEl["PROPERTIES"]["PRODUCT"][$arProfile["PROP"][$prop]["VALUE"]]["VALUE"];
					$propType = $arEl["PROPERTIES"]["PRODUCT"][$arProfile["PROP"][$prop]["VALUE"]]["TYPE"];
					break;

				case 'offer_props':
					$arResult[$prop] = $arEl["PROPERTIES"]["OFFER"][$arProfile["PROP"][$prop]["VALUE"]]["VALUE"];
					$propType = $arEl["PROPERTIES"]["OFFER"][$arProfile["PROP"][$prop]["VALUE"]]["TYPE"];
					break;

				case 'common_props':
					$arResult[$prop] = $arEl["PROPERTIES"]["OFFER"][$arProfile["PROP"][$prop]["VALUE"]]["VALUE"];
					$propType = $arEl["PROPERTIES"]["OFFER"][$arProfile["PROP"][$prop]["VALUE"]]["TYPE"];
					if( !$arResult[$prop] ) {
						$arResult[$prop] =
							$arEl["PROPERTIES"]["PRODUCT"][$arProfile["PROP"][$prop]["VALUE"]]["VALUE"];
						$propType = $arEl["PROPERTIES"]["PRODUCT"][$arProfile["PROP"][$prop]["VALUE"]]["TYPE"];
					}
					break;

				case 'stores':
					if( $arStores[$arProfile["PROP"][$prop]["VALUE"]] > 0 ) {
						$arResult[$prop] = $arStores[$arProfile["PROP"][$prop]["VALUE"]];
					}
					else {
						$arResult[$prop] = 0;
					}
					break;

				case 'product_fields':
					$arResult[$prop] = $arProduct[$arProfile["PROP"][$prop]["VALUE"]];
					break;

				case 'current_time':
					$arResult[$prop] = Helper::timeFix(date(DATE_ISO8601, time()));
					break;

				case 'element_last_change':
					$arResult[$prop] = Helper::timeFix(date(DATE_ISO8601, $arEl["TIMESTAMP_X_UNIX"]));
					break;

				case 'prices':
					$arResult[$prop] = $arPrices[$arProfile["PROP"][$prop]["VALUE"]];
					break;

				case 'min_discount_price':
					$arResult[$prop] = $minPrice;
					break;
				case 'from_product':
					if( $prop == "WEIGHT" ) {
						$arResult[$prop] = str_replace(",", ".", ($arProduct["WEIGHT"] / 1000));
					}
					if( $prop == "DIMENSIONS" ) {
						$arResult[$prop] = str_replace(
							",",
							".",
							($arProduct["LENGTH"] / 10) . "/" . ($arProduct["WIDTH"] / 10) . "/" .
							($arProduct["HEIGHT"] / 10)
						);
					}
					break;
			}
			if( $propType && $arResult[$prop] != "" ) {
				if( is_array($arResult[$prop]) ) {
					foreach( $arResult[$prop] as $key => $val )
						$arResult[$prop][$key] = self::replaceIdProps($propType, $val);
				}
				else {
					$arResult[$prop] = self::replaceIdProps($propType, $arResult[$prop]);
				}
			}
			unset($propType);
			if( $prop == "VAT" ) {
				$vat_id = $arProduct["VAT_ID"];
				if( $vat_id > 0 ) {
					$arVAT = \CCatalogVat::GetByID($vat_id)->Fetch();
					$arResult[$prop] = round($arVAT["RATE"]);
				}
			}
			if( $prop == "ELEMENT_NAME" ) {
				$arResult[$prop] = $arEl["NAME"];
			}
			if( $prop == "ELEMENT_XML_ID" ) {
				$arResult[$prop] = $arEl["XML_ID"];
			}
			if( $prop == "DISABLED" ) {
				$disabled = "false";
				if( $arResult[$prop] == "N" ) {
					$arResult[$prop] = "true";
				}
				if( $arResult[$prop] == 0 ) {
					$arResult[$prop] = "true";
				}
			}

		}
		return $arResult;
	}


	protected static function replaceIdProps($propType, $val)
	{
		if( $propType == "E" ) {
			$res = \CIBlockElement::GetByID($val);
			if( $arConEl = $res->GetNext() ) {
				$val = $arConEl["NAME"];
			}
		}
		elseif( $propType == "F" ) {
			$val = \CFile::GetPath($val);
		}
		return $val;
	}

}