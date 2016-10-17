/********************************************************************************
 *
 *	VData Client by DeKuan, Inc
 *	www.dekuan.org
 *
 ********************************************************************************/



/**
 *	class of CVDataLib
 */
function CVDataLib()
{
	/**
	 *	@return {boolean}
	 */
	this.IsString = function( vValue )
	{
		return ( "string" === typeof( vValue ) );
	};

	/**
	 *	@return {boolean}
	 */
	this.IsArray = function( vValue )
	{
		return ( "array" === typeof( vValue ) );
	};

	/**
	 *	@return {boolean}
	 */
	this.IsFunction = function( vValue )
	{
		return ( "function" === typeof( vValue ) );
	};

	/**
	 *	@return {boolean}
	 */
	this.IsObject = function( vValue )
	{
		return ( "object" === typeof( vValue ) );
	};
}


/**
 *	class of CVDataAjax
 */
function CVDataAjax()
{
	var m_cLib	= new CVDataLib();

	var ajaxLocation;
	var ajaxSettings;



	this.ajax = function( sUrl, oOptions )
	{

	};


	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	function _Construct()
	{
		//
		//	#8138, IE may throw an exception when accessing
		//	a field from window.location if document.domain has been set
		//
		try
		{
			ajaxLocation = location.href;
		}
		catch( e )
		{
			//	Use the href attribute of an A element
			//	since IE will modify it given document.location
			ajaxLocation		= document.createElement( "a" );
			ajaxLocation.href	= "";
			ajaxLocation		= ajaxLocation.href;
		}

		ajaxSettings =
		{
			url		: ajaxLocation,
			type		: "GET",
			isLocal		: rlocalProtocol.test( ajaxLocParts[ 1 ] ),
			global		: true,
			processData	: true,
			async		: true,
			contentType	: "application/x-www-form-urlencoded; charset=UTF-8",
			accepts		:
			{
				"*"	: allTypes,
				text	: "text/plain",
				html	: "text/html",
				xml	: "application/xml, text/xml",
				json	: "application/json, text/javascript"
			},
			contents:
			{
				xml	: /xml/,
				html	: /html/,
				json	: /json/
			},
			responseFields	:
			{
				xml	: "responseXML",
				text	: "responseText",
				json	: "responseJSON"
			},

			//
			//	Data converters
			//	Keys separate source (or catchall "*") and destination types with a single space
			//
			converters	:
			{
				//	Convert anything to text
				"* text"	: String,

				//	Text to html (true = no transformation)
				"text html"	: true,

				//	Evaluate text as a json expression
				"text json"	: jQuery.parseJSON,

				//	Parse text as xml
				"text xml"	: jQuery.parseXML
			},

			//
			//	For options that shouldn't be deep extended:
			//	you can add your own custom options here if
			//	and when you create one that shouldn't be
			//	deep extended (see ajaxExtend)
			//
			flatOptions	:
			{
				url	: true,
				context	: true
			}
		};
	}

	function extend()
	{
		var src, copyIsArray, copy, name, options, clone,
			target = arguments[0] || {},
			i = 1,
			length = arguments.length,
			deep = false;

		//	Handle a deep copy situation
		if ( typeof target === "boolean" )
		{
			deep = target;

			// skip the boolean and the target
			target = arguments[ i ] || {};
			i ++;
		}

		//	Handle case when target is a string or something (possible in deep copy)
		if ( typeof target !== "object" && ! m_cLib.IsFunction( target ) )
		{
			target = {};
		}

		//	extend jQuery itself if only one argument is passed
		if ( i === length )
		{
			target = this;
			i --;
		}

		for ( ; i < length; i++ )
		{
			//	Only deal with non-null/undefined values
			if ( (options = arguments[ i ]) != null )
			{
				//	Extend the base object
				for ( name in options )
				{
					src = target[ name ];
					copy = options[ name ];

					//	Prevent never-ending loop
					if ( target === copy )
					{
						continue;
					}

					//	Recurse if we're merging plain objects or arrays
					if ( deep && copy && ( m_cLib.IsObject( copy ) || ( copyIsArray = m_cLib.IsArray( copy ) ) ) )
					{
						if ( copyIsArray )
						{
							copyIsArray = false;
							clone = src && m_cLib.IsArray(src) ? src : [];
						}
						else
						{
							clone = src && m_cLib.IsObject(src) ? src : {};
						}

						//	Never move original objects, clone them
						target[ name ] = extend( deep, clone, copy );

						//	Don't bring in undefined values
					}
					else if ( copy !== undefined )
					{
						target[ name ] = copy;
					}
				}
			}
		}

		//	Return the modified object
		return target;
	}

	//
	//	Creates a full fledged settings object into target
	//	with both ajaxSettings and settings fields.
	//	If target is omitted, writes into ajaxSettings.
	//
	function _ajaxSetup( target, settings )
	{
		return settings ?

			//	Building a settings object
			ajaxExtend( ajaxExtend( target, ajaxSettings ), settings ) :

			//	Extending ajaxSettings
			ajaxExtend( ajaxSettings, target );
	}

	//
	//	A special extend for ajax options
	//	that takes "flat" options (not to be deep extended)
	//	Fixes #9887
	//
	function ajaxExtend( target, src )
	{
		var deep;
		var key;
		var flatOptions = ajaxSettings.flatOptions || {};

		for ( key in src )
		{
			if ( src[ key ] !== undefined )
			{
				( flatOptions[ key ] ? target : ( deep || (deep = {}) ) )[ key ] = src[ key ];
			}
		}
		if ( deep )
		{
			extend( true, target, deep );
		}

		return target;
	}



	function _Ajax( url, options )
	{
		//
		//	If url is an object, simulate pre-1.5 signature
		//
		if ( "object" === typeof url )
		{
			options	= url;
			url	= undefined;
		}

		//	Force options to be an object
		options = options || {};


		// Create the final options object
		var s = _ajaxSetup( {}, options );

		var // Cross-domain detection vars
			parts,
		// Loop variable
			i,
		// URL without anti-cache param
			cacheURL,
		// Response headers as string
			responseHeadersString,
		// timeout handle
			timeoutTimer,

		// To know if global events are to be dispatched
			fireGlobals,

			transport,
		// Response headers
			responseHeaders,
		// Callbacks context
			callbackContext = s.context || s,
		// Context for global events is callbackContext if it is a DOM node or jQuery collection
			globalEventContext = s.context && ( callbackContext.nodeType || callbackContext.jquery ) ?
				jQuery( callbackContext ) :
				jQuery.event,

		// Status-dependent callbacks
			statusCode = s.statusCode || {},
		// Headers (they are sent all at once)
			requestHeaders = {},
			requestHeadersNames = {},
		// The jqXHR state
			state = 0,
		// Default abort message
			strAbort = "canceled";

		//
		//	Fake xhr
		//
		var jqXHR =
		{
			readyState : 0,

			//	Builds headers hashtable if needed
			getResponseHeader : function( key )
			{
				var match;
				if ( 2 === state )
				{
					if ( ! responseHeaders )
					{
						responseHeaders = {};
						while ( ( match = rheaders.exec( responseHeadersString ) ) )
						{
							responseHeaders[ match[ 1 ].toLowerCase() ] = match[ 2 ];
						}
					}
					match = responseHeaders[ key.toLowerCase() ];
				}
				return match == null ? null : match;
			},

			//	Raw string
			getAllResponseHeaders : function()
			{
				return 2 === state ? responseHeadersString : null;
			},

			//	Caches the header
			setRequestHeader : function( name, value )
			{
				var lname = name.toLowerCase();
				if ( !state )
				{
					name = requestHeadersNames[ lname ] = requestHeadersNames[ lname ] || name;
					requestHeaders[ name ] = value;
				}
				return this;
			},

			//	Overrides response content-type header
			overrideMimeType : function( type )
			{
				if ( ! state )
				{
					s.mimeType = type;
				}
				return this;
			},

			//	Status-dependent callbacks
			statusCode : function( map )
			{
				var code;
				if ( map )
				{
					if ( state < 2 )
					{
						for ( code in map )
						{
							//	Lazy-add the new callback in a way that preserves old ones
							statusCode[ code ] = [ statusCode[ code ], map[ code ] ];
						}
					}
					else
					{
						//	Execute the appropriate callbacks
						jqXHR.always( map[ jqXHR.status ] );
					}
				}
				return this;
			},

			//	Cancel the request
			abort : function( statusText )
			{
				var finalText = statusText || strAbort;
				if ( transport )
				{
					transport.abort( finalText );
				}
				done( 0, finalText );
				return this;
			}
		};

		//
		//	Attach deferreds
		//
		deferred.promise( jqXHR ).complete	= completeDeferred.add;
		jqXHR.success				= jqXHR.done;
		jqXHR.error				= jqXHR.fail;

		//
		//	Remove hash character (#7531: and string promotion)
		//	Add protocol if not provided (#5866: IE7 issue with protocol-less urls)
		//	Handle falsy url in the settings object (#10093: consistency with old signature)
		//	We also use the url parameter if available
		//
		s.url	= ( ( url || s.url || ajaxLocation ) + "" ).replace( rhash, "" ).replace( rprotocol, ajaxLocParts[ 1 ] + "//" );

		//	Alias method option to type as per ticket #12004
		s.type	= options.method || options.type || s.method || s.type;

		//	Extract dataTypes list
		s.dataTypes = jQuery.trim( s.dataType || "*" ).toLowerCase().match( rnotwhite ) || [ "" ];

		//	A cross-domain request is in order when we have a protocol:host:port mismatch
		if ( s.crossDomain == null )
		{
			parts = rurl.exec( s.url.toLowerCase() );
			s.crossDomain = !! ( parts &&
				( parts[ 1 ] !== ajaxLocParts[ 1 ] || parts[ 2 ] !== ajaxLocParts[ 2 ] ||
				( parts[ 3 ] || ( parts[ 1 ] === "http:" ? "80" : "443" ) ) !==
				( ajaxLocParts[ 3 ] || ( ajaxLocParts[ 1 ] === "http:" ? "80" : "443" ) ) )
			);
		}

		//	Convert data if not already a string
		if ( s.data && s.processData && typeof s.data !== "string" )
		{
			s.data = jQuery.param( s.data, s.traditional );
		}

		//	Apply prefilters
		inspectPrefiltersOrTransports( prefilters, s, options, jqXHR );

		//	If request was aborted inside a prefilter, stop there
		if ( 2 === state )
		{
			return jqXHR;
		}

		//
		//	We can fire global events as of now if asked to
		//	Don't fire events if jQuery.event is undefined in an AMD-usage scenario (#15118)
		//
		fireGlobals	= jQuery.event && s.global;

		//	Watch for a new set of requests
		if ( fireGlobals && 0 === jQuery.active ++ )
		{
			jQuery.event.trigger( "ajaxStart" );
		}

		//	Uppercase the type
		s.type		= s.type.toUpperCase();

		//	Determine if request has content
		s.hasContent	= ! rnoContent.test( s.type );

		//
		//	Save the URL in case we're toying with the If-Modified-Since
		//	and/or If-None-Match header later on
		//
		cacheURL	= s.url;

		//	More options handling for requests with no content
		if ( ! s.hasContent )
		{
			//	If data is available, append data to url
			if ( s.data )
			{
				cacheURL = ( s.url += ( rquery.test( cacheURL ) ? "&" : "?" ) + s.data );
				//	#9682: remove data so that it's not used in an eventual retry
				delete s.data;
			}

			//	Add anti-cache in url if needed
			if ( false === s.cache )
			{
				s.url = rts.test( cacheURL ) ?
					//	If there is already a '_' parameter, set its value
					cacheURL.replace( rts, "$1_=" + nonce++ )
					:
					//	Otherwise add one to the end
					cacheURL + ( rquery.test( cacheURL ) ? "&" : "?" ) + "_=" + nonce ++;
			}
		}

		//	Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
		if ( s.ifModified )
		{
			if ( jQuery.lastModified[ cacheURL ] )
			{
				jqXHR.setRequestHeader( "If-Modified-Since", jQuery.lastModified[ cacheURL ] );
			}
			if ( jQuery.etag[ cacheURL ] )
			{
				jqXHR.setRequestHeader( "If-None-Match", jQuery.etag[ cacheURL ] );
			}
		}

		//	Set the correct header, if data is being sent
		if ( s.data && s.hasContent && false !== s.contentType || options.contentType )
		{
			jqXHR.setRequestHeader( "Content-Type", s.contentType );
		}

		//	Set the Accepts header for the server, depending on the dataType
		jqXHR.setRequestHeader
		(
			"Accept",
			s.dataTypes[ 0 ] && s.accepts[ s.dataTypes[0] ] ?
				s.accepts[ s.dataTypes[0] ] + ( s.dataTypes[ 0 ] !== "*" ? ", " + allTypes + "; q=0.01" : "" )
				:
				s.accepts[ "*" ]
		);

		//	Check for headers option
		for ( i in s.headers )
		{
			jqXHR.setRequestHeader( i, s.headers[ i ] );
		}

		//	Allow custom headers/mimetypes and early abort
		if ( s.beforeSend && ( false === s.beforeSend.call( callbackContext, jqXHR, s ) || 2 === state ) )
		{
			//	Abort if not done already and return
			return jqXHR.abort();
		}

		//	aborting is no longer a cancellation
		strAbort = "abort";

		//	Install callbacks on deferreds
		for ( i in { success: 1, error: 1, complete: 1 } )
		{
			jqXHR[ i ]( s[ i ] );
		}

		//	Get transport
		transport = inspectPrefiltersOrTransports( transports, s, options, jqXHR );

		//	If no transport, we auto-abort
		if ( ! transport )
		{
			done( -1, "No Transport" );
		}
		else
		{
			jqXHR.readyState	= 1;

			//	Send global event
			if ( fireGlobals )
			{
				globalEventContext.trigger( "ajaxSend", [ jqXHR, s ] );
			}

			//	Timeout
			if ( s.async && s.timeout > 0 )
			{
				timeoutTimer = setTimeout
				(
					function()
					{
						jqXHR.abort( "timeout" );
					},
					s.timeout
				);
			}

			try
			{
				state	= 1;
				transport.send( requestHeaders, done );
			}
			catch ( e )
			{
				//	Propagate exception as error if not done
				if ( state < 2 )
				{
					done( -1, e );
				}
				else
				{
					//	Simply rethrow otherwise
					throw e;
				}
			}
		}

		//	Callback for when everything is done
		function done( status, nativeStatusText, responses, headers )
		{
			var isSuccess;
			var success;
			var error;
			var response;
			var modified;
			var statusText = nativeStatusText;

			//	Called once
			if ( 2 === state )
			{
				return;
			}

			//	State is "done" now
			state	= 2;

			//	Clear timeout if it exists
			if ( timeoutTimer )
			{
				clearTimeout( timeoutTimer );
			}

			//
			//	Dereference transport for early garbage collection
			//	(no matter how long the jqXHR object will be used)
			//
			transport	= undefined;

			//	Cache response headers
			responseHeadersString = headers || "";

			//	Set readyState
			jqXHR.readyState	= status > 0 ? 4 : 0;

			//	Determine if successful
			isSuccess	= ( status >= 200 && status < 300 || 304 === status );

			//	Get response data
			if ( responses )
			{
				response = ajaxHandleResponses( s, jqXHR, responses );
			}

			//	Convert no matter what (that way responseXXX fields are always set)
			response	= ajaxConvert( s, response, jqXHR, isSuccess );

			//	If successful, handle type chaining
			if ( isSuccess )
			{
				//	Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
				if ( s.ifModified )
				{
					modified = jqXHR.getResponseHeader( "Last-Modified" );
					if ( modified )
					{
						jQuery.lastModified[ cacheURL ] = modified;
					}
					modified = jqXHR.getResponseHeader( "etag" );
					if ( modified )
					{
						jQuery.etag[ cacheURL ] = modified;
					}
				}

				//	if no content
				if ( 204 === status || "HEAD" === s.type )
				{
					statusText = "nocontent";

				}
				else if ( 304 === status )
				{
					//	if not modified
					statusText = "notmodified";
				}
				else
				{
					//	If we have data, let's convert it
					statusText	= response.state;
					success		= response.data;
					error		= response.error;
					isSuccess	= ! error;
				}
			}
			else
			{
				//
				//	We extract error from statusText
				//	then normalize statusText and status for non-aborts
				//
				error = statusText;
				if ( status || !statusText )
				{
					statusText = "error";
					if ( status < 0 )
					{
						status = 0;
					}
				}
			}

			//	Set data for the fake xhr object
			jqXHR.status		= status;
			jqXHR.statusText	= ( nativeStatusText || statusText ) + "";

			//	Success/Error
			if ( isSuccess )
			{
				deferred.resolveWith( callbackContext, [ success, statusText, jqXHR ] );
			}
			else
			{
				deferred.rejectWith( callbackContext, [ jqXHR, statusText, error ] );
			}

			//	Status-dependent callbacks
			jqXHR.statusCode( statusCode );
			statusCode = undefined;

			if ( fireGlobals )
			{
				globalEventContext.trigger
				(
					isSuccess ? "ajaxSuccess" : "ajaxError",
					[ jqXHR, s, isSuccess ? success : error ]
				);
			}

			//	Complete
			completeDeferred.fireWith( callbackContext, [ jqXHR, statusText ] );

			if ( fireGlobals )
			{
				globalEventContext.trigger( "ajaxComplete", [ jqXHR, s ] );

				//	Handle the global AJAX counter
				if ( ! ( --jQuery.active ) )
				{
					jQuery.event.trigger( "ajaxStop" );
				}
			}
		}

		return jqXHR;
	}

	//
	//	construct
	//
	_Construct();
}

/**
 *	class of CVDataCore
 */
function CVDataCore()
{
	var m_oThis	= this;
	var m_cLib	= new CVDataLib();


	/**
	 *	@return {number}
	 *	HTTP Get
	 */
	this.Get = function( arrParam, pfnCallback )
	{
		if ( ! m_oThis.IsValidParam( arrParam ) || ! m_cLib.IsFunction( pfnCallback ) )
		{
			return false;
		}

		//	...
		arrParam[ 'method' ] = 'GET';
		return m_oThis.Http( arrParam, pfnCallback );
	};

	/**
	 *	@return {number}
	 *	HTTP Post
	 */
	this.Post = function( arrParam, pfnCallback )
	{
		if ( ! m_oThis.IsValidParam( arrParam ) || ! m_cLib.IsFunction( pfnCallback ) )
		{
			return VDATA.ERROR.PARAMETER;
		}

		//	...
		arrParam[ 'method' ] = 'POST';
		return m_oThis.Http( arrParam, pfnCallback );
	};

	/**
	 *	@return {number}
	 *	HTTP Put
	 */
	this.Put = function( arrParam, pfnCallback )
	{
		if ( ! m_oThis.IsValidParam( arrParam ) || ! m_cLib.IsFunction( pfnCallback ) )
		{
			return VDATA.ERROR.PARAMETER;
		}

		//	...
		arrParam[ 'method' ] = 'PUT';
		return m_oThis.Http( arrParam, pfnCallback );
	};

	/**
	 *	@return {number}
	 *	HTTP Put
	 */
	this.Delete = function( arrParam, pfnCallback )
	{
		if ( ! m_oThis.IsValidParam( arrParam ) || ! m_cLib.IsFunction( pfnCallback ) )
		{
			return VDATA.ERROR.PARAMETER;
		}

		//	...
		arrParam[ 'method' ] = 'DELETE';
		return m_oThis.Http( arrParam, pfnCallback );
	};

	/**
	 *	@return {number}
	 *	HTTP Get
	 */
	this.Http = function( arrParam, pfnCallback )
	{
		//
		//	arrParam	- object,	see .IsValidParam()
		//	pfnCallback	- function,	function( oResponse ){}
		//	RETURN		- error id
		//
		var nRet;
		var sMethod;
		var sUrl;
		var oData;
		var nTimeout;
		var sVersion;
		var bASync;
		var oHeader;
		var oResponse;

		if ( ! m_oThis.IsValidParam( arrParam, true ) || ! m_cLib.IsFunction( pfnCallback ) )
		{
			return VDATA.ERROR.PARAMETER;
		}

		//	...
		nRet = VDATA.ERROR.UNKNOWN;

		//	...
		sMethod		= m_oThis.GetSafeMethod( _GetSafeValue( arrParam, 'method', VDATA.CONST.DEFAULT_METHOD ) );
		sUrl		= _GetSafeValue( arrParam, 'url', '' );
		oData		= _GetSafeValue( arrParam, 'data', {} );
		nTimeout	= m_oThis.GetSafeTimeout( _GetSafeValue( arrParam, 'timeout', VDATA.CONST.DEFAULT_TIMEOUT ) );
		sVersion	= m_oThis.GetSafeVersion( _GetSafeValue( arrParam, 'version', VDATA.CONST.DEFAULT_VERSION ) );
		bASync		= Boolean( _GetSafeValue( arrParam, 'async', true ) );

		//	get safe values
		oData		= ( m_cLib.IsObject( oData ) ? oData : {} );

		//	Request header
		oHeader		=
		{
			"Accept"	: ( VDATA.CONST.HTTP_HEADER_VERSION_ACCEPT + sVersion )
		};

		//	response
		oResponse	= m_oThis.GetDefaultVDataObject();

		try
		{
			$.ajax
			({
				method		: sMethod,
				url		: sUrl,
				data		: oData,
				headers		: oHeader,
				dataTpe		: "json",
				cache		: false,
				async		: bASync,
				timeout		: nTimeout,
				xhrFields:
				{
					withCredentials : true
				},
				beforeSend	: function( oJqXHR, oSettings )
				{
				},
				success 	: function( oJsonData, sStatus, jqXHR )
				{
					if ( m_oThis.IsValidVData( oJsonData ) )
					{
						oResponse = oJsonData;
					}
					else
					{
						oResponse[ 'errorid' ]		= VDATA.ERROR.JSON;
						oResponse[ 'errordesc' ]	= m_cLib.IsString( sStatus ) ? sStatus : '';
					}

					//
					//	call back
					//
					pfnCallback( oResponse );
				},
				error		: function( oJqXHR, sStatus, sErrorThrown )
				{
					//
					//	oJqXHR		- The jqXHR (in jQuery 1.4.x, XMLHttpRequest) object,
					// 			  a string describing the type of error that occurred and
					// 			  an optional exception object
					//	sStatus		- Possible values (besides null) are
					//			  "timeout", "error", "abort", and "parsererror".
					//	sErrorThrown	- When an HTTP error occurs,
					//			  errorThrown receives the textual portion of the HTTP status,
					//			  such as "Not Found" or "Internal Server Error."
					//

					oResponse[ 'errorid' ]		= VDATA.ERROR.NETWORK;
					oResponse[ 'errordesc' ]	= ( 'status: ' + sStatus + ', errorThrown :' + sErrorThrown );

					//
					//	call back
					//
					pfnCallback( oResponse );
				}
			});

			//	...
			nRet = VDATA.ERROR.SUCCESS;
		}
		catch ( oError )
		{
			nRet = VDATA.ERROR.EXCEPTION;

			//	...
			oResponse[ 'errorid' ]		= VDATA.ERROR.EXCEPTION;
			oResponse[ 'errordesc' ]	= oError.message;

			//
			//	call back
			//
			pfnCallback( oResponse );
		}

		//	...
		return nRet;
	};

	/**
	 *	@return {boolean}
	 *	if the variable being evaluated is a valid json in virtual data format ?
	 */
	this.IsValidVData = function( arrJson )
	{
		return ( arrJson &&
			m_cLib.IsObject( arrJson ) &&
			arrJson.hasOwnProperty( 'errorid' ) &&
			arrJson.hasOwnProperty( 'errordesc' ) &&
			arrJson.hasOwnProperty( 'vdata' ) &&
			$.isNumeric( arrJson[ 'errorid' ] ) &&
			m_cLib.IsString( arrJson[ 'errordesc' ] ) &&
			( $.isArray( arrJson[ 'vdata' ] ) || m_cLib.IsObject( arrJson[ 'vdata' ] ) ) &&
			(
				arrJson.hasOwnProperty( 'json' ) ?
					( m_cLib.IsObject( arrJson[ 'json' ] ) || $.isArray( arrJson[ 'json' ] ) )
					:
					true
			)
		);
	};

	/**
	 *	@return {object}
	 */
	this.GetDefaultVDataObject = function()
	{
		return {
			'errorid'	: VDATA.ERROR.UNKNOWN,
			'errordesc'	: '',
			'vdata'		: {},
			'version'	: VDATA.CONST.DEFAULT_VERSION,
			'json'		: {}
		};
	};

	/**
	 *	@return {boolean}
	 */
	this.IsValidParam = function( arrParam, bCheckMore )
	{
		//
		//	arrParam
		//		'method'	: string,	'GET', 'POST', ...
		//		'url'		: string,	url
		//		'data'		: string,array,	appended as parameters to url for GET
		//						appended as body for POST and others
		//		'version'	: string,	service version required by client
		//		'timeout'	: int,		timeout (in milliseconds) for the request
		//		'cookie'	: string/array,	cookies in string or array
		//		'headers'	: array,	HTTP request header list, like this:
		//						name1: value1
		//						name2: value2
		var bRet;

		//	...
		bRet = false;

		if ( arrParam &&
			m_cLib.IsObject( arrParam ) &&
			arrParam.hasOwnProperty( 'url' ) &&
			m_cLib.IsString( arrParam[ 'url' ] ) &&
			arrParam[ 'url'].length > 0 )
		{
			if ( bCheckMore )
			{
				if ( arrParam.hasOwnProperty( 'method' ) &&
					m_cLib.IsString( arrParam[ 'method' ] ) )
				{
					bRet = true;
				}
			}
			else
			{
				bRet = true;
			}
		}

		return bRet;
	};

	/**
	 *	@return {boolean}
	 */
	this.IsSupportedMethod = function( sMethod )
	{
		if ( ! m_cLib.IsString( sMethod ) || 0 == sMethod.length )
		{
			return false;
		}

		//	...
		sMethod	= $.trim( sMethod ).toUpperCase();

		//	...
		return ( VDATA.CONST.HTTP_SUPPORTED_METHODS.hasOwnProperty( sMethod ) &&
		VDATA.CONST.HTTP_SUPPORTED_METHODS[ sMethod ] );
	};

	/**
	 *	@return {string}
	 */
	this.GetSafeMethod = function( sMethod )
	{
		if ( ! m_cLib.IsString( sMethod ) || 0 == sMethod.length )
		{
			return VDATA.CONST.DEFAULT_METHOD;
		}

		//	...
		var sRet;

		//	...
		sMethod	= $.trim( sMethod ).toUpperCase();
		if ( m_oThis.IsSupportedMethod( sMethod ) )
		{
			sRet = sMethod;
		}
		else
		{
			sRet = VDATA.CONST.DEFAULT_METHOD;
		}

		return sRet;
	};

	/**
	 *	@return {string}
	 */
	this.GetSafeVersion = function( sVersion )
	{
		if ( ! m_cLib.IsString( sVersion ) || 0 == sVersion.length )
		{
			return VDATA.CONST.DEFAULT_VERSION;
		}

		//	...
		var sRet;

		//	...
		sVersion = $.trim( sVersion );
		if ( m_cLib.IsString( sVersion ) && sVersion.length > 0 )
		{
			sRet = sVersion;
		}
		else
		{
			sRet = VDATA.CONST.DEFAULT_VERSION;
		}

		return sRet;
	};

	/**
	 *	@return {number}
	 *	timeout (in milliseconds) for the request
	 */
	this.GetSafeTimeout = function( nTimeout )
	{
		return ( $.isNumber( nTimeout ) && nTimeout > 0 ) ? parseInt( nTimeout ) : VDATA.CONST.DEFAULT_TIMEOUT;
	};



	////////////////////////////////////////////////////////////////////////////////
	//	Private
	//

	/**
	 *	@return {*}
	 */
	function _Construct()
	{
	}

	/**
	 *	@return {*}
	 */
	function _GetSafeValue( oData, sKey, vDefaultValue )
	{
		var vRet;

		if ( ! m_cLib.IsObject( oData ) || ! m_cLib.IsString( sKey ) )
		{
			return vDefaultValue;
		}

		//	...
		vRet = vDefaultValue;

		if ( oData.hasOwnProperty( sKey ) )
		{
			vRet = oData[ sKey ];
		}

		return vRet;
	}


	//
	//	construct
	//
	_Construct();
}



/**
 *	defines VDATA
 */
var window	= window || {};
var VDATA	= window.VDATA || ( window.VDATA = new CVDataCore() );

//
//	error ids of VData
//
VDATA.ERROR	=
{
	//
	//	common error codes
	//
	SUCCESS			: 0,            //      successfully

	USER_START		: 10000,	//	start of user customized error id
	USER_END		: 99999,	//	end of user customized error id

	UNKNOWN			: -100000,      //      unknown errors
	ACCESS_DENIED		: -100001,      //      access denied
	PARAMETER		: -100002,      //      error in parameters
	PERMISSION		: -100003,      //      error in permission
	EXPIRED			: -100004,      //      error in expired
	NOT_LOGGEDIN		: -100005,      //      error in not logged in
	FAILED_LOGGEDIN		: -100006,      //      error in failed logged in

	CREATE_INSTANCE		: -100010,      //      error in creating instance
	EXCEPTION		: -100011,	//	error in exception

	DB_SELECT		: -100050,	//	error in selecting database
	DB_UPDATE		: -100051,	//	error in updating database
	DB_INSERT		: -100052,	//	error in inserting database
	DB_DELETE		: -100053,	//	error in deleting database
	DB_DROP			: -100054,	//	error in dropping database
	DB_TRANSACTION		: -100060,	//	error in transaction
	DB_TABLE_NAME		: -100065,	//	error in table name

	REQUEST_VIA_IP	: -100100,	//	bad request via ip request

	NETWORK		: -100300,	//	error network
	JSON		: -100301,	//	error json
	JSON_ERRORID	: -100302,	//	error json.errorid
	JSON_ERRORDESC	: -100303,	//	error json.errordesc
	JSON_VDATA	: -100304	//	error json.vdata
};

//
//	constants of VData
//
VDATA.CONST	=
{
	HTTP_HEADER_VERSION_ACCEPT	: 'application/vdata+json+version:',

	//
	//	supported methods
	//
	HTTP_SUPPORTED_METHODS		: { 'GET' : true, 'POST' : true, 'DELETE' : true, 'PUT' : true },

	//
	//	default values
	//
	DEFAULT_VERSION			: '1.0',	//	default version
	DEFAULT_METHOD			: 'GET',	//	default HTTP request method
	DEFAULT_TIMEOUT			: 5 * 1000	//	timeout in milliseconds, default value is 5 seconds
};



//
//	exports for node.js
//
if ( "object" === typeof module && "object" === typeof module.exports )
{
	module.exports.VDATA = VDATA;
}



