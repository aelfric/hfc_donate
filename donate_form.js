jQuery(document).ready( function(){
   jQuery('#donate_form').submit(function(event){
      var name = jQuery('#custom').val()
      jQuery('#custom').val(jQuery('#donation_type').val() + ':' + name); 
   });
});
