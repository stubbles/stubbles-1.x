/**
 * Namespaces for JSON-RPC
 */
stubbles.json = {};
stubbles.json.rpc = {};
stubbles.json.rpc.appendToUrl = null;
stubbles.json.rpc.serviceUrl  = null;

/**
 * Stubbles JSON-RPC client
 *
 * This class requires the YAHOO User Interface Library
 * available at http://developer.yahoo.com/yui
 *
 * @version  $Id$
 */

/**
 * constuctor
 */
stubbles.json.rpc.Client = function(clientObj) {
    var reqRespMapping = [];
    var finalServiceUrl = null;

    var callback = {
        success: function(o) {
            var rpcObj = JSON.parse(o.responseText);
            for (var i=0; i < reqRespMapping.length; i++) {
                if (rpcObj.id === reqRespMapping[i].id) {
                    var classAndMethod = reqRespMapping[i].method.split(".");
                    console.info("Stubbles::REQID " + rpcObj.id +  " :: Calling callback method callback__" + classAndMethod[1] + "("+rpcObj.id+", "+rpcObj.result+", "+rpcObj.error+")");
                    var methodName = 'callback__' + classAndMethod[1];

                    // call callback method on clientObj
                    if ( rpcObj.error === null ) {
                      clientObj[methodName].call(clientObj, rpcObj.id, rpcObj.result, rpcObj.error);
                    } else {
                      console.error("Stubbles::REQID " + rpcObj.id +  " :: ERROR: " + rpcObj.error);
                      clientObj[methodName].call(clientObj, rpcObj.id, null, rpcObj.error);
                    }
                    return;
                }
            }
            console.error("Stubbles::REQID " + rpcObj.id +  " :: Invalid request id.");
            var errorMsg = 'stubbles: no related request id for response id found - mapping in js obj reqRespMapping failed!';
            clientObj[methodName].call(clientObj, rpcObj.id, null, errorMsg);
        },
        failure: function(o) {
            console.error("Stubbles::REQID n/a :: ERROR: HTTP Request failed.");
            var errorMsg = 'stubbles: callback error due to bad request from service (instead of HTTP status code 200)';
            clientObj[methodName].call(clientObj, rpcObj.id, null, errorMsg);
        }
    };

    this.createId = function() {
        var d = new Date();
        var id = d.getHours() +''+ d.getMinutes() +''+ d.getMilliseconds();
        return id;
    };

    this.doCall = function(classAndMethod, args) {
        var id = this.createId();

        if(stubbles.json.rpc.serviceUrl === null) {
            throw 'stubbles: no service url set via js object \'stubbles.json.rpc.serviceUrl\'';
        } else {
            if (finalServiceUrl === null) {
                finalServiceUrl =  (stubbles.json.rpc.appendToUrl !== null)
                                  ? stubbles.json.rpc.serviceUrl + '&' + stubbles.json.rpc.appendToUrl
                                  : stubbles.json.rpc.serviceUrl;
            }
        }

        // because args is an array-like object and
        // not an array a conversion is needed
        for (var i=0, arr=[]; i < args.length; i++) {
            arr[i] = args[i];
        }

        var jsonRpcReq = {
            method: classAndMethod,
            params: arr,
            id: id
        };

        YAHOO.util.Connect.asyncRequest('POST', finalServiceUrl, callback, JSON.stringify(jsonRpcReq));
        console.info("Stubbles::REQID " + id +  " :: Calling method " + classAndMethod);
        reqRespMapping.push(jsonRpcReq);
        return id;
    };
};
