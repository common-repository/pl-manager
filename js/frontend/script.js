jQuery(document).ready(function($){
    
    $(document).on("click",'.plm-click',function(e){
        e.preventDefault();
        var post_id=jQuery(this).attr("data-post_id");
        var nonce=jQuery(this).attr("data-nonce");
        var type = jQuery(this).attr("data-type");
        
        jQuery.ajax({
               type : "post",
               async : true,
               dataType : "json",
               beforeSend:function() {
                                jQuery(".post-" + post_id +" .status").html('<i class="fa fa-spinner fa-spin fa-1x" aria-hidden="true"></i>');
                             },
               url : plm_admin_ajax.ajax_url,
               data : {action: "plm_process_vote_count", type : type, post_id : post_id, nonce: nonce},
               success: function(response) {
               jQuery(".post-" + post_id +" .plm-like .plm-count").html(response.like_count);
               jQuery(".post-" + post_id+" .plm-unlike .plm-count").html(response.unlike_count);
               jQuery(".post-" + post_id +" .status").html(response.message);
               },
               error: function(jqXHR, textStatus, errorThrown,exception) {
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
                        jQuery(".post-" + post_id +" .status").removeClass("loader");
                        jQuery(".post-" + post_id +" .message").html(msg);
               }
          });
        
        })
    
});