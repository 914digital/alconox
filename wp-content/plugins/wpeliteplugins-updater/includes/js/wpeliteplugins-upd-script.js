jQuery(document).ready( function($) {
	
	$('.wpeliteplugins-upd-submit-button').on('click', function() {
		
		var error = 0;
		
		$('.wpelitepluginsupd-email-field').each( function( i, val ) {
			
			var email_field	= $(this).val();
			var key_field	= $(this).parents('tr').find('.wpelitepluginsupd-key-field').val();
			var valid		= validateEmail( email_field );
			
			// if both field are empty don't check for validation
            if( $.trim(email_field) == '' && $.trim(key_field) == '') {
                return true;
            }

         	if( $.trim(email_field) == '' || email_field == 'undefined' || email_field == 'null' ) {
                $(this).next().next().fadeOut();
                $(this).addClass( 'wpelitepluginsupd-required-email' );
                $(this).next().fadeIn().attr( 'title', 'Email is Required.' );
                error = 1;
            } else if( !valid ) {                    
                $(this).next().next().fadeOut();
                $(this).addClass( 'wpelitepluginsupd-required-email' );
                $(this).next().fadeIn().attr( 'title', 'Invalid Email.' );
                error = 1;
            } else {
                $(this).next().fadeOut();
                $(this).removeClass( 'wpelitepluginsupd-required-email' );
                $(this).next().next().fadeIn();
            }			
		});
		
		if( error == 1 ){
			return false;
		}
	});

	$('.wpelitepluginsupd-email-field').on( 'focus', function(){        
        $(this).next().next().fadeOut();
    });
	
	$('.wpelitepluginsupd-email-field').on( 'blur', function(){
	
		var email_field	= $(this).val();		
		var valid		= validateEmail( email_field );
		
		if( valid ) {
            $(this).next().fadeOut();
            $(this).removeClass( 'wpelitepluginsupd-required-email' );
            $(this).next().next().fadeIn();
        }		
	});
});

function validateEmail( email ) {
	
	var filter = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,12})+$/;
	
	if( filter.test( email ) ) {
		return true;
	} else {
		return false;
	}
}