var assert = require('assert'),
	sinon = require('sinon'),
	$ = require('jquery'),
	url = require('./url_shorter.js');

describe('Url', function()
{
	describe(".shorten", function()
	{
		beforeEach(function ()
		{
			//this.callback = sinon.spy();
			//sinon.stub( $, 'ajax', function(options)
			//{
			//	var dfd = $.Deferred();
			//	if(options.success)
			//		dfd.done(options.success({status_code: 200, data: {url: "bit.ly/aaaa"}}));
			//	if(options.error)
			//		dfd.fail(options.error);
			//
			//	dfd.success = dfd.done;
			//	dfd.error = dfd.fail;
			//	return dfd;
			//});

		});

		afterEach(function ()
		{
			//$.ajax.restore();
		});




		it("return false when url is not passed", function ()
		{
			assert.equal(false,url.shorten(""));
			assert.equal(false,url.shorten());
			assert.equal(false,url.shorten(null));
		});

		it("call the ajax once", function (done)
		{
			url.shorten("http://baidu.com", this.callback).resolve();
			assert($.ajax.calledOnce);
			done();
		});

		it("yeild success", function (done)
		{
			url.shorten("http://baidu.com", this.callback).resolve();
			assert(this.callback.withArgs(0,"bit.ly/aaaa").calledOnce);
			done();
		});

		it("yields error", function (done)
		{
			url.shorten("http://baidu.com", this.callback).reject();
			assert(this.callback.withArgs(-1, "ajaxFailed").calledOnce);
			done();
		});
	});
});