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



class CConstTest1
{
	const ERROR_PROJECT_ID		= 1;
	const ERROR_PROJECT_BASE	= CConst::ERROR_PROJECT_START * self::ERROR_PROJECT_ID + CConst::ERROR_USER_START;

	const ERROR_USER_DDD		= self::ERROR_PROJECT_BASE + 1;
}


/**
 * Created by PhpStorm.
 * User: xing
 * Date: 25/10/2016
 * Time: 12:02 PM
 */
class TestCConst extends PHPUnit_Framework_TestCase
{
	public function testUserCustomizedErrorIds()
	{
		echo "\r\n";

		$arrData	=
			[
				CConstTest1::ERROR_USER_DDD,
			];

		print_r( $arrData );
	}
}
