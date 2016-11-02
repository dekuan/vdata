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





/**
 * Created by PhpStorm.
 * User: xing
 * Date: 02/11/2016
 * Time: 12:09 PM
 */
class TestRequestRaw extends PHPUnit_Framework_TestCase
{
	public function testRawRequest()
	{
		$cRequest	= CRequest::GetInstance();

		echo "\r\n";

		$nCall		= $cRequest->HttpRaw
		(
			[
				'method'	=> 'GET',
				'url'		=> "http://127.0.0.1:9916",
			],
			$arrResp
		);
		if ( CConst::ERROR_SUCCESS == $nCall &&
			$cRequest->IsValidRawResponse( $arrResp ) )
		{
			echo "HTTP Status: " . $arrResp[ 'status' ] . "\r\n";
			print_r( $arrResp[ 'headers' ] );

			var_dump( $arrResp[ 'data' ] );
		}

	}
}
