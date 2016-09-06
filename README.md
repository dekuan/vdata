# vdata
VDATA protocol is the best way to call or respond a HTTP service.



### Format

```
{
	"name"			: "",		//	string,	name of service
	"address"		: "",		//	string,	address of service
	"version"		: "1.0",	//	string,	version of service 
	"errorid"		: 0,		//	numeric,	error id
	"errordesc"		: "",		//	string,	desciption of error
	"parent"		: {},		//	array,	parent nodes
	"vdata"			: {}		//	user customized data
}
```