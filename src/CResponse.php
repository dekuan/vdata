<?php

namespace dekuan\xsnetwork;

use dekuan\deconst\CConst;
use Symfony\Component\HttpFoundation\Response;


//
//	CResponse
//
class CResponse
{
	protected static $g_cStaticInstance;

	//
	//	constants
	//
	const SERVICE_DEFAULT_VERSION	= '1.0';	//	default service version
	const CORS_DEFAULT_DOMAIN	= '.xs.cn';	//	default cors domain

	//
	//	Domains for Cross-Origin Resource Sharing
	//
	protected $m_arrCorsDomains	= [ self::CORS_DEFAULT_DOMAIN ];



	public function __construct()
	{
	}
	public function __destruct()
	{
	}
	static function GetInstance()
	{
		if ( is_null( self::$g_cStaticInstance ) || ! isset( self::$g_cStaticInstance ) )
		{
			self::$g_cStaticInstance = new self();
		}
		return self::$g_cStaticInstance;
	}


	//
	//	set domains for Cross-Origin Resource Sharing
	//
	public function SetCorsDomains( $arrDomains )
	{
		//
		//	arrDomains	- [in] array,	[ 'domain1', 'domain2' ]
		//	RETURN		- true / false
		//
		if ( ! is_array( $arrDomains ) )
		{
			return false;
		}

		//	...
		$arrDomainsNew	= [];
		foreach ( $arrDomains as $sDomain )
		{
			if ( is_string( $sDomain ) && strlen( $sDomain ) > 0 )
			{
				$sDomain = trim( $sDomain );
				if ( strlen( $sDomain ) > 0 )
				{
					$arrDomainsNew[] = strtolower( $sDomain );
				}
			}
		}

		//	...
		$this->m_arrCorsDomains = $arrDomainsNew;

		//	...
		return true;
	}

	//
	//	is allowed cors request
	//
	public function IsAllowedCorsRequest()
	{
		$bRet	= false;

		//	...
		$sRefHost = $this->_GetRefererHost();
		if ( is_string( $sRefHost ) )
		{
			$nRefHostLen = strlen( $sRefHost );
			if ( is_array( $this->m_arrCorsDomains ) &&
				count( $this->m_arrCorsDomains ) > 0 )
			{
				foreach ( $this->m_arrCorsDomains as $sDomain )
				{
					$nDmLength = strlen( $sDomain );
					if ( $nRefHostLen >= $nDmLength &&
						0 == strcasecmp( $sDomain, substr( $sRefHost, -1 * $nDmLength ) ) )
					{
						$bRet = true;
						break;
					}
					else if ( $nRefHostLen + 1 == $nDmLength &&
						$nDmLength > 1 &&
						0 == strcasecmp( $sRefHost, substr( $sDomain, 1 ) ) )
					{
						$bRet = true;
						break;
					}
				}
			}
		}

		return $bRet;
	}


	//
	//	get json in virtual data format
	//
	public function GetVDataJson( $nErrorId, $sErrorDesc = '', $arrVData = [], $sVersion = '1.0', $bCached = null )
	{
		//
		//	nErrorId	- [in] int	error id
		//	sErrorDesc	- [in] string	error description
		//	arrVData	- [in] array	virtual data
		//	sVersion	- [in] string	service version, default is '1.0'
		//	bCached		- [in] bool	if the data come from cache
		//	RETURN		- json array
		//
		$arrRet = [];

		if ( is_numeric( $nErrorId ) && is_string( $sErrorDesc ) )
		{
			//	Okay
			$arrRet =
				[
					'errorid'   => intval( $nErrorId ),
					'errordesc' => strval( $sErrorDesc ),
					'vdata'     => ( is_array( $arrVData ) ? $arrVData : [] ),
				];
		}
		else
		{
			//	invalid
			$arrRet = $this->GetDefaultVDataJson();
		}

		//
		//	tell the world/client, we are version x.x.x via:
		//	2, version node of vdata
		//
		$arrRet[ 'version' ] = ( is_string( $sVersion ) && strlen( $sVersion ) > 0 ) ? $sVersion : self::SERVICE_DEFAULT_VERSION;

		//
		//	data from cache?
		//
		if ( is_bool( $bCached ) )
		{
			$arrRet['cache'] = intval( $bCached ? 1 : 0 );
		}

		return $arrRet;
	}

	//
	//	get json encoded string in virtual data format
	//
	public function GetVDataString( $nErrorId, $sErrorDesc = '', $arrVData = [], $sVersion = '1.0', $bCached = null )
	{
		//
		//	nErrorId	- [in] int	error id
		//	sErrorDesc	- [in] string	error description
		//	arrVData	- [in] array	virtual data
		//	sVersion	- [in] string	service version, default is '1.0'
		//	bCached		- [in] bool	if the data come from cache
		//	RETURN		- encoded json string
		//
		$sRet = '';

		//	...
		$arrJson = $this->GetVDataJson( $nErrorId, $sErrorDesc, $arrVData, $sVersion, $bCached );
		if ( $this->IsValidVDataJson( $arrJson ) )
		{
			$sRet = @ json_encode( $arrJson );
		}
		else
		{
			$sRet = @ json_encode( $this->GetDefaultVDataJson() );
		}

		return $sRet;
	}

	//
	//	get response object instance contains json encoded string in virtual data format
	//
	public function GetVDataResponse
	(
		$nErrorId,
		$sErrorDesc	= '',
		$arrVData	= [],
		$sVersion	= self::SERVICE_DEFAULT_VERSION,
		$bCached	= null,
		$nHttpStatus	= Response::HTTP_OK
	)
	{
		//
		//	nErrorId	- [in] int	error id
		//	sErrorDesc	- [in] string	error description
		//	arrVData	- [in] array	virtual data
		//	sVersion	- [in] string	service version, default is '1.0'
		//	bCached		- [in] bool	if the data come from cache
		//	nHttpStatus	- [in] int	HTTP response status
		//	RETURN		- Instance of Symfony\Component\HttpFoundation\Response
		//
		$cResponse	= new Response();
		$sContentJson	= '';
		$sContentType	= '';

		if ( ! is_numeric( $nHttpStatus ) )
		{
			$nHttpStatus = Response::HTTP_OK;
		}

		//
		//	send json response to client
		//
		$sContentJson	= $this->GetVDataString( $nErrorId, $sErrorDesc, $arrVData, $sVersion, $bCached );

		//
		//	tell the world/client, we are version x.x.x via Content-Type of HTTP header
		//
		$sContentType	= $this->GetContentTypeWithVersion( $sVersion );

		//
		//	send response to client now
		//
		$cResponse->setContent( $sContentJson );
		$cResponse->setStatusCode( $nHttpStatus );
		$cResponse->headers->set( 'Content-Type', $sContentType );

		if ( $this->IsAllowedCorsRequest() )
		{
			//
			//	this request is allowed via Cross-Origin Resource Sharing
			//
			$cResponse->headers->set( 'Access-Control-Allow-Origin', '*' );
			//$cResponse->headers->set( 'Access-Control-Allow-Headers', '*' );
			//$cResponse->headers->set( 'Access-Control-Max-Age', 60 );
			//$cResponse->headers->set( 'Access-Control-Allow-Methods', 'POST' );
		}

		//
		//	prints the HTTP headers followed by the content
		//
		return $cResponse;
	}

	//
	//	send HTTP response in json encoded string of virtual data format
	//
	public function SendVDataResponse
	(
		$nErrorId,
		$sErrorDesc	= '',
		$arrVData	= [],
		$sVersion	= self::SERVICE_DEFAULT_VERSION,
		$bCached	= null,
		$nHttpStatus	= Response::HTTP_OK
	)
	{
		//
		//	nErrorId	- [in] int	error id
		//	sErrorDesc	- [in] string	error description
		//	arrVData	- [in] array	virtual data
		//	sVersion	- [in] string	service version, default is '1.0'
		//	bCached		- [in] bool	if the data come from cache
		//	nHttpStatus	- [in] int	HTTP response status
		//	RETURN		- error code
		//
		$nRet = CConst::ERROR_UNKNOWN;

		//	...
		$cResponse = $this->GetVDataResponse( $nErrorId, $sErrorDesc, $arrVData, $sVersion, $bCached, $nHttpStatus );
		if ( $cResponse instanceof Response )
		{
			$cResponse->send();
			$nRet = CConst::ERROR_SUCCESS;
		}

		return $nRet;
	}

	//
	//	if the variable being evaluated is a valid json in virtual data format ?
	//
	public function IsValidVDataJson( $arrJson )
	{
		return ( is_array( $arrJson ) &&
			array_key_exists( 'errorid', $arrJson ) &&
			array_key_exists( 'errordesc', $arrJson ) &&
			array_key_exists( 'vdata', $arrJson ) &&
			is_numeric( $arrJson[ 'errorid' ] ) &&
			is_string( $arrJson[ 'errordesc' ] ) &&
			is_array( $arrJson[ 'vdata' ] ) );
	}

	//
	//	get default json in virtual data format
	//
	public function GetDefaultVDataJson()
	{
		return [
			'errorid'   => intval( CConst::ERROR_UNKNOWN ),
			'errordesc' => '',
			'vdata'     => []
		];
	}

	//
	//	get content type of HTTP header
	//
	public function GetContentTypeWithVersion( $sVersion )
	{
		return sprintf
		(
			"application/json; version:%s",
			( is_string( $sVersion ) && strlen( $sVersion ) > 0 ) ? $sVersion : self::SERVICE_DEFAULT_VERSION
		);
	}


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//
	protected function _GetRefererHost()
	{
		$sRet	= '';

		//	...
		if ( is_array( $_SERVER ) &&
			array_key_exists( 'HTTP_REFERER', $_SERVER ) )
		{
			$sReferer = $_SERVER[ 'HTTP_REFERER' ];
			if ( is_string( $sReferer ) &&
				strlen( $sReferer ) > 0 )
			{
				$arrUrl	= @ parse_url( $sReferer );
				if ( is_array( $arrUrl ) &&
					array_key_exists( 'host', $arrUrl ) )
				{
					$sRefHost = $arrUrl['host'];
					if ( is_string( $sRefHost ) )
					{
						//
						//	...
						//
						$sRet = strtolower( trim( $sRefHost ) );
					}
				}
			}
		}

		return $sRet;
	}
}