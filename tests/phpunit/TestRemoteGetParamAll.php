<?php

require_once __DIR__ . '/../../src/CRemote.php';
require_once __DIR__ . '/../../src/CConst.php';
require_once __DIR__ . '/../../vendor/dekuan/delib/src/CLib.php';



class TestGetParamAll extends PHPUnit_Framework_TestCase
{
	private $m_arrInput =
		[
			'sr'	=> 1,
			'apid'	=> 'id-x-c',
			'apnm'	=> 'XING NAME',
			'aver'	=> '2.0.12',
			'apc'	=> '2.0.12',
			'sver'	=> '1.0',
			'pmod'	=> 'iPhone5s',
			'reso'	=> '1136x640',
			'mobop'	=> 'CMCC',
			'im'	=> 'imssdfasdfasdfasdfdsf',
			'nstat'	=> '4G',
			'mad'	=> '124123:222:222:223:DFDF',
			'sla'	=> 'chs',
			'stz'	=> '4800',
		];
	public function testGetParamAll()
	{
		echo "\r\ntestGetParamAll\r\n";

		print_r( \dekuan\vdata\CRemote::GetParamAll( $this->m_arrInput ) );
	}
}
