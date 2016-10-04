# vdata
VDATA protocol is the best way to call or respond a HTTP/HTTPS service.


## Features

* Defines the data protocol for HTTP/HTTPS based service Remote Process Call.
* Defines common error codes and the range of user customized error codes.
* Defines common status codes.
* RESTfull api supported.
* Cross-Origin Resource Sharing supported.
* Send version request by HTTP_ACCEPT from client.
* Respond inservice version of service from server.



## Data Protocol/Format

```
{
	"name"			: "",		//	string,	name of service
	"url"			: "",		//	string,	address of service
	"version"		: "1.0",	//	string,	version of service
	"errorid"		: 0,		//	numeric,	error id
	"errordesc"		: "",		//	string,	desciption of error
	"parents"		: {},		//	array,	parent nodes
	"vdata"			: {}		//	user customized data. it might be a string, number, array or an object
}
```


## Comnon error codes

| error defines	| error code	| remark	|
| ------------ | ------------- | ------------ |
| ERROR_SUCCESS			| 0			| successfully	|
| 				| 			| 		|
| ERROR_UNKNOWN			| -100000		| unknown errors	|
| ERROR_ACCESS_DENIED		| -100001		| access denied	|
| ERROR_PARAMETER		| -100002		| error in parameters	|
| ERROR_PERMISSION		| -100003		| error in permission	|
| ERROR_EXPIRED			| -100004		| error in expired	|
| ERROR_NOT_LOGGEDIN		| -100005		| error in not logged in	|
| ERROR_FAILED_LOGGEDIN		| -100006		| error in failed logged in	|
| 				| 			| 		|
| ERROR_CREATE_INSTANCE		| -100010		| error in creating instance	|
| ERROR_EXCEPTION		| -100011		| error in exception	|
| 				| 			| 		|
| ERROR_DB_SELECT		| -100050		| error in selecting database	|
| ERROR_DB_UPDATE		| -100051		| error in updating database	|
| ERROR_DB_INSERT		| -100052		| error in inserting database	|
| ERROR_DB_DELETE		| -100053		| error in deleting database	|
| ERROR_DB_DROP			| -100054		| error in dropping database	|
| ERROR_DB_TRANSACTION		| -100060		| error in transaction	|
| ERROR_DB_TABLE_NAME		| -100065		| error in table name	|
| 				| 			| 	|
| ERROR_REQUEST_VIA_IP		| -100100		| bad request via ip request	|
| 				| 			| 		|
| ERROR_MO_NOT_ENOUGH_COINS	| -100200		| not enough coins	|
| ERROR_MO_HIRE_OVERDUE		| -100201		| the hiring date is overdue	|
| ERROR_MO_TRANSACTION_TYPE	| -100202		| error consume type	|
| 				| 			| 	|
| ERROR_NETWORK			| -100300		| error network	|
| ERROR_JSON			| -100301		| error json	|
| ERROR_JSON_ERRORID		| -100302		| error json.errorid	|
| ERROR_JSON_ERRORDESC		| -100303		| error json.errordesc	|
| ERROR_JSON_VDATA		| -100304		| error json.vdata	|



## the range of user customized error codes

| error defines	| error code	| remark	|
| ------------ | ------------- | ------------ |
| 				| 			| 		|
| ERROR_USER_START		| 10000			| start of user customized error id	|
| ERROR_USER_END		| 99999			| end of user customized error id



## Cross-Origin Resource Sharing
```
use dekuan\vdata\CResponse;


$cResponse	= CResponse::GetInstance();

//
//	requests from *.domain1.com will be allowed
//
$cResponse->SetCorsDomains( [ '.domain1.com' ] );

//	...

```


## Send Request to Server



```
use dekuan\vdata\CConst;
use dekuan\vdata\CRequest;


$cRequest	= CRequest::GetInstance();
$arrResponse	= [];
$nCall		= $cRequest->Post
(
	[
		'url'		=> 'http://account.xs.cn/api/login',
		'data'		=>
		[
			'u_name'	=> 'username',
			'u_pwd'		=> 'password',
			'u_keep'	=> 1
		],
		'version'	=> '1.0',
		'timeout'	=> 30,		//	timeout in seconds
		'cookie'	=> [],		//	array or string are both okay.
		'headers'	=> [],
	],
	$arrResponse
);
if ( CConst::ERROR_SUCCESS == $nCall &&
	$cRequest->IsValidVData( $arrResponse ) )
{
	print_r( $arrResponse );
}


```



## Respond to Client

#### 1, Respond by json encoded string

```
use dekuan\vdata\CResponse;


$cResponse	= CResponse::GetInstance();

$cResponse->SetServiceName( 'Test of responding array VData' );
$cResponse->SetServiceUrl( 'http://www.ladep.cn/' );
$arrVData	= $cResponse->GetVDataString
(
	0,
	"error desc",
	[ "info" => "User customized info" ],
	$cResponse->GetDefaultVersion()
);

echo( $arrVData );


```


#### 2, Respond by json array

```
use dekuan\vdata\CResponse;


$cResponse	= CResponse::GetInstance();

$cResponse->SetServiceName( 'Test of responding array VData' );
$cResponse->SetServiceUrl( 'http://www.ladep.cn/' );
$arrVData	= $cResponse->GetVDataArray
(
	0,
	"error desc",
	[ "info" => "User customized info" ],
	$cResponse->GetDefaultVersion()
);

print_r( $arrVData );


```


#### 3, Respond by Laravel response instance

```
use dekuan\vdata\CResponse;


$cResponse	= CResponse::GetInstance();

$cResponse->SetServiceName( 'Test of responding array VData' );
$cResponse->SetServiceUrl( 'http://www.ladep.cn/' );
$nCall	= $cResponse->Send
(
	0,
	"error desc",
	[ "info" => "User customized info" ],
	$cResponse->GetDefaultVersion()
);


```







