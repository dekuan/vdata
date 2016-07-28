<?php

namespace dekuan\vdata;

use dekuan\vdata\CConst;
use dekuan\delib\CLib;


//
//	CRequest
//
class CRequest extends CVData
{
	//	statics instance
	protected static $g_cStaticRequestInstance;

	//	constants
	const HTTP_X_FORWARDED_FOR			= 'X-Forwarded-For';
	const HTTP_VDATA_FORWARDED_FOR			= 'VDATA-Forwarded-For';

	const DEFAULT_TIMEOUT				= 30;
	const DEFAULT_VERSION				= '1.0';


	//	errors
	const ERROR_SUCCESS				= CConst::ERROR_SUCCESS;
	const ERROR_NETWORK_CURL_NOT_SUPPORTED		= CConst::ERROR_USER_START + 1;
	const ERROR_NETWORK_CURL_INIT			= CConst::ERROR_USER_START + 2;
	const ERROR_NETWORK_PARAMETER			= CConst::ERROR_USER_START + 3;
	const ERROR_NETWORK_REQUEST_METHOD		= CConst::ERROR_USER_START + 4;
	const ERROR_NETWORK_HTTP_STATUS			= CConst::ERROR_USER_START + 5;

	const ERROR_INVALID_HTTP_HEADER			= CConst::ERROR_USER_START + 100;
	const ERROR_INVALID_SERVER_ADDR_EMPTY		= CConst::ERROR_USER_START + 110;
	const ERROR_INVALID_SERVER_ADDR_UNDEFINED	= CConst::ERROR_USER_START + 111;
	const ERROR_INVALID_REMOTE_ADDR_EMPTY		= CConst::ERROR_USER_START + 111;


	//
	//	configurations
	//
	private $m_arrMethods	= [ 'GET', 'POST', 'PUT', 'DELETE' ];
	private $m_arrHeaders	= [];


	public function __construct()
	{
		parent::__construct();
	}
	public function __destruct()
	{
	}
	static function GetInstance()
	{
		if ( is_null( self::$g_cStaticRequestInstance ) || ! isset( self::$g_cStaticRequestInstance ) )
		{
			self::$g_cStaticRequestInstance = new self();
		}
		return self::$g_cStaticRequestInstance;
	}


	public function Get( $arrParam )
	{
		if ( ! is_array( $arrParam ) || 0 == count( $arrParam ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		$arrParam[ 'method' ]	= 'GET';
		return $this->Http( $arrParam );
	}

	public function Post( $arrParam )
	{
		if ( ! is_array( $arrParam ) || 0 == count( $arrParam ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		$arrParam[ 'method' ]	= 'POST';
		return $this->Http( $arrParam );
	}

	public function Http( $arrParam )
	{
		//
		//	'response'	: function,	function( nErrorId, sErrorDesc, arrVData, arrJson )
		//
		//
		if ( ! is_array( $arrParam ) || 0 == count( $arrParam ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//
		//	original response
		//
		$pfnOriginResponse = array_key_exists( 'response', $arrParam ) ? $arrParam[ 'response' ] : null;


		//
		//	response in VDATA format
		//
		$arrParam[ 'response' ] =
			function( $sData, $nStatus, $arrHeaders )
			use ( $pfnOriginResponse )
		{
			$nErrorId	= CConst::ERROR_UNKNOWN;
			$sErrorDesc	= '';
			$arrVData	= [];
			$sVersion	= '';
			$arrJson	= [];

			if ( 200 == $nStatus &&
				is_string( $sData ) && strlen( $sData ) > 0 )
			{
				$arrJson = @ json_decode( $sData, true );
				if ( $this->IsValidVDataJson( $arrJson ) )
				{
					$nErrorId	= $arrJson[ 'errorid' ];
					$sErrorDesc	= $arrJson[ 'errordesc' ];
					$arrVData	= $arrJson[ 'vdata' ];
					$sVersion	= CLib::GetValEx( $arrJson, 'version', CLib::VARTYPE_STRING, '' );
				}
				else
				{
					$nErrorId = CConst::ERROR_JSON;
				}
			}
			else
			{
				$nErrorId = CConst::ERROR_NETWORK;
			}

			if ( is_callable( $pfnOriginResponse ) )
			{
				$pfnOriginResponse();
			}
		};

		return $this->HttpRaw( $arrParam );
	}

	public function HttpRaw( $arrParam )
	{
		//
		//	arrParam	- Array
		//
		//	'method'	: string,	'GET', 'POST'
		//	'url'		: string,	url
		//	'gets'		: string/array,	appended as GET parameters
		//	'posts'		: string/array,	POST data
		//	'version'	: string,	service version required by client
		//	'timeout'	: int,		timeout in seconds
		//	'cookie'	: string/array,	cookies in string or array
		//	'headers'	: array,	HTTP request header list, like this:
		//					name1: value1
		//					name2: value2
		//	'response'	: function,	function( sData, nStatus, arrHeaders )
		//
		//	RETURN		- error id
		//
		if ( ! is_array( $arrParam ) || 0 == count( $arrParam ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet = CConst::ERROR_UNKNOWN;

		//	...
		$sMethod	= CLib::GetValEx( $arrParam, 'method', CLib::VARTYPE_STRING, '' );
		$sMethod	= $this->_IsValidMethod( $sMethod ) ? $sMethod : $this->_GetDefaultMethod();

		$sUrl		= CLib::GetValEx( $arrParam, 'url', CLib::VARTYPE_STRING, '' );
		$nTimeout	= CLib::GetValEx( $arrParam, 'timeout', CLib::VARTYPE_NUMERIC, self::DEFAULT_TIMEOUT );
		$arrCookie	= array_key_exists( 'cookie', $arrParam ) ? $arrParam[ 'cookie' ] : '';
		$sVersion	= CLib::GetValEx( $arrParam, 'version', CLib::VARTYPE_STRING, self::DEFAULT_VERSION );

		$arrData	=
		[
			'method'	=> $sMethod,
			'url'		=> $sUrl,
			'param'		=> $arrParam,
			'cookie'	=> $arrCookie,
			'version'	=> $sVersion,
		];
		$sResponse	= '';
		$nHttpStatus	= 0;

		//
		//	send http request
		//
		$nRet = $this->_HttpSendRequest( $arrData, $nTimeout, $sResponse, $nHttpStatus );
		if ( array_key_exists( 'response', $arrParam ) &&
			is_callable( $arrParam[ 'response' ] ) )
		{
			$arrParam[ 'response' ]( $sResponse, $nHttpStatus, [] );
		}

		return $nRet;
	}



	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	private function _ResetHeaders()
	{
		$this->m_arrHeaders	= [];
	}
	private function _AppendHeader( $sName, $sValue )
	{
		if ( ! CLib::IsExistingString( $sName, true ) )
		{
			return false;
		}

		$this->m_arrHeaders[ $sName ] = $sValue;
		return true;
	}
	private function _GetHeadersList()
	{
		$arrRet	= [];

		if ( is_array( $this->m_arrHeaders ) && count( $this->m_arrHeaders ) > 0 )
		{
			foreach ( $this->m_arrHeaders as $sName => $sValue )
			{
				if ( CLib::IsExistingString( $sName ) )
				{
					$arrRet[] = sprintf( "%s: %s", $sName, $sValue );
				}
			}
		}

		return $arrRet;
	}

	private function _IsValidCUrlHandle( $oCUrl )
	{
		return ( isset( $oCUrl ) && false !== $oCUrl && is_resource( $oCUrl ) );
	}

	private function _HttpSendRequest( $arrRequest, $nTimeout = 5, & $sResponse = null, & $nHttpStatus = 0 )
	{
		if ( ! function_exists( 'curl_init' ) )
		{
			return self::ERROR_NETWORK_CURL_NOT_SUPPORTED;
		}
		if ( ! CLib::IsArrayWithKeys( $arrRequest ) )
		{
			return CConst::ERROR_PARAMETER;
		}
		if ( ! is_numeric( $nTimeout ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$sMethod	= array_key_exists( 'method', $arrRequest ) ? $arrRequest['method'] : '';
		$sUrl		= array_key_exists( 'url', $arrRequest ) ? $arrRequest['url'] : '';
		$arrParam	= array_key_exists( 'param', $arrRequest ) ? $arrRequest['param'] : '';
		$arrCookie	= array_key_exists( 'cookie', $arrRequest ) ? $arrRequest['cookie'] : '';
		$sVersion	= array_key_exists( 'version', $arrRequest ) ? $arrRequest['version'] : '';

		if ( ! $this->_IsValidMethod( $sMethod ) )
		{
			return self::ERROR_NETWORK_REQUEST_METHOD;
		}
		if ( ! CLib::IsExistingString( $sUrl ) )
		{
			return CConst::ERROR_PARAMETER;
		}

		//	...
		$nRet		= CConst::ERROR_UNKNOWN;
		$sDataString	= '';
		$sContentType	= '';

		//	...
		$oCUrl		= curl_init();
		$this->_ResetHeaders();

		if ( $this->_IsValidCUrlHandle( $oCUrl ) )
		{
			if ( false !== stripos( $sUrl, "https://" ) )
			{
				//
				//	set options for https request
				//

				//	FALSE to stop cURL from verifying the peer's certificate.
				curl_setopt( $oCUrl, CURLOPT_SSL_VERIFYPEER, false );

				//
				//	1	- to check the existence of a common name in the SSL peer certificate.
				//	2	- to check the existence of a common name and also verify that
				//		  it matches the hostname provided.
				//	In production environments the value of this option
				//	should be kept at 2 (default value).
				//
				curl_setopt( $oCUrl, CURLOPT_SSL_VERIFYHOST, 2 );
			}

			//
			//	build data string / parameter
			//
			if ( is_array( $arrParam ) && count( $arrParam ) > 0 )
			{
				//
				//	set enc_type to PHP_QUERY_RFC3986,
				//	spaces will be percent encoded (%20).
				//
				$sDataString = http_build_query( $arrParam, '', '&', PHP_QUERY_RFC3986 );
			}
			else if ( is_string( $arrParam ) && strlen( $arrParam ) > 0 )
			{
				$sDataString	= $arrParam;
				$sContentType	= 'text/xml';
			}

			if ( is_array( $arrCookie ) && count( $arrCookie ) > 0 )
			{
				//
				//	The contents of the "Cookie: " header to be used in the HTTP request.
				//		Note that multiple cookies are separated with a semicolon followed by
				//		a space (e.g., "fruit=apple; colour=red")
				//
				$sCookieString = http_build_query( $arrCookie, '', '; ', PHP_QUERY_RFC3986 );
				curl_setopt( $oCUrl, CURLOPT_COOKIE, $sCookieString );
			}
			else if ( is_string( $arrCookie ) && strlen( $arrCookie ) > 0 )
			{
				curl_setopt( $oCUrl, CURLOPT_COOKIE, $arrCookie );
			}

			if ( is_string( $sVersion ) && strlen( $sVersion ) > 0 )
			{
				$sVersion	= str_replace( '+', '', trim( $sVersion ) );
				$this->_AppendHeader( 'Accept', sprintf( "application/xs.cn+json+version:%s", $sVersion ) );
			}


			//
			//	set options by method
			//
			$this->_SetRequestOptByMethod( $oCUrl, $sMethod, $sUrl, $sDataString, $sContentType );

			//
			//	set proxy information
			//
			$this->_MakeRequestOptHeaderHttpXForwardedFor( $oCUrl );

			//	return the transfer as a string instead of outputting it out directly.
			curl_setopt( $oCUrl, CURLOPT_RETURNTRANSFER, true );

			//	set timeout
			curl_setopt( $oCUrl, CURLOPT_TIMEOUT, $nTimeout );

			//	return html body while HTTP Status 500
			curl_setopt( $oCUrl, CURLOPT_FAILONERROR, false );
			curl_setopt( $oCUrl, CURLOPT_HTTP200ALIASES, [ 500 ] );

			//
			//	set http headers
			//
			$arrHttpHeader	= $this->_GetHeadersList();
			if ( CLib::IsArrayWithKeys( $arrHttpHeader ) )
			{
				curl_setopt( $oCUrl, CURLOPT_HTTPHEADER, $arrHttpHeader );
			}


			//
			//	send request and set return buffer
			//
			$sResponse	= curl_exec( $oCUrl );
			$arrStatus	= curl_getinfo( $oCUrl );

			//	close curl
			curl_close( $oCUrl );
			$oCUrl = null;

			//	...
			if ( CLib::IsArrayWithKeys( $arrStatus, 'http_code' ) )
			{
				//	...
				$nHttpStatus = intval( $arrStatus[ 'http_code' ] );

				if ( 200 == $nHttpStatus )
				{
					//	successfully
					$nRet = CConst::ERROR_SUCCESS;
				}
				else
				{
					$nRet = self::ERROR_NETWORK_HTTP_STATUS;
				}
			}
			else
			{
				$nRet = self::ERROR_NETWORK_HTTP_STATUS;
			}
		}
		else
		{
			$nRet = self::ERROR_NETWORK_CURL_INIT;
		}

		//	...
		return $nRet;
	}

	private function _IsValidMethod( $sMethod )
	{
		if ( empty( $sMethod ) )
		{
			return false;
		}

		$sMethod = strtoupper( $sMethod );
		return in_array( $sMethod, $this->m_arrMethods );
	}

	private function _GetDefaultMethod()
	{
		return $this->m_arrMethods[ 0 ];
	}

	//
	//	@ Private
	//	set options for the request by method
	//
	private function _SetRequestOptByMethod( $oCUrl, $sMethod, $sUrl, $sDataString, $sContentType = '' )
	{
		if ( ! $this->_IsValidCUrlHandle( $oCUrl ) || ! is_string( $sUrl ) || ! is_string( $sMethod ) )
		{
			return false;
		}

		//	...
		$bRet		= false;
		$sReqUrl	= $sUrl;

		//	...
		if ( 0 == strcasecmp( 'GET', $sMethod ) )
		{
			//
			//	append sDataString to the end of the url if sDataString exists
			//
			if ( CLib::IsExistingString( $sDataString ) )
			{
				$sReqUrl .= sprintf( "%s%s", ( strchr( $sUrl, '?' ) ? '&' : '?' ), $sDataString );
			}
			$bRet = $this->_SetRequestOptForGet( $oCUrl, $sContentType );
		}
		else if ( 0 == strcasecmp( 'POST', $sMethod ) )
		{
			$bRet = $this->_SetRequestOptForPost( $oCUrl, $sDataString, $sContentType );
		}
		else if ( 0 == strcasecmp( 'PUT', $sMethod ) )
		{
			$bRet = $this->_SetRequestOptForPut( $oCUrl, $sDataString, $sContentType );
		}
		else if ( 0 == strcasecmp( 'DELETE', $sMethod ) )
		{
			$bRet = $this->_SetRequestOptForDelete( $oCUrl, $sDataString, $sContentType );
		}

		//	set url
		curl_setopt( $oCUrl, CURLOPT_URL, $sReqUrl );

		//	...
		return $bRet;
	}
	private function _SetRequestOptForGet( $oCUrl, $sContentType = '' )
	{
		if ( ! $this->_IsValidCUrlHandle( $oCUrl ) )
		{
			return false;
		}

		//	...
		curl_setopt( $oCUrl, CURLOPT_CUSTOMREQUEST, 'GET' );
		if ( strlen( $sContentType ) > 0 )
		{
			$this->_AppendHeader( 'Content-Type', $sContentType );
		}

		return true;
	}
	private function _SetRequestOptForPost( $oCUrl, $sDataString, $sContentType = '' )
	{
		if ( ! $this->_IsValidCUrlHandle( $oCUrl ) || ! is_string( $sDataString ) )
		{
			return false;
		}

		//	...
		//	curl_setopt ( $oCUrl, CURLOPT_POST, true );
		//	curl_setopt ( $oCUrl, CURLOPT_POSTFIELDS, $sDataString );
		//	curl_setopt ( $oCUrl, CURLOPT_RETURNTRANSFER, 1 );

		curl_setopt( $oCUrl, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $oCUrl, CURLOPT_FAILONERROR, true );
		curl_setopt( $oCUrl, CURLOPT_POSTFIELDS, $sDataString );
		if ( strlen( $sContentType ) > 0 )
		{
			$this->_AppendHeader( 'Content-Type', $sContentType );
		}
		//	curl_setopt( $oCUrl, CURLOPT_HTTPHEADER, Array(
		//			'Content-Type: application/json',
		//			'Content-Length: ' . strlen( $sDataString ) )
		//	);

		return true;
	}
	private function _SetRequestOptForPut( $oCUrl, $sDataString, $sContentType = '' )
	{
		if ( ! $this->_IsValidCUrlHandle( $oCUrl ) || ! is_string( $sDataString ) )
		{
			return false;
		}

		//	...
		curl_setopt( $oCUrl, CURLOPT_CUSTOMREQUEST, 'PUT' );
		curl_setopt( $oCUrl, CURLOPT_FAILONERROR, true );
		curl_setopt( $oCUrl, CURLOPT_POSTFIELDS, $sDataString );
		if ( strlen( $sContentType ) > 0 )
		{
			$this->_AppendHeader( 'Content-Type', $sContentType );
		}

		return true;
	}
	private function _SetRequestOptForDelete( $oCUrl, $sDataString, $sContentType = '' )
	{
		if ( ! $this->_IsValidCUrlHandle( $oCUrl ) || ! is_string( $sDataString ) )
		{
			return false;
		}

		//	...
		curl_setopt( $oCUrl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		curl_setopt( $oCUrl, CURLOPT_FAILONERROR, true );
		curl_setopt( $oCUrl, CURLOPT_POSTFIELDS, $sDataString );
		if ( strlen( $sContentType ) > 0 )
		{
			$this->_AppendHeader( 'Content-Type', $sContentType );
		}

		return true;
	}

	private function _MakeRequestOptHeaderHttpXForwardedFor( $oCUrl, & $pnErrorId = null )
	{
		//
		//	oCUrl	- [in] the handle of CURL
		//	RETURN	- true / false
		//
		//	* About HTTP_X_FORWARDED_FOR
		//
		//		The X-Forwarded-For (XFF) HTTP header field was a common method for identifying
		//		the originating IP address of a client connecting to a web server through an HTTP proxy
		//		or load balancer.
		//
		//		The general format of the field is:
		//		X-Forwarded-For: client, proxy1, proxy2
		//
		//		Where the value is a comma+space separated list of IP addresses,
		// 		the left-most being the original client, and each successive proxy that passed the request
		// 		adding the IP address where it received the request from.
		// 		In this example, the request passed through proxy1, proxy2, and then proxy3 ( not shown in the header ).
		// 		proxy3 appears as remote address of the request.
		//
		//		Since it is easy to forge an X-Forwarded-For field the given information should be used with care.
		// 		The last IP address is always the IP address that connects to the last proxy,
		// 		which means it is the most reliable source of information.
		// 		X-Forwarded-For data can be used in a forward or reverse proxy scenario.
		//
		//		Just logging the X-Forwarded-For field is not always enough as the last proxy IP address in a chain
		// 		is not contained within the X-Forwarded-For field, it is in the actual IP header.
		//		A web server should log BOTH the request's source IP address and
		// 		the X-Forwarded-For field information for completeness.
		//

		if ( ! $this->_IsValidCUrlHandle( $oCUrl ) )
		{
			return false;
		}

		$bRet	= false;

		//	...
		$sServerAddr		= '';
		$sXForwardedFor		= '';
		$sRemoteAddr		= '';
		$sVDATAForwardedFor	= '';

		$sNewXForwardedFor	= '';
		$sNewVDATAForwardedFor	= '';

		$arrOptHttpHeader	= [];


		try
		{
			if ( is_array( $_SERVER ) )
			{
				if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) &&
					is_string( $_SERVER[ 'SERVER_ADDR' ] ) )
				{
					$sServerAddr	= trim( $_SERVER[ 'SERVER_ADDR' ], "\r\n\t, " );
					if ( strlen( $sServerAddr ) > 0 )
					{
						if ( array_key_exists( self::HTTP_X_FORWARDED_FOR, $_SERVER ) &&
							is_string( $_SERVER[ self::HTTP_X_FORWARDED_FOR ] ) )
						{
							$sXForwardedFor	= trim( $_SERVER[ self::HTTP_X_FORWARDED_FOR ], "\r\n\t, " );
						}
						if ( array_key_exists( self::HTTP_VDATA_FORWARDED_FOR, $_SERVER ) &&
							is_string( $_SERVER[ self::HTTP_VDATA_FORWARDED_FOR ] ) )
						{
							$sVDATAForwardedFor = trim( $_SERVER[ self::HTTP_VDATA_FORWARDED_FOR ], "\r\n\t, " );
						}
						if ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) &&
							is_string( $_SERVER[ 'REMOTE_ADDR' ] ) )
						{
							$sRemoteAddr	= trim( $_SERVER[ 'REMOTE_ADDR' ], "\r\n\t, " );
						}


						//
						//	HTTP_X_FORWARDED_FOR
						//
						if ( is_string( $sXForwardedFor ) && strlen( $sXForwardedFor ) > 0 &&
							is_string( $sRemoteAddr ) && strlen( $sRemoteAddr ) > 0 )
						{
							//
							//	this request was sent by a proxy
							//
							$sNewXForwardedFor	= sprintf( "%s, %s", $sXForwardedFor, $sServerAddr );
							$arrOptHttpHeader[ self::HTTP_X_FORWARDED_FOR ]	= $sNewXForwardedFor;

						}
						else if ( is_string( $sRemoteAddr ) && strlen( $sRemoteAddr ) > 0 )
						{
							//
							//	this request was sent by the original client
							//
							$sNewXForwardedFor	= sprintf( "%s, %s", $sRemoteAddr, $sServerAddr );
							$arrOptHttpHeader[ self::HTTP_X_FORWARDED_FOR ]	= $sNewXForwardedFor;
						}


						//
						//	HTTP_VDATA_FORWARDED_FOR
						//
						if ( is_string( $sVDATAForwardedFor ) && strlen( $sVDATAForwardedFor ) > 0 )
						{
							$sNewVDATAForwardedFor	= sprintf( "%s, %s", $sVDATAForwardedFor, $sServerAddr );
							$arrOptHttpHeader[ self::HTTP_VDATA_FORWARDED_FOR ]	= $sNewVDATAForwardedFor;
						}
						else if ( is_string( $sRemoteAddr ) && strlen( $sRemoteAddr ) > 0 )
						{
							$sNewVDATAForwardedFor	= sprintf( "%s, %s", $sRemoteAddr, $sServerAddr );
							$arrOptHttpHeader[ self::HTTP_VDATA_FORWARDED_FOR ]	= $sNewVDATAForwardedFor;
						}



						//
						//	try to set CURLOPT_HTTPHEADER
						//
						if ( is_array( $arrOptHttpHeader ) &&
							count( $arrOptHttpHeader ) > 0 )
						{
							//
							//	...
							//
							$bRet = true;

							//	...
							foreach ( $arrOptHttpHeader as $sName => $sValue )
							{
								$this->_AppendHeader( $sName, $sValue );
							}
						}
					}
					else
					{
						//	SERVER_ADDR is invalid or empty
						$pnErrorId = self::ERROR_INVALID_SERVER_ADDR_EMPTY;
					}
				}
				else
				{
					//	SERVER_ADDR key was not defined
					$pnErrorId = self::ERROR_INVALID_SERVER_ADDR_UNDEFINED;
				}
			}
			else
			{
				//	this request was sent without any header information
				$pnErrorId = self::ERROR_INVALID_HTTP_HEADER;
			}
		}
		catch ( \Exception $e )
		{
			throw $e;
		}

		return $bRet;
	}

}

