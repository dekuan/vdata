<?php

namespace dekuan\vdata;

use dekuan\vdata\CConst;
use dekuan\delib\CLib;
use Symfony\Component\HttpFoundation\Response;


/**
 *	CResponse
 */
class CResponse extends CVData
{
	protected static $g_cStaticResponseInstance;


	public function __construct()
	{
		parent::__construct();
	}
	public function __destruct()
	{
	}
	static function GetInstance()
	{
		if ( is_null( self::$g_cStaticResponseInstance ) || ! isset( self::$g_cStaticResponseInstance ) )
		{
			self::$g_cStaticResponseInstance = new self();
		}
		return self::$g_cStaticResponseInstance;
	}


	//
	//	send HTTP response in json encoded string of virtual data format
	//
	public function Send
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



}