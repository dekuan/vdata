<?php

@ ini_set( 'date.timezone', 'Etc/GMT＋0' );
@ date_default_timezone_set( 'Etc/GMT＋0' );

@ ini_set( 'display_errors',	'on' );
@ ini_set( 'max_execution_time',	'60' );
@ ini_set( 'max_input_time',	'0' );
@ ini_set( 'memory_limit',	'512M' );

//	mb 环境定义
mb_internal_encoding( "UTF-8" );

//	Turn on output buffering
ob_start();


require_once( __DIR__ . "/../../vendor/autoload.php");
require_once( __DIR__ . "/../../src/CConst.php");
require_once( __DIR__ . "/../../src/CRemote.php");


use dekuan\vdata\CConst;


/**
 * Created by PhpStorm.
 * User: xing
 * Date: 25/10/2016
 * Time: 12:02 PM
 */
class TestCConst extends PHPUnit_Framework_TestCase
{
	public function testProjectId()
	{
		$arrData	=
			[
				[ true,		1 ],
				[ true,		55 ],
				[ true,		99 ],
				[ false,	0 ],
				[ false,	-1 ],
				[ false,	null ],
				[ false,	[] ],
				[ false,	"" ],
			];
		foreach ( $arrData as $arrItem )
		{
			$nProjectStart	= CConst::GetProjectStart( $arrItem[ 1 ] );
			$bResult	= ( $arrItem[ 0 ] == CConst::IsValidProjectStart( $nProjectStart ) );

			//	...
			echo __FUNCTION__ . " :: ";
			echo ( $bResult ? "[OK]" : "[ER]" );
			echo "\t$nProjectStart\t";
			echo ( is_numeric( $arrItem[ 1 ] ) ? $arrItem[ 1 ] : "NULL" );
			echo "\r\n";

			$this->assertTrue( $bResult );
		}
	}

	public function testProjectStarts()
	{
		echo "\r\n";

		for ( $i = 1; $i < 100; $i ++ )
		{
			$nProjectStart	= CConst::GetProjectStart( $i );
			$bResult	= CConst::IsValidProjectStart( $nProjectStart );

			//	...
			echo __FUNCTION__ . " :: ";
			echo ( $bResult ? "[OK]" : "[ER]" );
			echo "\t$nProjectStart";
			echo "\r\n";

			$this->assertTrue( $bResult );
		}
	}

	public function testUserCustomizedErrorIds()
	{
		echo "\r\n";

		$nProjectStart	= CConst::GetProjectStart( 20 );

		$arrData	=
			[
				$nProjectStart + CConst::ERROR_USER_START + 100,
				$nProjectStart + CConst::ERROR_USER_START + 101,
			];

		print_r( $arrData );
	}
}
