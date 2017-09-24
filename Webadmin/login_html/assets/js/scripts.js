
jQuery(document).ready(function() {
	
    /*
        Fullscreen background
    */
    $.backstretch("/login_html/assets/img/backgrounds/universe.jpg");
    
    /*
        Form validation
    */
    $('.login-form input[type="text"], .login-form input[type="password"], .login-form textarea').on('focus', function() 
    {
    	$(this).removeClass('input-error');
    });
    
    $('.login-form').on('submit', function(e) 
    {
        e.preventDefault();
        
    	var fault = false;
        $(this).find('input[type="text"], input[type="password"], textarea').each(function()
        {
    		if( $(this).val() == "" ) {
                $(this).addClass('input-error');
                fault = true;
    		}
    		else 
            {
    			$(this).removeClass('input-error');
    		}
        });
        
        if(!fault)
        {
            if($('#passwd').val() != "")
                {
                    $('#response').load(BASE_URI + $('#passwd').val() + "/login", 
                        function() { 
                            $('#passwd').val('');
                        });            
                }
                    
        }
    });

    

    
});
