# vdata
VDATA protocol is the best way to call or respond a HTTP service.



### Format

```
{
	"name"			: "",		//	string,	name of service
	"url"			: "",		//	string,	address of service
	"version"		: "1.0",	//	string,	version of service 
	"errorid"		: 0,		//	numeric,	error id
	"errordesc"		: "",		//	string,	desciption of error
	"parents"		: {},		//	array,	parent nodes
	"vdata"			: {}		//	user customized data
}
```