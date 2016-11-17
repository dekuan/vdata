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
		$sRefHost = $this->GetRefererHost();
		if ( is_string( $sRefHost ) )
		{
			$nRefHostLen = strlen( $sRefHost );
			if ( is_array( $this->m_arrCorsDomains ) &&
				count( $this->m_arrCorsDomains ) > 0 )
			{
				foreach ( $this->m_arrCorsDomains as $sCfgDomain )
				{
					$nDmLength = strlen( $sCfgDomain );
					if ( $nRefHostLen >= $nDmLength &&
						0 == strcasecmp( $sCfgDomain, substr( $sRefHost, -1 * $nDmLength ) ) )
					{
						//
						//	ref:	www.dekuan.org
						//	cfg:	dekuan.org
						//
						$bRet = true;
						break;
					}
					else if ( $nRefHostLen + 1 == $nDmLength &&
						$nDmLength > 1 &&
						0 == strcasecmp( $sRefHost, substr( $sCfgDomain, 1 ) ) )
					{
						//
						//	ref:	dekuan.org
						//	cfg:	.dekuan.org
						//
						$bRet = true;
						break;
					}
					else if ( strstr( $sCfgDomain, '*' ) )
					{
						if ( fnmatch( $sCfgDomain, $sRefHost ) )
						{
							//
							//	ref:	msgsender.service.dekuan.org
							//	cfg:	*.service.dekuan.org
							//
							$bRet = true;
							break;
						}
					}
				}
			}
		}

		return $bRet;
	}

	public function GetRefererHost( $bWithScheme = false )
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
					array_key_exists( 'scheme', $arrUrl ) &&
					array_key_exists( 'host', $arrUrl ) )
				{
					$sRefScheme	= $arrUrl['scheme'];
					$sRefHost	= $arrUrl['host'];
					$nPort		= array_key_exists( 'port', $arrUrl ) ? intval( $arrUrl[ 'port' ] ) : 80;

					if ( is_string( $sRefScheme ) &&
						is_string( $sRefHost ) )
					{
						//
						//	...
						//
						$sRet = sprintf
						(
							"%s%s%s",
							(
								$bWithScheme ?
									sprintf( "%s://", $sRefScheme )
									:
									''
							),
							strtolower( trim( $sRefHost ) ),
							(
								( $bWithScheme && 80 != $nPort ) ?
									sprintf( ":%d", $nPort )
									:
									''
							)
						);
						$sRet = rtrim( $sRet, "/\\" );
					}
				}
			}
		}

		return $sRet;
	}
}