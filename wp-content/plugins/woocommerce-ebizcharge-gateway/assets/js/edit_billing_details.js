// When done loading: (also alias '$' for 'jQuery' inside this block)
jQuery( document ).ready( function( $ ) {

  $("button[id^='edit-button-'], button[id^='cancel-button-']").click( function() {
    
    var method_number = $(this).attr('id').split('-').pop();

    // Toggle input fields
    $("[id='cc-number-" + method_number + "']").toggle();
    $("[id='edit-cc-number-" + method_number + "']").toggle();
    $("[id='cc-exp-" + method_number + "']").toggle();
    $("[id='edit-cc-exp-" + method_number + "']").toggle();
    $("[id='cvv-label-" + method_number + "']").toggle();
    $("[id='edit-cvv-" + method_number + "']").toggle();
    
    // Toggle buttons
    $("button[id='edit-button-" + method_number + "']" ).toggle();
    $("button[id='cancel-button-" + method_number + "']" ).toggle();
    $("input[id='save-button-" + method_number + "']" ).toggle();
    $("button[id='unlock-delete-button-" + method_number + "']" ).toggle();
  
  });
  
  $("button[id^='unlock-delete-button-'], button[id^='cancel-delete-button-']").click( function() {

    var method_number = $(this).attr('id').split('-').pop();
    // Toggle confirmation message
    $("[id='delete-confirm-msg-" + method_number + "']").toggle();
    
    // Toggle buttons
    $("button[id='unlock-delete-button-" + method_number + "']" ).toggle();
    $("button[id='edit-button-" + method_number + "']" ).toggle();
    $("input[id='delete-button-" + method_number + "']" ).toggle();
    $("button[id='cancel-delete-button-" + method_number + "']" ).toggle();
  
  });
  
  $("#Add-new-method").click( function(){
	  $("#0007t").show();
	  $("#hide-method-bar").hide();
	  $(".woocommerce-error").hide();
	  $(".woocommerce-message").hide();
  });
  
  $("#cancel_button1").click( function(){
	  $("#0007t").hide();
	  $("#hide-method-bar").show();
	  return false;
  });
  
} );