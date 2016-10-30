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
				'url'	: 'http://www-loc.dekuan.org/api/vdata/',
				'async'	: false,
				'data'	: { 'get1' : 2, 'get2' : 3 }
			},
			function( oResponse )
			{
				console.log( oResponse );
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
				'url'	: 'http://www-loc.dekuan.org/api/vdata/',
				'async'	: false,
				'data'	: { 'post1' : 99, 'post2' : 200 }
			},
			function( oResponse )
			{
				console.log( oResponse );
			}
		);

		console.log( "Post request result: " + ( vdataConst.ERROR.SUCCESS == nCall ? "Success" : "Error" ) );
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


