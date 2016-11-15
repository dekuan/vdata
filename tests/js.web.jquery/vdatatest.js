var assert	= chai.assert;
var expect	= chai.expect;
var should	= chai.should;


describe( "vdata", function()
{
	describe( "#errors", function()
	{
		it ( "`VDATA.ERROR.SUCCESS` should equal 0", function()
		{
			expect( VDATA.ERROR.SUCCESS ).to.equal( 0 );
		});
		it ( "`VDATA.ERROR.USER_START` should equal 10000", function()
		{
			expect( VDATA.ERROR.USER_START ).to.equal( 10000 );
		});
		it ( "`VDATA.ERROR.USER_END` should equal 99999", function()
		{
			expect( VDATA.ERROR.USER_END ).to.equal( 99999 );
		});
		it ( "`VDATA.ERROR.UNKNOWN` should equal -100000", function()
		{
			expect( VDATA.ERROR.UNKNOWN ).to.equal( -100000 );
		});
		it ( "`VDATA.ERROR.ACCESS_DENIED` should equal -100001", function()
		{
			expect( VDATA.ERROR.ACCESS_DENIED ).to.equal( -100001 );
		});
		it ( "`VDATA.ERROR.PARAMETER` should equal -100002", function()
		{
			expect( VDATA.ERROR.PARAMETER ).to.equal( -100002 );
		});
		it ( "`VDATA.ERROR.EXCEPTION` should equal -100011", function()
		{
			expect( VDATA.ERROR.EXCEPTION ).to.equal( -100011 );
		});
		it ( "`VDATA.ERROR.NETWORK` should equal -100300", function()
		{
			expect( VDATA.ERROR.NETWORK ).to.equal( -100300 );
		});
		it ( "`VDATA.ERROR.JSON` should equal -100301", function()
		{
			expect( VDATA.ERROR.JSON ).to.equal( -100301 );
		});
	});


	describe( "#constants", function()
	{
		it ( "`VDATA.CONST.HTTP_HEADER_VERSION_ACCEPT` should equal to `application/vdata+json+version:`", function()
		{
			expect( VDATA.CONST.HTTP_HEADER_VERSION_ACCEPT ).to.equal( 'application/vdata+json+version:' );
		});
		it ( "`VDATA.CONST.HTTP_SUPPORTED_METHODS['GET']` should equal to true", function()
		{
			expect( VDATA.CONST.HTTP_SUPPORTED_METHODS['GET'] ).to.equal( true );
		});
	});

	describe( "#objects", function()
	{
		it ( "`VDATA` should be an object", function()
		{
			expect( typeof( VDATA ) ).to.equal( 'object' );
		});
	});


	describe( "#functions", function()
	{
		//it ( "`$` should be an object", function()
		//{
		//	expect( $ instanceof jQuery ).to.true();
		//});

		it ( "`$.trim` should be `function`", function()
		{
			expect( typeof( $.trim ) ).to.equal( 'function' );
		});

		it ( "`Get` method should return an vdata object, see console of browser", function()
		{
			var vCall = VDATA.Get
			(
				{
					'url'	: 'http://vdata.dekuan.org/api/vdata/',
					'async'	: false,
					'data'	: { 'get1' : 2, 'get2' : 3 }
				},
				function( oResponse )
				{
					console.log( oResponse );
					expect( typeof( oResponse ) ).to.equal( 'object' );
				}
			);

			console.log( "Call `Get` = " + vCall );
		});

		it ( "`Post` method should return an vdata object, see console of browser", function()
		{
			var vCall = VDATA.Post
			(
				{
					'url'	: 'http://vdata.dekuan.org/api/vdata/',
					'async'	: false,
					'data'	: { 'post1' : 2, 'post2' : 3 }
				},
				function( oResponse )
				{
					console.log( oResponse );
					expect( typeof( oResponse ) ).to.equal( 'object' );
				}
			);

			console.log( "Call `Post` = " + vCall );
		});

		//it ( "`Put` method should return an vdata object, see console of browser", function()
		//{
		//	//VDATA.Put
		//	//(
		//	//	{
		//	//		'url'	: 'http://vdata-loc.dekuan.org/api/vdata/',
		//	//		'async'	: true,
		//	//		'data'	: { 'post1' : 2, 'post2' : 3 }
		//	//	},
		//	//	function( oResponse )
		//	//	{
		//	//		console.log( oResponse );
		//	//		expect( typeof( oResponse ) ).to.equal( 'object' );
		//	//	}
		//	//);
		//
		//	$.ajax({
		//		type: "PUT",
		//		url: "http://vdata-loc.dekuan.org/api/vdata/",
		//		data: "1",
		//		crossDomain: true,
		//		dataType: "json",
		//		success: function ( sText )
		//		{
		//			console.log( sText );
		//		}
		//
		//	});
		//});





		it ( "Deferred `Get` method should return an vdata object, see console of browser", function()
		{
			var vCall = VDATA.Get
			(
				{
					'url'	: 'http://vdata.dekuan.org/api/vdata/',
					'async'	: false,
					'data'	: { 'get1' : 2, 'get2' : 3 }
				}
			)
			.done( function( oResponse )
			{
				console.log( "done by calling Deferred `Get`" );
				console.log( oResponse );
				expect( typeof( oResponse ) ).to.equal( 'object' );
			})
			.fail( function( oResponse )
			{
				console.log( "fail by calling Deferred `Get`" );
				console.log( oResponse );
				expect( typeof( oResponse ) ).to.equal( 'object' );
			});

			console.log( "Call Deferred `Get` = " + vCall );
		});

		it ( "Deferred `Post` method should return an vdata object, see console of browser", function()
		{
			var vCall = VDATA.Post
			(
				{
					'url'	: 'http://vdata.dekuan.org/api/vdata/',
					'async'	: false,
					'data'	: { 'post1' : 2, 'post2' : 3 }
				}
			)
			.done( function( oResponse )
			{
				console.log( "done by calling Deferred `Post`" );
				console.log( oResponse );
				expect( typeof( oResponse ) ).to.equal( 'object' );
			})
			.fail( function( oResponse )
			{
				console.log( "fail by calling Deferred `Post`" );
				console.log( oResponse );
				expect( typeof( oResponse ) ).to.equal( 'object' );
			});

			console.log( "Call Deferred `Post` = " + vCall );
		});
	});

});