<?php

namespace dekuan\vdata;


/**
 *	Cross-Origin Resource Sharing
 */
class CCors
{
	//
	//	constants
	//
	const CORS_DEFAULT_DOMAIN	= '.dekuan.org';	//	default cors domain

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

	//
	//	set domains for Cross-Origin Resource Sharing
	//
	public function SetCorsDomains( $arrDomains )
	{
		//
		//	arrDomains	- [in] array,	[ '.domain1.com', 'www.domain2.com' ]
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