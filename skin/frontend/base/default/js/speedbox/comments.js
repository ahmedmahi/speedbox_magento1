 document.observe("dom:loaded", function() {
     $$('.order-comments .order-about dd').each(function(e) {
         e.replace(htmlDecode(e.innerHTML));
     });
 });

 function htmlDecode(input) {
     var d = document.createElement('div');
     d.innerHTML = input;
     return d.childNodes[0].nodeValue;
 }