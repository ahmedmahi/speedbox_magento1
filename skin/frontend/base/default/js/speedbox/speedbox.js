/**
 * @category    S3ibusiness
 * @package     S3ibusiness_Speedbox
 * @author      Speedbox ( http://www.speedbox.ma)
 * @developer   Ahmed MAHI <1hmedmahi@gmail.com> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
Varien.Speedbox = Class.create();
Varien.Speedbox.prototype = {
    initialize: function(ajaxurl, ajaxloader) {
        this.ajaxurl = ajaxurl;
        this.ajaxloader = ajaxloader;
        this.methodradioButton = 's_method_speedbox_speedbox';
        this.divconteneur = 'speedbox_relais';
    },
    SetCity: function(city) {
        this.city = city;
        this.method = $(this.methodradioButton).up('li');
        this.AddDivPR();
        this.addButtonEven(this);
    },
    AddDivPR: function() {
        divelemt = '<span id="speedbox_please_wait" class="please-wait" style="display:none;"><img src="' + this.ajaxloader + '" class="v-middle" /> Chargement points relais...</span><div id="' + this.divconteneur + '"></div>';
        this.method.replace(this.method.innerHTML + divelemt);
        $(this.divconteneur).hide();
        //this.method.innerHTML;
    },
    addButtonEven: function(thisclass) {
        $$("input[id*='s_method_']").each(function(_method) {
            $(_method).observe('change', function(event) {
                thisclass.speedboxCheck();
            });
        });
    },
    speedboxCheck: function() {
        if ($$("input[id*='" + this.methodradioButton + "']").length != 0) {
            if ($(this.methodradioButton).checked) {
                $(this.divconteneur).show();
                this.getPointsRelais();
            } else {
                $(this.divconteneur).hide();
            }
        }
    },
    getPointsRelais: function() {
        this.ajaxPointsRelais();
    },
    ajaxPointsRelais: function(setRequest = false, PR_id = '', PR_infos = '') {
        param = (setRequest) ? 'PR_id=' + PR_id + '&PR_infos=' + PR_infos : 'city=' + this.city;
        $('speedbox_please_wait').show();
        thisclass = this;
        new Ajax.Request(this.ajaxurl, {
            method: 'post',
            onSuccess: function(transport) {
                var response = transport.responseText || "no response text";
                if (setRequest) {
                    $('speedbox_relais_selected').update(response);
                } else {
                    $(thisclass.divconteneur).update(response);
                    thisclass.checkOnePoint();
                }
                $('speedbox_please_wait').hide()
            },
            onFailure: function() {
                alert('Something went wrong...');
            },
            //onComplete: $('speedbox_please_wait').hide(),
            parameters: param,
        });
    },
    checkOnePoint: function() {
        radiochecked = '';
        if (typeof relais_data != "undefined" && $(relais_data.relay_id) != "undefined" && $(relais_data.relay_id) != null) {
            $(relais_data.relay_id).checked = 1;
            radiochecked = relais_data.relay_id;
        }
        if ($$("input[name='sb_relay_id']:checked").length == 0) {
            $$("input[name='sb_relay_id']")[0].checked = 1;
            radiochecked = $$("input[name='sb_relay_id']")[0].id;
        }
        if (radiochecked != '') {
            this.write_point_relais_vlues(radiochecked);
        }
    },
    popup_speedbox_view: function(baseurl, id, mapid, lat, longti, thiscla = this) {
        $('sb_relais_filter').show();
        $(id).show();
        window.setTimeout(function() {
            thiscla.init_google_maps(baseurl, mapid, lat, longti)
        }, 200);
    },
    init_google_maps: function(baseurl, mapid, lat, longti) {
        var latlng = new google.maps.LatLng(lat, longti);
        var myOptions = {
            zoom: 16,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
        };
        var map = new google.maps.Map(document.getElementById(mapid), myOptions);
        var marker = new google.maps.Marker({
            icon: baseurl,
            position: latlng,
            animation: google.maps.Animation.DROP,
            map: map
        });
    },
    hidePopup: function() {
        $('sb_relais_filter').hide();
        $$('.sb_relaisbox').each(function(relaisbox) {
            $(relaisbox).hide();
        });
    },
    write_point_relais_vlues: function(item) {
        value = $(item).readAttribute('value');
        relais_data = JSON.parse(value);
        infos = '{"relay_id":"' + relais_data.relay_id + '","shop_name":"' + relais_data.shop_name + '","address":"' + relais_data.address + '","city":"' + relais_data.city + '"}';
        this.ajaxPointsRelais(true, $(item).readAttribute('id'), infos);
    },
}
Varien.speedboxCity = Class.create();
Varien.speedboxCity.prototype = {
    initialize: function(options, selected) {
        this.shipping_city = $('shipping:city');
        this.billing_city = $('billing:city');
        this.changeCityToSelect('shipping', options, selected);
        this.changeCityToSelect('billing', options, selected);
    },
    changeCity: function(field) {
        $(field).replace('<input type="text" title="City" name="shipping[city]" value="" class="input-text  required-entry" id="shipping:city" onchange="shipping.setSameAsBilling(false);"> <div id="city_autocomplete" class="autocomplete"></div>');
        $(field).innerHTML;
    },
    changeCityToSelect: function(field, options, selected) {
        if (field == 'billing') {
            this.billing_city.replace(this.generateBillingCity(options, selected));
            this.billing_city.innerHTML;
        }
        if (field == 'shipping') {
            this.shipping_city.replace(this.generateShippingCity(options, selected));
            this.shipping_city.innerHTML;
        }
    },
    generateSelectOptionsHtml: function(options, selected) {
        var html = '';
        var selectedHtml;
        for (var key in options) {
            var value = options[key];
            if (selected instanceof Array) {
                if (selected.indexOf(key) != -1) {
                    selectedHtml = ' selected="selected"';
                } else {
                    selectedHtml = '';
                }
            } else {
                if (key == selected) {
                    selectedHtml = ' selected="selected"';
                } else {
                    selectedHtml = '';
                }
            }
            html += '<option value="' + key + '"' + selectedHtml + '>' + value + '</option>';
        }
        html += '</select>';
        return html;
    },
    generateShippingCity: function(options, selected) {
        var html = '<select name="shipping[city]" id="shipping:city" class="validate-select" title="City" >';
        html += this.generateSelectOptionsHtml(options, selected);
        return html;
    },
    generateBillingCity: function(options, selected) {
        var html = '<select name="billing[city]" id="billing:city" class="validate-select" title="City" >';
        html += this.generateSelectOptionsHtml(options, selected);
        return html;
    },
    initAutocomplete: function(url, destinationElement) {
        new Ajax.Autocompleter(this.field, destinationElement, url, {
            paramName: this.field.name,
            method: 'get',
            minChars: 2,
            updateElement: this._selectAutocompleteItem.bind(this),
            onShow: function(element, update) {
                if (!update.style.position || update.style.position == 'absolute') {
                    update.style.position = 'absolute';
                    Position.clone(element, update, {
                        setHeight: false,
                        offsetTop: element.offsetHeight
                    });
                }
                Effect.Appear(update, {
                    duration: 0
                });
            }
        });
    },
    _selectAutocompleteItem: function(element) {
        if (element.title) {
            this.field.value = element.title;
        }
    }
};