<?php

namespace dekuan\vdata;

use dekuan\vdata\CConst;
use dekuan\delib\CLib;


/**
 *      CRemote
 */
class CRemote
{
	//
	//      common status
	//
	const PARAM_SOURCE			= "sr";
	const PARAM_APP_ID			= "apid";		//	app id of client/caller
	const PARAM_APP_NAME			= "apnm";		//	app name of client/caller
	const PARAM_APP_VERSION			= "aver";		//	软件版本号
	const PARAM_APP_CHANNEL			= "apc";		//	app的渠道
	const PARAM_SYSTEM_VERSION		= "sver";		//	手机系统版本号
	const PARAM_PHONE_MODEL			= "pmod";		//	手机型号（例如iPhone5s）
	const PARAM_SCREEN_RESOLUTION		= "reso";		//	屏幕分辨率（例如1136x640）
	const PARAM_MOBILE_OPERATOR		= "mobop";		//	运营商（CT:中国电信， CMCC:中国移动， CU:中国联通）
	const PARAM_IMEI			= "im";			//	移动设备国际识别码
	const PARAM_NET_STATUS			= "nstat";		//	网络状态(2G, 3G, 4G, WiFi)
	const PARAM_MAC_ADDRESS			= "mad";		//	mac地址
	const PARAM_SYSTEM_LANGUAGE		= "sla";		//	系统语言
	const PARAM_SYSTEM_TIMEZONE		= "stz";		//	系统时区，单位为：分钟


	//
	//	tools
	//
	static function GetAcceptedVersionEx()
	{
		$sRet = '';

		if ( is_array( $_SERVER ) &&
			array_key_exists( 'HTTP_ACCEPT', $_SERVER ) )
		{
			$sServiceVersion = self::GetAcceptedVersion( [ $_SERVER[ 'HTTP_ACCEPT' ] ] );
			if ( is_string( $sServiceVersion ) &&
				strlen( $sServiceVersion ) > 0 )
			{
				$sRet = $sServiceVersion;
			}
		}

		return $sRet;
	}
	static function GetAcceptedVersion( $arrAcceptList )
	{
		//
		//	arrAcceptList	- Request::getAcceptableContentTypes() in laravel
		//	RETURN		- accepted version of service by client/caller
		//
		if ( ! $arrAcceptList || ! is_array( $arrAcceptList ) || 0 == count( $arrAcceptList ) )
		{
			return '';
		}

		//	Accept: application/dekuan+json+version:%s
		$sRet		= '';
		$sAccept	= '';
		foreach ( $arrAcceptList as $sItem )
		{
			if ( is_string( $sItem ) &&
				strlen( $sItem ) > 0 &&
				strstr( $sItem, CConst::HTTP_HEADER_VERSION_ACCEPT ) )
			{
				$sAccept = $sItem;
				break;
			}
		}

		if ( strlen( $sAccept ) > 0 )
		{
			$sNeedle	= '+version:';
			$nPos		= strpos( $sAccept, $sNeedle );
			if ( FALSE !== $nPos && is_numeric( $nPos ) && $nPos >= 0 )
			{
				$sVerNumber = substr( $sAccept, $nPos + strlen( $sNeedle ) );
				if ( strlen( $sVerNumber ) > 0 )
				{
					$sRet = trim( $sVerNumber, "\r\n\t " );
				}
			}
		}

		return $sRet;
	}


	//
	//	public parameters
	//
	static function GetSource( $arrInput )
	{
		//
		//	arrInput	- Input::get()
		//
		$nSource = self::GetParam( $arrInput, self::PARAM_SOURCE );
		if ( ! CConst::IsValidSource( $nSource ) )
		{
			$nSource = CConst::SOURCE_UNKNOWN;
		}
		return $nSource;
	}

	static function GetAppID( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_APP_ID );
	}
	static function GetAppName( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_APP_NAME );
	}
	static function GetAppVersion( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_APP_VERSION );
	}
	static function GetAppChannel( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_APP_CHANNEL );
	}
	static function GetSystemVersion( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_SYSTEM_VERSION );
	}
	static function GetPhoneModel( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_PHONE_MODEL );
	}
	static function GetScreenResolution( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_SCREEN_RESOLUTION );
	}
	static function GetMobileOperator( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_MOBILE_OPERATOR );
	}
	static function GetIMEI( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_IMEI );
	}
	static function GetNetStatus( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_NET_STATUS );
	}
	static function GetMacAddress( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_MAC_ADDRESS );
	}
	static function GetSystemLanguage( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_SYSTEM_LANGUAGE );
	}
	static function GetSystemTimezone( $arrInput )
	{
		return self::GetParam( $arrInput, self::PARAM_SYSTEM_TIMEZONE );
	}

	//
	static function GetParamAll( $arrInput )
	{
		return
		[
			self::PARAM_SOURCE		=> self::GetSource( $arrInput ),
			self::PARAM_APP_ID		=> self::GetAppID( $arrInput ),
			self::PARAM_APP_NAME		=> self::GetAppName( $arrInput ),
			self::PARAM_APP_VERSION		=> self::GetAppVersion( $arrInput ),
			self::PARAM_APP_CHANNEL		=> self::GetAppChannel( $arrInput ),
			self::PARAM_SYSTEM_VERSION	=> self::GetSystemVersion( $arrInput ),
			self::PARAM_PHONE_MODEL		=> self::GetPhoneModel( $arrInput ),
			self::PARAM_SCREEN_RESOLUTION	=> self::GetScreenResolution( $arrInput ),
			self::PARAM_MOBILE_OPERATOR	=> self::GetMobileOperator( $arrInput ),
			self::PARAM_IMEI		=> self::GetIMEI( $arrInput ),
			self::PARAM_NET_STATUS		=> self::GetNetStatus( $arrInput ),
			self::PARAM_MAC_ADDRESS		=> self::GetMacAddress( $arrInput ),
			self::PARAM_SYSTEM_LANGUAGE	=> self::GetSystemLanguage( $arrInput ),
			self::PARAM_SYSTEM_TIMEZONE	=> self::GetSystemTimezone( $arrInput ),
		];
	}


	//	...
	static function GetParam( $arrInput, $sKey, $vDefault = '' )
	{
		if ( ! CLib::IsExistingString( $sKey, true ) )
		{
			return $vDefault;
		}

		//	...
		$vRet	= $vDefault;
		$sKey	= trim( $sKey );

		//	...
		if ( array_key_exists( $sKey, $_GET ) )
		{
			$vRet = $_GET[ $sKey ];
		}
		else if ( array_key_exists( $sKey, $_POST ) )
		{
			$vRet = $_POST[ $sKey ];
		}
		else if ( is_array( $arrInput ) && array_key_exists( $sKey, $arrInput ) )
		{
			$vRet = $arrInput[ $sKey ];
		}

		return $vRet;
	}
}

?>