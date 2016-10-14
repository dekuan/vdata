var path	= require('path');

module.exports	= function( config )
{
	config.set
	({
		frameworks	: [ 'mocha', 'sinon-chai' ],
		browsers	: [ 'PhantomJS' ],
		reporters	: [ 'spec', 'coverage' ],
		files		:
		[
			'tests/js.command/vdata.test.js'
		],
		preprocessors	:
		{
			'tests/js/vdata.test.js' : [ 'webpack', 'sourcemap' ]
		},
		client		:
		{
			mocha	:
			{
				timeout : 100000
			}
		},
		browserNoActivityTimeout : 100000,
		webpack :
		{
			devtool	: '#inline-source-map',
			module	:
			{
				loaders	:
				[
					{
						include	: path.resolve( __dirname, 'tests/js.web/jquery.js' ),
						loader	: 'istanbul-instrumenter'
					}
				]
			}
		},
		webpackMiddleware:
		{
			noInfo: true
		},
		coverageReporter:
		{
			reporters:
			[
				{ type: 'lcov', subdir: '.' },
				{ type: 'text-summary' }
			]
		}
	});
};
