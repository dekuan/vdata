<?php

namespace dekuan\vdata;


/**
 * Created by PhpStorm.
 * User: xing
 * Date: 25/10/2016
 * Time: 12:02 PM
 */


/***
 *	Class testCConst
 *	@package dekuan\vdata
 */
class testCConst extends \PHPUnit\Framework\TestCase
{
	public function testSource()
	{
		//
		//      Source/Client
		//
		$arrDataList =
		[
			[ false,	CConst::SOURCE_UNKNOWN ],
			[ true,		CConst::SOURCE_ANDROID ],
			[ true,		CConst::SOURCE_IOS ],
			[ true,		CConst::SOURCE_WAP ],
			[ true,		CConst::SOURCE_PC ],
			[ true,		CConst::SOURCE_MGR_SYSTEM ],
			[ false,	-1 ],
			[ false,	null ],
			[ false,	"sfdfdfdfdfdf" ],
		];

		foreach ( $arrDataList as $nIndex => $arrItem )
		{
			$bExpect	= $arrItem[ 0 ];
			$vValue		= $arrItem[ 1 ];
			$bResult	= CConst::IsValidSource( $vValue );
			$nErrorId	= ( $bExpect === $bResult ? CConst::ERROR_SUCCESS : ( 10000 + $nIndex ) );
			$sDumpString	= sprintf( "value : %d, expect : %d, result : %d",
						intval( $vValue ), intval( $bExpect ), intval( $bResult ) );

			new CAssertResult
			(
				__CLASS__,
				__FUNCTION__,
				'IsValidSource',
				$nErrorId,
				( CConst::ERROR_SUCCESS == $nErrorId ? null : $sDumpString )
			);
		}
	}

	public function testAppType()
	{
		//
		//	App type
		//
		$arrDataList =
		[
			[ false,	CConst::APP_TYPE_UNKNOWN ],
			[ true,		CConst::APP_TYPE_APP ],
			[ true,		CConst::APP_TYPE_WEB ],
			[ false,	-1 ],
			[ false,	null ],
			[ false,	[] ],
			[ false,	"sfdfdfdfdfdf" ],
		];

		foreach ( $arrDataList as $nIndex => $arrItem )
		{
			$bExpect	= $arrItem[ 0 ];
			$vValue		= $arrItem[ 1 ];
			$bResult	= CConst::IsValidAppType( $vValue );
			$nErrorId	= ( $bExpect === $bResult ? CConst::ERROR_SUCCESS : ( 10000 + $nIndex ) );
			$sDumpString	= sprintf( "value : %d, expect : %d, result : %d",
				intval( $vValue ), intval( $bExpect ), intval( $bResult ) );

			new CAssertResult
			(
				__CLASS__,
				__FUNCTION__,
				'IsValidAppType',
				$nErrorId,
				( CConst::ERROR_SUCCESS == $nErrorId ? null : $sDumpString )
			);
		}
	}

}
