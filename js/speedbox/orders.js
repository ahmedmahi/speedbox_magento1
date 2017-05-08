 document.observe("dom:loaded", function() {
     addWeight();
 });

 function addWeight() {
     var d = document.getElementById("speedbox_adminhtml_order_grid_table").children[2];
     if (d) {
         for (var i = 0; i < d.children.length; i++) {
             var orderids = document.getElementsByName("order_ids")[i].value;
             var weight = document.getElementsByName("weight")[i + 2].value;
             document.getElementsByName("weight")[i + 2].onchange = function() {
                 changeWeight(this.value, this.parentNode.parentNode.childNodes[1].childNodes[1].value.split("-")[0]);
             };
             document.getElementsByName("weight")[i + 2].onkeyup = function() {
                 validateWeight(this);
             };
             document.getElementById("speedbox_adminhtml_order_grid_massaction-form").children[0].innerHTML += '<input name="weight_' + orderids + '" type="hidden" value="' + orderids + '-' + weight + '" />';
         }
     }
 }

 function validateWeight(th) {
     var valnum = "0123456789.";
     var caract = "";
     for (i = 0; i < th.value.length; i++) {
         x = th.value.charAt(i);
         if (valnum.indexOf(x, 0) != -1) caract += x;
     }
     th.value = caract;
 }

 function changeWeight(weight, orderid) {
     var welement = document.getElementsByName("weight_" + orderid)[0].value.split("-");
     welement[1] = weight;
     document.getElementsByName("weight_" + orderid)[0].value = welement.join("-");
 }