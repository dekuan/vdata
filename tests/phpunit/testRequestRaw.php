<?php

namespace dekuan\vdata;



use dekuan\vdata\CConst;
use dekuan\vdata\CRequest;
use dekuan\delib\CLib;


/***
 *	Class testRequestRaw
 *	@package dekuan\vdata
 */
class testRequestRaw extends \PHPUnit\Framework\TestCase
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
				'headers'	=>
					[
						'X-Application-Id'	=> '99999-88888',
					],
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
