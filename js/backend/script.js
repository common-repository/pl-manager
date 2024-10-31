jQuery(document).ready(function($) {
   
    $('#plm-tabs').easyResponsiveTabs({
            type: 'default',          
            width: 'auto',
            fit: true,   
            closed: false, 
    });
   $(".plm-select.image").imagepicker();
   $('.plm-color-field').wpColorPicker({
        hide: true
    });
   
   
   $('#plm-settings-form').submit(function() { 
      $(this).ajaxSubmit({
         beforeSend:function(){
            $('.pure-button .submit_button i').attr('class','fa fa-spinner fa-spin');
            $('.overlay').css('display','block');
         },
         success: function(){
            $('.pure-button .submit_button i').attr('class','fa fa-floppy-o');
            $.notify("Successfully Saved.",
                     {
                        type : "success",
                        align:"right",
                        verticalAlign : "bottom",
                        Delay : 3000,
                        animationType : "drop",
                        Color :  "#fff" ,
                        background :  "#81F79F"
                     }
                    );
            $('.overlay').css('display','none');
            
         }, 
      }); 
      //setTimeout("$('.pure-button .submit_button i').attr('class','fa fa-floppy-o');", 3000);
      return false; 
   });
   
   $(document.body).on('click','.reset',function(event){
      event.preventDefault();
      $.ajax({
               type : "post",
               async : true,
               dataType : "text",
               beforeSend:function() {
                                $('.reset').find('i').attr('class','fa fa-spinner fa-spin');
                                $('.overlay').css('display','block');
                             },
               url : plm_admin_ajax.ajax_url,
               data : {action: "plm_restore_defaults"},
               success: function(response) {
                  $('.reset').find('i').attr('class','fa fa-undo');
                  $.notify("Reset Succesfull.",
                     {
                        type : "success",
                        align:"right",
                        verticalAlign : "bottom",
                        Delay : 3000,
                        animationType : "drop",
                        Color :  "#fff" ,
                        background :  "#81F79F"
                     }
                    );
                      location.reload(true);
               },
               error: function(jqXHR, textStatus, errorThrown,exception) {
                  $('.reset').find('i').attr('class','fa fa-undo');
                        var msg = '';
                        if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                        } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                        } else {
                        msg = 'Uncaught Error.';
                        }
                        $.notify(msg,
                              {
                                 type : "error",
                                 align:"right",
                                 verticalAlign : "bottom",
                                 Delay : 3000,
                                 animationType : "drop",
                                 Color :  "#fff" ,
                                 background :  "#F78181"
                              }
                             );
               }
          });
      
      })
   
   //open popup
	$(document.body).on('click','.cd-popup-trigger',function(event){
		event.preventDefault();
        var deleteID = $(this).data('delete-id');
        $('#delete_confirm').attr('data-deleteid',deleteID);
		$('.cd-popup').addClass('is-visible');
        
	});
	
	//close popup
	$(document.body).on('click','.cd-popup', function(event){
		if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup') ) {
			event.preventDefault();
			$(this).removeClass('is-visible');
            $('#delete_confirm').find('i').attr('class','fa fa-check fa-4x');
		}
	});
	//close popup when clicking the esc keyboard button
	$(document).keyup(function(event){
    	if(event.which=='27'){
    		$('.cd-popup').removeClass('is-visible');
	    }
    });
    
    $(document.body).on('click','#delete_confirm',function(){
      
      var tdId = $('#delete_confirm').attr("data-deleteid");
      var nonce = "";
   
       $.ajax({
               type : "post",
               async : true,
               dataType : "text",
               beforeSend:function() {
                                $('#delete_confirm').find('i').attr('class','fa fa-spinner fa-spin fa-4x');
                             },
               url : plm_admin_ajax.ajax_url,
               data : {action: "plm_delete_liked_post", deleteID : tdId},
               success: function(response) {
                      $('#delete_confirm').find('i').attr('class','fa fa-check fa-4x');
                      $('.cd-popup').removeClass('is-visible');
                      
                      if ('all'==tdId) {
                        $('.plm-post-like-table').html("<p>No Post Liked..</p>");
                      }else{
                        $('#liked-'+tdId).remove();
                        if ($('.plm-post-like-table table tr').length==1) {
                           $('.plm-post-like-table').html("<p>No Post Liked..</p>");
                        }
                      }
            

               },
               error: function(jqXHR, textStatus, errorThrown,exception) {
                        $('#delete_confirm').find('i').attr('class','fa fa-check fa-4x');
                        $('.cd-popup').removeClass('is-visible');
                        var msg = '';
                        if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                        } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                        } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                        } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                        } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                        } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                        } else {
                        msg = 'Uncaught Error.';
                        }
                       $.notify(msg,
                              {
                                 type : "error",
                                 align:"right",
                                 verticalAlign : "bottom",
                                 Delay : 3000,
                                 animationType : "drop",
                                 Color :  "#fff" ,
                                 background :  "#F78181"
                              }
                             );
               }
          });
    })
    
    $(document.body).on('click','.navigation a.page-numbers',function(event){
      event.preventDefault();
      
      $('.overlay').css('display','block');
      var url = $(this).attr('href');
   
      
    $(".plm-post-like-table").load(url+" .plm-post-like-table", function(responseTxt, statusTxt, xhr){
        if(statusTxt == "success"){
            $('.overlay').css('display','none');
            //$(window).scrollTop($('.plm-post-like-table').offset().top);
            //$('.plm-post-like-table')[0].scrollIntoView(true);
            $('html, body').animate({
              scrollTop: $(".plm-post-like-table").offset().top
        }, 1000);               
        }
        if(statusTxt == "error")
            alert("Error: " + xhr.status + ": " + xhr.statusText);
    });
});
    
    $('.view-shortcode').on('mouseover',function(){  
        $(this).select();
    })
    $('.view-shortcode').on('mouseout',function(){  
        $(this).blur();
    })
   
});
