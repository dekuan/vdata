beforeEach(function () {
	this.callback = sinon.spy();

	// Stubbing the ajax method
	sinon.stub($, 'ajax', function (options) {
		// Creating a deffered object
		var dfd = $.Deferred();

		// assigns success callback to done.
		if(options.success) dfd.done(options.success({status_code: 200, data: {url: "bit.ly/aaaa"}}));

		// assigns error callback to fail.
		if(options.error) dfd.fail(options.error);
		dfd.success = dfd.done;
		dfd.error = dfd.fail;

		// returning the deferred object so that we can chain it.
		return dfd;
	});

});

afterEach( function()
{
	$.ajax.restore();
});