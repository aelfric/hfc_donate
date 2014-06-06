jQuery(document).ready( function(){
   jQuery('#donate_form').submit(function(event){
      alert( "Handler for .submit() called." );
      var name = jQuery('#custom').val()
      jQuery('#custom').val(jQuery('#donation_type').val() + ':' + name); 
      event.preventDefault();
   });
});
