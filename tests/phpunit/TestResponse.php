<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/dekuan/delib/src/CLib.php';
require __DIR__ . '/../../src/CConst.php';
require __DIR__ . '/../../src/CCors.php';
require __DIR__ . '/../../src/CVData.php';
require __DIR__ . '/../../src/CRequest.php';
require __DIR__ . '/../../src/CResponse.php';

use dekuan\vdata\CConst;
use dekuan\vdata\CRequest;
use dekuan\vdata\CResponse;



/**
 * Created by PhpStorm.
 * User: xing
 * Date: 9/6/16
 * Time: 8:17 PM
 */
class TestResponse extends PHPUnit_Framework_TestCase
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
