//originally from [url]http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript[/url]
function formatCurrency(amount, decimal_sep, thousands_sep) {
    "use strict";

    var sign,
        amount_string,
        num_digits,
        precision = 2;

    decimal_sep = decimal_sep === undefined ? "." : decimal_sep;
    thousands_sep = thousands_sep === undefined ? "," : thousands_sep;

    sign = amount < 0 ? "-" : "";
    amount = Math.abs(+amount || 0).toFixed(precision);
    amount_string = parseInt(amount, 10).toString();
    num_digits = amount_string.length;
    num_digits = (num_digits > 3) ? num_digits % 3 : 0;

    return (sign +
            (num_digits ? amount_string.substr(0, num_digits) + thousands_sep : "") +
            amount_string.substr(num_digits).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep) +
            (decimal_sep + Math.abs(amount - amount_string).toFixed(precision).slice(2)));
}

/**
 * Thermometer Progress meter.
 * This function will update the progress element in the "thermometer"
 * to the updated percentage.
 * If no parameters are passed in it will read them from the DOM
 *
 * @param {Number} goalAmount The Goal amount, this represents the 100% mark
 * @param {Number} progressAmount The progress amount is the current amount
 * @param {Boolean} animate Whether to animate the height or not
 *
 */
function thermometer(goalAmount, progressAmount, animate) {
    "use strict";

    //set up our vars and cache some jQuery objects    
    var $thermo = jQuery("#thermometer"),
        $progress = jQuery(".progress", $thermo),
        $goal = jQuery(".goal", $thermo),
        percentageAmount;

    //work out our numbers        
    goalAmount = goalAmount || parseFloat($goal.text());
    progressAmount = progressAmount || parseFloat($progress.text());
    percentageAmount = Math.min((progressAmount / goalAmount * 100).toFixed(1), 100);
    //make sure we have 1 decimal point

    //let's format the numbers and put them back in the DOM
    $goal.find(".amount").text("$" + formatCurrency(goalAmount));
    $progress.find(".amount").text("$" + formatCurrency(progressAmount));

    //let's set the progress indicator

    $progress.find(".amount").hide();

    if (animate !== false) {
        $progress.animate({
            "height": percentageAmount + "%"
        }, 1200, function () {
            jQuery(this).find(".amount").fadeIn(500);
        });
    } else { // we don't always want to animate
        $progress.css({
            "height": percentageAmount + "%"
        });
        $progress.find(".amount").fadeIn(500);
    }
}


jQuery(document).ready(function () {
    "use strict";

    //call without the parameters to have it read from the DOM
    thermometer();

    // or with parameters if you want to update it using JavaScript.
    // you can update it live, and choose whether to show the animation
    // (which you might not if the updates are relatively small)
    //thermometer( 1000000, 425610, false );

});
