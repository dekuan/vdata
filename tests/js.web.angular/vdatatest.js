angular.module( "cpApp", [ 'vdata' ], function( $interpolateProvider )
{
	$interpolateProvider.startSymbol ( "<%" );
	$interpolateProvider.endSymbol ( "%>" );
})
.controller
(
	"vdataTest",
	[
		"$scope",
		"vdataConst",
		"vdataFactory",
		function ( $scope, vdataConst, vdataFactory )
{
	$scope.SendGetRequest = function()
	{
		var nCall;

		nCall	= vdataFactory.Get
		(
			{
				'url'	: 'http://vdata.dekuan.org/api/vdata/',
				'async'	: false,
				'data'	: { 'get_hour' : (new Date).getHours(), 'get2' : 3 }
			},
			function( oJsonData )
			{
				console.log( oJsonData );

				if ( vdataConst.ERROR.SUCCESS == oJsonData['errorid'] )
				{
					//  successfully
				}
				else if ( vdataConst.ERROR.NETWORK == oJsonData['errorid'] )
				{
					//	network
				}
				else if ( vdataConst.ERROR.EXCEPTION == oJsonData['errorid'] )
				{
					//	exception
				}
				else
				{
					//  some cases else
				}
			}
		);

		console.log( "Get request result: " + ( vdataConst.ERROR.SUCCESS == nCall ? "Success" : "Error" ) );
	};

	$scope.SendPostRequest = function()
	{
		var nCall;

		nCall	= vdataFactory.Post
		(
			{
				'url'	: 'http://vdata.dekuan.org/api/vdata/',
				'async'	: false,
				'data'	: { 'post_hour' : (new Date).getHours(), 'post2' : 200 }
			},
			function( oJsonData )
			{
				console.log( oJsonData );

				if ( vdataConst.ERROR.SUCCESS == oJsonData['errorid'] )
				{
					//  successfully
				}
				else if ( vdataConst.ERROR.NETWORK == oJsonData['errorid'] )
				{
					//	network
				}
				else if ( vdataConst.ERROR.EXCEPTION == oJsonData['errorid'] )
				{
					//	exception
				}
				else
				{
					//  some cases else
				}
			}
		);

		console.log( "Post request result: " + ( vdataConst.ERROR.SUCCESS == nCall ? "Success" : "Error" ) );
	};

	$scope.SendPutRequest = function()
	{
		var nCall;

		nCall	= vdataFactory.Put
		(
			{
				'url'	: 'http://vdata.dekuan.org/api/vdata/',
				'async'	: false,
				'data'	: { 'post_hour' : (new Date).getHours(), 'post2' : 200 }
			},
			function( oJsonData )
			{
				console.log( oJsonData );

				if ( vdataConst.ERROR.SUCCESS == oJsonData['errorid'] )
				{
					//  successfully
				}
				else if ( vdataConst.ERROR.NETWORK == oJsonData['errorid'] )
				{
					//	network
				}
				else if ( vdataConst.ERROR.EXCEPTION == oJsonData['errorid'] )
				{
					//	exception
				}
				else
				{
					//  some cases else
				}
			}
		);

		console.log( "Put request result: " + ( vdataConst.ERROR.SUCCESS == nCall ? "Success" : "Error" ) );
	};


	//	...
	function _constructor()
	{
	}

	//
	//	constructor
	//
	_constructor();
}]);


