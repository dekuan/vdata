<?php

namespace dekuan\vdata;



/**
 *	Created by PhpStorm.
 *	User: xing
 *	Date: 5:16 PM December 15, 2017
 */

/***
 *	Class CAssertResult
 *	@package dekuan\vdata
 */
class CAssertResult
{
	public function __construct( $sClassName, $sFuncName, $sCallMethod, $nErrorId, $vDumpString = null )
	{
		if ( CConst::ERROR_SUCCESS === $nErrorId )
		{
			$this->assertTrue( true );
		}
		else
		{
			printf( "\r\n" );
			printf( "# ERROR %8d, ", $nErrorId );
			printf( "%s::%s->%s\r\n", $sClassName, $sFuncName, $sCallMethod );
			printf( "\r\n" );
		}

		if ( null !== $vDumpString )
		{
			if ( is_array( $vDumpString ) )
			{
				print_r( $vDumpString );
			}
			else
			{
				var_dump( $vDumpString );	
			}
		}
	}
	
	public function __destruct()
	{
	}

	public function assertTrue( $condition, $message = '' )
	{
		\PHPUnit\Framework\TestCase::assertTrue( $condition, $message = '' );
	}
}