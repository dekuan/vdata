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
 * User: liuqixing
 * Date: 7/29/16
 * Time: 5:17 PM
 */
class TestRequest extends PHPUnit_Framework_TestCase
{
	public function testLoginViaHttpPost()
	{
		//
		//	https://account.xs.cn/api/login
		//
		//	u_name		13810550569
		//	u_pwd		qqqqqqqq
		//	u_keep		1
		//	u_ctype		0
		//

		$cRequest	= CRequest::GetInstance();
		$arrResponse	= [];
		$nCall		= $cRequest->Post
		(
			[
				'url'		=> 'http://account.xs.cn/api/login',
				'data'		=>
					[
						'u_name'	=> '13810550569',
						'u_pwd'		=> 'qqqqqqqq',
						'u_keep'	=> 1,
						'u_ctype'	=> 0,
					],
				'version'	=> '1.0',
				'timeout'	=> 30,		//	timeout in seconds
				'cookie'	=> [],		//	array or string are both okay.
				'headers'	=> [],
			],
			$arrResponse
		);
		if ( CConst::ERROR_SUCCESS == $nCall &&
			$cRequest->IsValidVData( $arrResponse ) )
		{
			echo "\r\n";
			echo "nErrorId\t\t : " . $arrResponse['errorid'] . "\r\n";
			echo "sErrorDesc\t : " . $arrResponse['errordesc'] . "\r\n";
			echo "sVersion\t\t : " . $arrResponse['version'] . "\r\n";
			echo "arrVData\t\t :\r\n";
			print_r( $arrResponse['vdata'] );

			echo "arrJson\t :\r\n";
			print_r( $arrResponse['json'] );
		}
	}

	public function testLoginViaHttpPut()
	{
		//
		//	https://account.xs.cn/api/login
		//
		//	u_name		13810550569
		//	u_pwd		qqqqqqqq
		//	u_keep		1
		//	u_ctype		0
		//

		$cRequest	= CRequest::GetInstance();
		$arrResponse	= [];
		$nCall		= $cRequest->Http
		(
			[
				'method'	=> 'PUT',
				'url'		=> 'http://account.xs.cn/api/login',
				'data'		=>
					[
						'u_name'	=> '13810550569',
						'u_pwd'		=> 'qqqqqqqq',
						'u_keep'	=> 1,
						'u_ctype'	=> 0,
					],
				'version'	=> '1.0',
				'timeout'	=> 30,		//	timeout in seconds
				'cookie'	=> [],		//	array or string are both okay.
				'headers'	=> [],
			],
			$arrResponse
		);
		if ( CConst::ERROR_SUCCESS == $nCall &&
			$cRequest->IsValidVData( $arrResponse ) )
		{
			echo "\r\n";
			echo "nErrorId\t\t : " . $arrResponse['errorid'] . "\r\n";
			echo "sErrorDesc\t : " . $arrResponse['errordesc'] . "\r\n";
			echo "sVersion\t\t : " . $arrResponse['version'] . "\r\n";
			echo "arrVData\t\t :\r\n";
			print_r( $arrResponse['vdata'] );

			echo "arrJson\t :\r\n";
			print_r( $arrResponse['json'] );
		}
	}


	public function testIsValidRawResponse()
	{
		$arrData	=
		[
			[ false,	null ],
			[ false,	'' ],
			[ false,	[] ],
			[ false,	123 ],
			[ false,	[ 'data' => 'sss', 'status' => 'ss', 'headers' => []] ],
			[ true,		[ 'data' => 'sss', 'status' => 200, 'headers' => []] ],
		];

		$cRequest	= CRequest::GetInstance();
		$nNo		= 1;

		//	...
		echo "\r\n";

		foreach ( $arrData as $arrItem )
		{
			$bGoal	= $arrItem[ 0 ];
			$arrRes	= $arrItem[ 1 ];

			printf
			(
				"[%02d] IsValidRawResponse\t- %s\r\n",
				$nNo,
				( $bGoal == $cRequest->IsValidRawResponse( $arrRes ) ? "OK" : "ERROR" )
			);

			//	...
			$nNo ++;
		}
	}



}
