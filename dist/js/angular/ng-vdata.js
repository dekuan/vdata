/********************************************************************************
 *
 *	VData Client by DeKuan, Inc
 *	www.dekuan.org
 *
 ********************************************************************************/
angular.module
(
	'vdata',
	[]
)
.constant
(
	'vdataConst',
	{
		ERROR :
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

			REQUEST_VIA_IP		: -100100,	//	bad request via ip request

			NETWORK			: -100300,	//	error network
			JSON			: -100301,	//	error json
			JSON_ERRORID		: -100302,	//	error json.errorid
			JSON_ERRORDESC		: -100303,	//	error json.errordesc
			JSON_VDATA		: -100304	//	error json.vdata
		},
		CONST :
		{
			//
			//	constants of VData
			//
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
		}
	}
)
.factory
(
	'vdataFactory',
	[
		'$rootScope',
		'$http',
		'$filter',
		'$httpParamSerializer',
		'vdataConst',
		function( $rootScope, $http, $filter, $httpParamSerializer, vdataConst )
	{
		return {

			/**
			 *	@return {number}
			 *	HTTP Get
			 */
			Get : function( arrParam, pfnCallback )
			{
				if ( ! this.IsValidParam( arrParam ) || ! angular.isFunction( pfnCallback ) )
				{
					return false;
				}

				//	...
				arrParam[ 'method' ] = 'GET';
				return this.Http( arrParam, pfnCallback );
			},

			/**
			 *	@return {number}
			 *	HTTP Post
			 */
			Post : function( arrParam, pfnCallback )
			{
				if ( ! this.IsValidParam( arrParam ) || ! angular.isFunction( pfnCallback ) )
				{
					return vdataConst.ERROR.PARAMETER;
				}

				//	...
				arrParam[ 'method' ] = 'POST';
				return this.Http( arrParam, pfnCallback );
			},

			/**
			 *	@return {number}
			 *	HTTP Put
			 */
			Put : function( arrParam, pfnCallback )
			{
				if ( ! this.IsValidParam( arrParam ) || ! angular.isFunction( pfnCallback ) )
				{
					return vdataConst.ERROR.PARAMETER;
				}

				//	...
				arrParam[ 'method' ] = 'PUT';
				return this.Http( arrParam, pfnCallback );
			},

			/**
			 *	@return {number}
			 *	HTTP Put
			 */
			Delete : function( arrParam, pfnCallback )
			{
				if ( ! this.IsValidParam( arrParam ) || ! angular.isFunction( pfnCallback ) )
				{
					return vdataConst.ERROR.PARAMETER;
				}

				//	...
				arrParam[ 'method' ] = 'DELETE';
				return this.Http( arrParam, pfnCallback );
			},

			/**
			 *	@return {number}
			 *	HTTP Get
			 */
			Http : function( arrParam, pfnCallback )
			{
				//
				//	arrParam	- object,	see .IsValidParam()
				//	pfnCallback	- function,	function( oResponse ){}
				//	RETURN		- error id
				//
				var oParent	= this;

				var nRet;
				var sMethod;
				var sUrl;
				var oData;
				var nTimeout;
				var sVersion;
				var bASync;
				var oHeader;
				var oResponse;
				var bUpdateRequest;
				var oSettings;

				if ( ! this.IsValidParam( arrParam, true ) || ! angular.isFunction( pfnCallback ) )
				{
					return vdataConst.ERROR.PARAMETER;
				}

				//	...
				nRet = vdataConst.ERROR.UNKNOWN;

				//	...
				sMethod		= this.GetSafeMethod( this.GetSafeValue( arrParam, 'method', vdataConst.CONST.DEFAULT_METHOD ) );
				sUrl		= this.GetSafeValue( arrParam, 'url', '' );
				oData		= this.GetSafeValue( arrParam, 'data', {} );
				nTimeout	= this.GetSafeTimeout( this.GetSafeValue( arrParam, 'timeout', vdataConst.CONST.DEFAULT_TIMEOUT ) );
				sVersion	= this.GetSafeVersion( this.GetSafeValue( arrParam, 'version', vdataConst.CONST.DEFAULT_VERSION ) );
				bASync		= Boolean( this.GetSafeValue( arrParam, 'async', true ) );

				//	get safe values
				oData		= ( angular.isObject( oData ) ? oData : {} );

				//	...
				bUpdateRequest	= false;
				if ( 'POST' == sMethod ||
					'PUT' == sMethod ||
					'DELETE' == sMethod )
				{
					bUpdateRequest = true;
				}

				//	Request header
				oHeader	=
				{
					"Accept"	: ( vdataConst.CONST.HTTP_HEADER_VERSION_ACCEPT + sVersion )
				};
				if ( bUpdateRequest )
				{
					//$http.defaults.headers.post[ "Content-Type" ]	= "text/plain";
					oHeader	= angular.extend( {}, oHeader, { "Content-Type"	: "text/plain" } );
				}

				//	response
				oResponse	= this.GetDefaultVDataObject();

				//	settings
				oSettings	=
				{
					method		: sMethod,
					url		: sUrl,
					dataTpe		: "json",
					cache		: false,
					async		: bASync,
					withCredentials	: true,
					timeout		: this.GetSafeTimeout( nTimeout ),	//	timeout in milliseconds
					headers		: oHeader
				};

				//
				//	GET
				//	POST, PUT, DELETE
				//
				oSettings = angular.extend( {}, oSettings, { params : oData } );

				try
				{
					//	...
					$http
					(
						oSettings
					)
					.success ( function( oJsonData, nStatus, oConfig, pfnHeaderGetter )
					{
						if ( oParent.IsValidVData( oJsonData ) )
						{
							oResponse = oJsonData;
						}
						else
						{
							oResponse[ 'errorid' ]		= vdataConst.ERROR.JSON;
							oResponse[ 'errordesc' ]	= ( 'HTTP status: ' + nStatus );
						}

						//
						//	call back
						//
						pfnCallback( oResponse );

					})
					.error( function( oJsonData, nStatus, oConfig, pfnHeaderGetter )
					{
						oResponse[ 'errorid' ]		= vdataConst.ERROR.NETWORK;
						oResponse[ 'errordesc' ]	= ( 'HTTP status: ' + nStatus );

						//
						//	call back
						//
						pfnCallback( oResponse );
					});

					//	...
					nRet = vdataConst.ERROR.SUCCESS;
				}
				catch ( oError )
				{
					nRet = vdataConst.ERROR.EXCEPTION;

					//	...
					oResponse[ 'errorid' ]		= vdataConst.ERROR.EXCEPTION;
					oResponse[ 'errordesc' ]	= oError.message;

					//
					//	call back
					//
					pfnCallback( oResponse );
				}

				//	...
				return nRet;
			},

			/**
			 *	@return {boolean}
			 *	if the variable being evaluated is a valid json in virtual data format ?
			 */
			IsValidVData : function( arrJson )
			{
				return ( arrJson &&
					angular.isObject( arrJson ) &&
					arrJson.hasOwnProperty( 'errorid' ) &&
					arrJson.hasOwnProperty( 'errordesc' ) &&
					arrJson.hasOwnProperty( 'vdata' ) &&
					angular.isNumber( arrJson[ 'errorid' ] ) &&
					angular.isString( arrJson[ 'errordesc' ] ) &&
					( angular.isArray( arrJson[ 'vdata' ] ) || angular.isObject( arrJson[ 'vdata' ] ) ) &&
					(
						arrJson.hasOwnProperty( 'json' ) ?
							( angular.isObject( arrJson[ 'json' ] ) || angular.isArray( arrJson[ 'json' ] ) )
							:
							true
					)
				);
			},

			/**
			 *	@return {object}
			 */
			GetDefaultVDataObject : function()
			{
				return {
					'errorid'	: vdataConst.ERROR.UNKNOWN,
					'errordesc'	: '',
					'vdata'		: {},
					'version'	: vdataConst.CONST.DEFAULT_VERSION,
					'json'		: {}
				};
			},

			/**
			 *	@return {boolean}
			 */
			IsValidParam : function( arrParam, bCheckMore )
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
					angular.isObject( arrParam ) &&
					arrParam.hasOwnProperty( 'url' ) &&
					angular.isString( arrParam[ 'url' ] ) &&
					arrParam[ 'url'].length > 0 )
				{
					if ( bCheckMore )
					{
						if ( arrParam.hasOwnProperty( 'method' ) &&
							angular.isString( arrParam[ 'method' ] ) )
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
			},

			/**
			 *	@return {boolean}
			 */
			IsSupportedMethod : function( sMethod )
			{
				if ( ! angular.isString( sMethod ) || 0 == sMethod.length )
				{
					return false;
				}

				//	...
				//sMethod	= $filter( 'uppercase' )( $.trim( sMethod ) );
				sMethod	= $filter( 'uppercase' )( this.TrimString( sMethod ) );

				//	...
				return ( vdataConst.CONST.HTTP_SUPPORTED_METHODS.hasOwnProperty( sMethod ) &&
				vdataConst.CONST.HTTP_SUPPORTED_METHODS[ sMethod ] );
			},

			/**
			 *	@return {string}
			 */
			GetSafeMethod : function( sMethod )
			{
				if ( ! angular.isString( sMethod ) || 0 == sMethod.length )
				{
					return vdataConst.CONST.DEFAULT_METHOD;
				}

				//	...
				var sRet;

				//	...
				//sMethod	= $filter( 'uppercase' )( $.trim( sMethod ) );
				sMethod	= $filter( 'uppercase' )( this.TrimString( sMethod ) );

				if ( this.IsSupportedMethod( sMethod ) )
				{
					sRet = sMethod;
				}
				else
				{
					sRet = vdataConst.CONST.DEFAULT_METHOD;
				}

				return sRet;
			},

			/**
			 *	@return {string}
			 */
			GetSafeVersion : function( sVersion )
			{
				if ( ! angular.isString( sVersion ) || 0 == sVersion.length )
				{
					return vdataConst.CONST.DEFAULT_VERSION;
				}

				//	...
				var sRet;

				//	...
				sVersion = this.TrimString( sVersion );
				if ( angular.isString( sVersion ) && sVersion.length > 0 )
				{
					sRet = sVersion;
				}
				else
				{
					sRet = vdataConst.CONST.DEFAULT_VERSION;
				}

				return sRet;
			},

			/**
			 *	@return {number}
			 *	timeout (in milliseconds) for the request
			 */
			GetSafeTimeout : function( nTimeout )
			{
				return ( angular.isNumber( nTimeout ) && nTimeout > 0 ) ? parseInt( nTimeout ) : vdataConst.CONST.DEFAULT_TIMEOUT;
			},

			/**
			 *	@return {*}
			 */
			GetSafeValue : function( oData, sKey, vDefaultValue )
			{
				var vRet;

				if ( ! angular.isObject( oData ) || ! angular.isString( sKey ) )
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
			},

			/**
			 *	@return {string}
			 */
			TrimString : function( vValue )
			{
				if ( ! angular.isString( vValue ) )
				{
					return vValue;
				}

				//	you could use .trim, but it's not going to work in IE<9
				return vValue.replace( /^\s+|\s+$/g, '' );
			}
		};
	}]
);

