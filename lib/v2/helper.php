<?php
namespace Iplogic\Beru\V2;

use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Web\Json;

/**
 * Module helpers
 *
 * Class Helper
 * @package Iplogic\Beru\V2
 */
class Helper
{

	/**
	 * @var string
	 */
	public static $moduleID = "iplogic.beru";


	/**
	 * Simplified module setup call
	 *
	 * @param string $option
	 * @param string $default
	 * @return mixed
	 */
	public static function getOption($option, $default = "")
	{
		return Option::get(self::$moduleID, $option, $default, SITE_ID);
	}


	/**
	 * Cleans text from the database for output to the site
	 *
	 * @param string $s
	 * @return string
	 */
	public static function toHtml($s)
	{
		$s = str_replace(' ', '&nbsp;', $s);
		$s = nl2br($s);
		$s = str_replace('\r', '\n', $s);
		$s = str_replace('\n', '', $s);
		return ($s);
	}


	/**
	 * Prepares text for sending in JSON format
	 *
	 * @param string $str
	 * @param bool $charset
	 * @param bool $cdata
	 * @return false|string
	 */
	public static function prepareText($str, $charset = true, $cdata = false)
	{
		if( !$cdata ) {
			$bad = ["<", ">", "'", '"', "&"];
			$good = ["&lt;", "&gt;", "&apos;", "&quot;", "&amp;"];
			$str = str_replace($bad, $good, $str);
		}
		if( $charset && LANG_CHARSET != "UTF-8" ) {
			$str = iconv("cp1251", "UTF-8", $str);
		}
		return $str;
	}


	/**
	 * Converts text from UTF-8 for win-1251 encoding sites
	 *
	 * @param $str
	 * @return false|string
	 */
	public static function fixUnicode($str)
	{
		if( LANG_CHARSET != "UTF-8" ) {
			$str = iconv("UTF-8", "windows-1251", $str);
		}
		return $str;
	}


	/**
	 * Converts text from UTF-8 in an array recursively for win-1251 encoding sites
	 *
	 * @param $array
	 * @return array
	 */
	public static function fixUnicodeRecursive($array)
	{
		if( LANG_CHARSET == "UTF-8" ) {
			return $array;
		}
		foreach( $array as $key => $value ) {
			if( is_array($value) ) {
				$array[$key] = self::fixUnicodeRecursive($array[$key]);
			}
			else {
				$array[$key] = iconv("UTF-8", "windows-1251", $value);
			}
		}
		return $array;
	}


	/**
	 * Converts text to UTF-8 for request
	 *
	 * @param $str
	 * @return false|string
	 */
	public static function prepareRequestText($str)
	{
		if( LANG_CHARSET != "UTF-8" ) {
			$str = iconv("windows-1251", "UTF-8", $str);
		}
		return $str;
	}


	/**
	 * Converts text to UTF-8 in an array recursively for request
	 *
	 * @param $array
	 * @return array
	 */
	public static function prepareRequestRecursive($array)
	{
		if( LANG_CHARSET == "UTF-8" ) {
			return $array;
		}
		foreach( $array as $key => $value ) {
			if( is_array($value) ) {
				$array[$key] = self::prepareRequestRecursive($array[$key]);
			}
			else {
				$array[$key] = iconv("windows-1251", "UTF-8", $value);
			}
		}
		return $array;
	}


	/**
	 * Creates a JSON string from an array
	 *
	 * @param $ar
	 * @return string
	 */
	public static function jsonEncode($ar)
	{
		$ar = self::prepareRequestRecursive($ar);
		return Json::encode((object)$ar);
	}


	/**
	 * Get all headers of an incoming request
	 *
	 * @return array
	 */
	public static function getallheaders()
	{
		$headers = [];
		foreach( $_SERVER as $name => $value ) {
			$name = str_replace("REDIRECT_", "", $name);
			if( substr($name, 0, 5) == 'HTTP_' ) {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
			else {
				if( $name == "CONTENT_TYPE" ) {
					$headers["Content-Type"] = $value;
				}
				else {
					if( $name == "CONTENT_LENGTH" ) {
						$headers["Content-Length"] = $value;
					}
					else {
						$headers[$name] = $value;
					}
				}
			}
		}
		return $headers;
	}


	/**
	 * Adds seconds to the time if there are none
	 *
	 * @param $time
	 * @return string
	 */
	public static function timeFix($time)
	{
		return $time = substr($time, 0, 22) . ":00";
	}


	/**
	 * Cleans temporary files
	 */
	public static function clearOldFiles()
	{
		$root = str_replace("/bitrix/modules/" . self::$moduleID . "/lib", "", __DIR__);
		$dir = $root . "/upload/tmp/" . self::$moduleID;
		if( $d = @opendir($dir) ) {
			while( ($file = readdir($d)) !== false ) {
				if( $file != "." && $file != ".." ) {
					$ftime = filemtime($dir . '/' . $file);
					if( time() - $ftime > (Option::get(self::$moduleID, "keep_temp_files_days") * 86400) ) {
						unlink($dir . '/' . $file);
					}
				}
			}
			closedir($d);
		}
	}


	/**
	 * Validation of JSON format
	 *
	 * @param $string
	 * @return bool
	 */
	public static function isJson($string)
	{
		@json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
		//return json_validate($string);  // from php 8.3
	}


	/**
	 * Gets the language message values ​​for all languages
	 *
	 * @param $file
	 * @param $key
	 * @return array|bool
	 */
	public static function getMessFromAllLangFiles($file, $key)
	{
		$arLanguages = [];
		$rsLanguage = \CLanguage::GetList($by, $order, []);
		while( $arLanguage = $rsLanguage->Fetch() )
			$arLanguages[] = $arLanguage["LID"];
		$arMess = [];

		$filepath = rtrim(preg_replace("'[\\\\/]+'", "/", $file), "/ ");
		$module_path = "/modules/";
		if( strpos($filepath, $module_path) !== false ) {
			$pos = strlen($filepath) - strpos(strrev($filepath), strrev($module_path));
			$rel_path = substr($filepath, $pos);
			$p = strpos($rel_path, "/");
			if( !$p ) {
				return false;
			}

			$module_name = substr($rel_path, 0, $p);
			$rel_path = substr($rel_path, $p + 1);
			$BX_DOC_ROOT = rtrim(preg_replace("'[\\\\/]+'", "/", $_SERVER["DOCUMENT_ROOT"]), "/ ");
			$module_path = $BX_DOC_ROOT . getLocalPath($module_path . $module_name);
		}
		else {
			return false;
		}

		foreach( $arLanguages as $lang ) {

			unset($MESS);
			$fname = $module_path . "/lang/" . $lang . "/" . $rel_path;
			$fname = \Bitrix\Main\Localization\Translation::convertLangPath($fname, $lang);
			if( file_exists($fname) ) {
				include($fname);
				$arMess[$lang] = $MESS[$key];
			}

		}
		return $arMess;
	}


	/**
	 * Checks whether the IP address of the request matches the IP address of the server
	 */
	public static function checkIP()
	{
		$host = gethostname();
		$ip = gethostbyname($host);
		if( $_SERVER['REMOTE_ADDR'] != $ip ) {
			die();
		}
	}

}