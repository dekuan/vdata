var assert	= require('assert');
var fs		= require('fs');
var vm		= require('vm');
var expect	= require('chai').expect;
var jsdom	= require('mocha-jsdom');


describe( 'mocha dom', function()
{
	var $;

	jsdom();
	//jsdom
	//({
	//	src : fs.readFileSync( 'tests/js/jquery-test.js', 'utf-8' )
	//});

	before( function()
	{
		$ = require('jquery');
		//
		////	...
		var jsdom = require('jsdom-global')();
		global.$ = global.jQuery = require('jquery')( window );
	});


	// includes minified & uglified version, assuming mocha is run in repo root dir
	var path = 'tests/js/jquery-test.js';
	var code = fs.readFileSync(path);
	vm.runInThisContext(code);




	it ( 'has document', function()
	{
		var div = document.createElement( 'div' );
		expect( div.nodeName ).eql( 'DIV' );
	});
	//it ( 'works', function()
	//{
	//	document.body.innerHTML = '<div>hola</div>';
	//	expect( $( "div" ).html() ).eql( 'hola' );
	//});

	describe( "functions", function()
	{
		it ( "`Name` method should return an string", function()
		{
			var obj = new jQueryTest();
			expect( obj.Name() ).to.equal( 'XING' );
		});
	});
});