it("return false when url is not passed", function () {
	assert.equal(false,url.shorten(""));
	assert.equal(false,url.shorten());
	assert.equal(false,url.shorten(null));
});

it("call the ajax once", function (done) {
	// This will execute the method assigned to Deferred.done
	url.shorten("http://google.com", this.callback).resolve();

	// sinon will check whether the ajax method is called Once
	assert($.ajax.calledOnce);
	done();
});

it("yeild success", function (done) {
	url.shorten("http://google.com", this.callback).resolve();

	// sinon will check whether the success method is called Once
	assert(this.callback.withArgs(0,"bit.ly/aaaa").calledOnce);
	done();
});

it("yields error", function (done) {
	// This will execute the method assigned to Deferred.fail
	url.shorten("http://google.com", this.callback).reject();

	// sinon will check whether the error method is called Once
	assert(this.callback.withArgs(-1, "ajaxFailed").calledOnce);
	done();
});