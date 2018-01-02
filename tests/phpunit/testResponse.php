<?php

namespace dekuan\vdata;



use dekuan\vdata\CConst;
use dekuan\vdata\CRequest;
use dekuan\vdata\CResponse;


/***
 *	Class testResponse
 *	@package dekuan\vdata
 */
class testResponse extends \PHPUnit\Framework\TestCase
{

	public function testVDataArray()
	{
		$cResponse	= CResponse::GetInstance();

		$cResponse->SetServiceName( 'Test of responding array VData' );
		$cResponse->SetServiceUrl( 'http://www.ladep.cn/' );
		$arrVData	= $cResponse->GetVDataArray
		(
			0,
			"error desc",
			[ 'love' => true, 'hug' => true ],
			$cResponse->GetDefaultVersion(),
			null,
			[ 'result' => 1, 'parents' => 'ppp', 'name' => 'SB', 'doaction' => 'come_on' ]
		);

		print_r( $arrVData );
	}

	public function testVDataString()
	{
		$cResponse	= CResponse::GetInstance();

		$cResponse->SetServiceName( 'Test of responding string VData' );
		$cResponse->SetServiceUrl( 'http://www.ladep.cn/' );
		$sJson	= $cResponse->GetVDataString
		(
			0,
			"error desc",
			[ 'love' => true, 'hug' => true ],
			$cResponse->GetDefaultVersion(),
			null,
			[ 'result' => 1, 'parents' => 'ppp', 'name' => 'SB', 'doaction' => 'come_on' ]
		);

		echo "\r\n------------------------------------------------------------\r\n";
		echo "JSON STRING : \r\n";
		echo $sJson;
		echo "\r\n------------------------------------------------------------\r\n";
	}
}
