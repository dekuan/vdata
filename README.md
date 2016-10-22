# vdata
vdata protocol is a perfectly total solution for HTTP RPC Call. It's free and open source. And, it's the best way for calling and responding HTTP services. 
With pretty vdata, you can easily call HTTP services with required version and timeout control. And definitely, Cross-origin resource sharing(CORS) call is okay. vdata client were now wrote in PHP and Javascript, for more features, please view [documentation](http://vdata.dekuan.org/docs).


## Features

* Defines the data protocol for HTTP/HTTPS based service Remote Process Call.
* Defines common error codes and the range of user customized error codes.
* Defines common status codes.
* RESTfull api supported.
* Cross-Origin Resource Sharing supported.
* Send version request by HTTP_ACCEPT from client.
* Respond in-service version of service from server.


## Why do we call it vdata?

Truth be told, V is a pretty girl's name. Uh huh, so, we hope vdata would be pretty too for you.



## What vdata looks like?

vdata is JSON-based protocol, when you call a HTTP service played by vdata protocol, you will see the response from server:


```
{
    "name"          : "",       //  string, name of service
    "url"           : "",       //  string, address of service
    "version"       : "1.0",    //  string, version of service
    "errorid"       : 0,        //  numeric,    error id
    "errordesc"     : "",       //  string, desciption of error
    "vdata"         : {}        //  user customized data.
                                //      it might be an array or an object with
                                //      contents of a string, number, array or object.
}
```


## For more information

Please view [documentation](http://vdata.dekuan.org/docs)




