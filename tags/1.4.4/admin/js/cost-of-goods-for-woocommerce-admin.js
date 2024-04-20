(function (jQuery) {
    jQuery.fn.inputFilter = function (callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function (e) {
            if (callback(this.value)) {
                // Accepted value
                if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                    jQuery(this).removeClass("input-error");
                    this.setCustomValidity("");
                }
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                jQuery(this).addClass("input-error");
                this.setCustomValidity(errMsg);
                this.reportValidity();
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                // Rejected value - nothing to restore
                this.value = "";
            }
        });
    };

    jQuery('input#cost_of_goods').bind('keyup keypress blur', function () {

        jQuery(this).inputFilter(function (value) {
            // Updated regular expression to allow numbers with decimals
            return /^\d*\.?\d*$/.test(value);
        }, "Only price numbers allowed (For example: 20.50)");

        var profit_field = jQuery('input#profit');
        var regular_price = parseFloat(jQuery('input#_regular_price').val());
        var dInput = parseFloat(this.value);
        if (isNaN(dInput)) {
            profit_field.val('0');
        } else {
            let final_profit_price = regular_price - dInput;
            profit_field.val(final_profit_price.toFixed(2)); // Ensure the result is formatted as a price
        }
    });

    jQuery('#woocommerce-product-data').on('woocommerce_variations_loaded', function (event) {

        jQuery('.woocommerce_variation').each(function (index, o) {

            let regular_price_field = jQuery('#variable_regular_price_' + index);
            let cost_of_goods_field = jQuery('#cost_of_goods_' + index);
            let profit_field = jQuery('#profit_' + index);

            jQuery(cost_of_goods_field).each(function (i, o) {

                jQuery(this).bind('keyup keypress blur', function () {

                    jQuery(this).inputFilter(function (value) {
                        // Updated regular expression to allow numbers with decimals
                        return /^\d*\.?\d*$/.test(value);
                    }, "Only price numbers allowed (For example: 20.50)");

                    var regular_price = parseFloat(regular_price_field.val());
                    var dInput = parseFloat(this.value);
                    if (isNaN(dInput)) {
                        profit_field.val('0');
                    } else {
                        let final_profit_price = regular_price - dInput;
                        profit_field.val(final_profit_price.toFixed(2)); // Ensure the result is formatted as a price
                    }
                });

            });

        });

    });

}(jQuery));