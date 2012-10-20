/*

以下问题会引起很多隐藏bug：

Array原型prototype扩展引起for in遍历问题，例如：

  Array.prototype.remove = function(...) {
    ...
  }
  a = []
  for (var i in a) {
    ...
  }

会多出一个key：remove，所以只能使用for(var i=0, i<n; i++){}来遍历，
或者直接把remove改为普通函数。

*/

Array.remove = function(arr, name, value) {
    var rest = $.grep(arr, function(item){
        return (item[name] != value);
    });

    arr.length = 0;
    arr.push.apply(arr, rest);

    return arr; // optional
}


function __dump2(obj){
    alert(JSON.stringify(obj));
}


function generateUniqueId(){
  var timer = new Date();
  return (timer.getTime() + Math.random());
}


function truncate(str, len, hasDot) {
    var newLength = 0;
    var newStr = "";
    var singleChar = "";
    var chineseRegex = /[^\x00-\xff]/g;    
    var strLength = str.replace(chineseRegex, "**").length;
    
    for (var i = 0; i < strLength; i++) {
        singleChar = str.charAt(i).toString();

        if( singleChar.match(chineseRegex) != null )
            newLength += 2;
        else        
            newLength++;
        
        if( newLength > len )
            break;
        
        newStr += singleChar;
    }
    
    if( hasDot && strLength > len )
        newStr += "...";    
    
    return newStr;
}

Function.prototype.getMultiLine = function() {
    var lines = new String(this);
    lines = lines.substring(lines.indexOf("/*") + 3, lines.lastIndexOf("*/"));
    return lines;
}
