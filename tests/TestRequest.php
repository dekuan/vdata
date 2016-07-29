<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/dekuan/delib/src/CLib.php';
require __DIR__ . '/../src/CConst.php';
require __DIR__ . '/../src/CCors.php';
require __DIR__ . '/../src/CVData.php';
require __DIR__ . '/../src/CRequest.php';
require __DIR__ . '/../src/CResponse.php';

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
	public function testLoginViaHttp()
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

		$cRequest->Post
		([
			'url'		=> 'http://account.xs.cn/api/login',
			'gets'		=> [],
			'posts'		=>
			[
				'u_name'	=> '13810550569',
				'u_pwd'		=> 'qqqqqqqq',
				'u_keep'	=> 1,
				'u_ctype'	=> 0,
			],
			'version'	=> '1.0',
			'timeout'	=> 60,
			'cookie'	=> [],
			'headers'	=> [],
			'response'	=> function( $nErrorId, $sErrorDesc, $arrVData, $sVersion, $arrParents, $arrJson )
			{
				echo "\r\n";
				echo "nErrorId\t\t : $nErrorId\r\n";
				echo "sErrorDesc\t : $sErrorDesc\r\n";
				echo "sVersion\t\t : $sVersion\r\n";
				echo "arrVData\t\t :\r\n";
				print_r( $arrVData );

				echo "arrParents\t :\r\n";
				print_r( $arrParents );

				echo "arrJson\t :\r\n";
				print_r( $arrJson );
			}
		]);


	}



}
