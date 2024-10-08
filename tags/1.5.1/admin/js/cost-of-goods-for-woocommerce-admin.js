(function (jQuery) {
    jQuery.fn.inputFilter = function (callback, errMsg) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function (e) {
            if (callback(this.value)) {
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
                this.value = "";
            }
        });
    };

    function calculateProfit() {
        var costOfGoods = parseFloat(jQuery('input#cost_of_goods').val());
        var profitField = jQuery('input#profit');
        var regularPrice = parseFloat(jQuery('input#_regular_price').val());
        var rewriteRegularPriceChecked = jQuery('input#rewrite_regular_price').is(':checked');
        var regularPriceVat = rewriteRegularPriceChecked ? parseFloat(jQuery('input#regular_price_vat').val()) : NaN;

        if (!isNaN(regularPriceVat) && rewriteRegularPriceChecked) {
            // Use the Regular Price - VAT amount
            regularPrice = regularPriceVat;
        }

        if (isNaN(costOfGoods) || isNaN(regularPrice)) {
            profitField.val('0');
        } else {
            let profit = regularPrice - costOfGoods;
            profitField.val(profit.toFixed(2)); // Ensure the result is formatted as a price
        }
    }

    jQuery('input#cost_of_goods, input#regular_price_vat, input#rewrite_regular_price').on('keyup keypress blur change', function () {
        jQuery(this).inputFilter(function (value) {
            return /^\d*\.?\d*$/.test(value);
        }, "Only numeric values are allowed (e.g., 20.50)");

        calculateProfit();
    });

    jQuery('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {
        jQuery('.woocommerce_variation').each(function (index) {
            let regularPriceField = jQuery('#variable_regular_price_' + index);
            let costOfGoodsField = jQuery('#cost_of_goods_' + index);
            let profitField = jQuery('#profit_' + index);

            jQuery(costOfGoodsField).on('keyup keypress blur change', function () {
                jQuery(this).inputFilter(function (value) {
                    return /^\d*\.?\d*$/.test(value);
                }, "Only numeric values are allowed (e.g., 20.50)");

            });
        });
    });
}(jQuery));
