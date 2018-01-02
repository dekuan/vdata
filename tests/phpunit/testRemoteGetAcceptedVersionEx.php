<?php

namespace dekuan\vdata;


/***
 *	Class testRemoteGetAcceptedVersionEx
 *	@package dekuan\vdata
 */
class testRemoteGetAcceptedVersionEx extends \PHPUnit\Framework\TestCase
{
	public function testNow()
	{
		$arrTestData	=
			[
				[ true,		'1.0',	'application/vdata+json+version:1.0' ],
				[ true,		'1.1',	'application/vdata+json+version:1.1' ],
				[ true,		'',	'application/vdata+json+version:' ],
				[ true,		'',	'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' ],
				[ true,		'',	'' ],
				[ true,		'',	[] ],
				[ true,		'',	null ],

			];
		if ( ! is_array( $_SERVER ) )
		{
			$_SERVER = [];
		}

		foreach ( $arrTestData as $arrItem )
		{
			$bGoal	= $arrItem[ 0 ];
			$sVer	= $arrItem[ 1 ];
			$vVal	= $arrItem[ 2 ];

			//	...
			$_SERVER[ 'HTTP_ACCEPT' ]	= $vVal;

			$sGetVer	= CRemote::GetAcceptedVersionEx();
			$bResult	= ( 0 == strcasecmp( $sVer, $sGetVer ) );
			$bSuccess	= ( $bGoal == $bResult );

			$sTitle		= sprintf
			(
				"GetAcceptedVersionEx\tresult(%s) - goal(%s)\tval(%s)",
				$sGetVer,
				$sVer,
				( is_string( $vVal ) || is_numeric( $vVal ) ) ? $vVal : "NULL"
			);

			$this->_OutputResult( __FUNCTION__, $sTitle, -1, $bSuccess );
		}
	}



	protected function _OutputResult( $sFuncName, $sCallMethod, $nErrorId, $bAssert )
	{
		printf( "\r\n# %s->%s\r\n", $sFuncName, $sCallMethod );
		printf( "# ErrorId : %6d, result : [%s]", $nErrorId, ( $bAssert ? "OK" : "ERROR" ) );
		printf( "\r\n" );

		$this->assertTrue( $bAssert );
	}
}