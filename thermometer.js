//originally from [url]http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript[/url]
function formatCurrency(n, c, d, t) {
    "use strict";

    var s, i, j;

    c = Math.abs(c);
    c = isNaN(c) ? 2 : c;
    d = d === undefined ? "." : d;
    t = t === undefined ? "," : t;

    s = n < 0 ? "-" : "";
    n = Math.abs(+n || 0).toFixed(c);
    i = parseInt(n, 10).toString();
    j = i.length;
    j = (j > 3) ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
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
