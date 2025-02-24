<script type="text/javascript">
    function fp_removeParam(key, sourceURL) {
        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }
            rtn = rtn + "?" + params_arr.join("&");
        }
        return rtn;
    }
    function fp_trimChar(string, charToRemove) {
        while(string.charAt(0)==charToRemove) {
            string = string.substring(1);
        }

        while(string.charAt(string.length-1)==charToRemove) {
            string = string.substring(0,string.length-1);
        }

        return string;
    }
    if ( typeof window.history.pushState == 'function' ) {
        var url = document.location.href;
        url = fp_removeParam('refresh', url);
        url = fp_removeParam('state', url);
        url = fp_trimChar(url,'?');
        window.history.pushState({}, "", url);
    }

</script>
