
//show modal pop-up
(function ($) {

    /**
     * Confirm a link or a button
     * @param [options] {{title, text, confirm, cancel, confirmButton, cancelButton, post, confirmButtonClass}}
     */
    $.fn.confirmClick = function (options) {
        if (typeof options === 'undefined') {
            options = {};
        }

        this.click(function (e) {
            e.preventDefault();

            var newOptions = $.extend({
                button: $(this)
            }, options);

            $.confirm(newOptions, e);
        });

        return this;
    };
    
    $.fn.confirm = function (options) {
        if (typeof options === 'undefined') {
            options = {};
        }


        var newOptions = $.extend({
            button: $(this)
        }, options);

        $.confirm(newOptions, this);
       

        return this;
    };


    /**
     * Show a confirmation dialog
     * @param [options] {{title, text, confirm, cancel, confirmButton, cancelButton, post, confirmButtonClass}}
     * @param [e] {Event}
     */
    $.confirm = function (options, e) {
        // Do nothing when active confirm modal.
        if ($('.confirmation-modal').length > 0)
            return;

        // Parse options defined with "data-" attributes
        var dataOptions = {};
        if (options.button) {
            var dataOptionsMapping = {
                'title': 'title',
                'text': 'text',
                'confirm-button': 'confirmButton',
                'cancel-button': 'cancelButton',
                'confirm-button-class': 'confirmButtonClass',
                'cancel-button-class': 'cancelButtonClass'
            };
            $.each(dataOptionsMapping, function(attributeName, optionName) {
                var value = options.button.data(attributeName);
                if (value) {
                    dataOptions[optionName] = value;
                }
            });
        }

        // Default options
        var settings = $.extend({}, $.confirm.options, {
            confirm: function () {
                var url = e && (('string' === typeof e && e) || (e.currentTarget && e.currentTarget.attributes['href'].value));
                if (url) {
                    if (options.post) {
                        var form = $('<form method="post" class="hide" action="' + url + '"></form>');
                        $("body").append(form);
                        form.submit();
                    } else {
                        window.location = url;
                    }
                }
            },
            cancel: function (o) {
            },
            button: null
        }, dataOptions, options);

        // Modal
        var modalHeader = '';
        if (settings.title !== '') {
            modalHeader =
                '<div class=modal-header>' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                    '<h4 class="modal-title">' + settings.title+'</h4>' +
                '</div>';
        }
        var modalHTML =
                '<div class="confirmation-modal modal fade" tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">' +
                            modalHeader +
                            '<div class="modal-body">' + settings.text + '</div>' +
                            '<div class="modal-footer">' +
                                '<button class="confirm btn ' + settings.confirmButtonClass + '" type="button" data-dismiss="modal">' +
                                    settings.confirmButton +
                                '</button>' +
                                '<button class="cancel btn ' + settings.cancelButtonClass + '" type="button" data-dismiss="modal">' +
                                    settings.cancelButton +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

        var modal = $(modalHTML);

        modal.on('shown.bs.modal', function () {
            modal.find(".btn-primary:first").focus();
        });
        modal.on('hidden.bs.modal', function () {
            modal.remove();
        });
        modal.find(".confirm").click(function () {
            settings.confirm(settings.button);
        });
        modal.find(".cancel").click(function () {
            settings.cancel(settings.button);
        });

        // Show the modal
        $("body").append(modal);
        modal.modal('show');
    };

    /**
     * Globally definable rules
     */
    $.confirm.options = {
        text: "Are you sure?",
        title: "",
        confirmButton: "Yes",
        cancelButton: "Cancel",
        post: false,
        confirmButtonClass: "btn-primary",
        cancelButtonClass: "btn-default"
    }
    
    /**
     * Show beauty popup
     * @param message message of popup
     * @param title title of popup
     * @param type type of popup(just have warning)
     */
    jAlert = function(message,title,type) {
    	if ($('.confirmation-modal').length > 0){
    		$('.confirmation-modal').modal('hide');
    	}
    		
    	if (type == 'warning')
    		title = '<span style="color:red"><i class="icon-warning"></i></span>&nbsp;'+title;
        var modalHTML =
                '<div class="confirmation-modal modal " tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">'+ 
                        	'<div class="modal-header"><b>'+title+'</b></div>'+
                            '<div class="modal-body">' + message + '</div>' +
                            '<div class="modal-footer center" style="">' +
                                '<button class="center btn-primary btn" type="button" data-dismiss="modal">OK</button>' +                                
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
        
        if(title == undefined)
        	modalHTML =
                '<div class="confirmation-modal modal " tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">'+                        	
                            '<div class="modal-body">' + message + '</div>' +
                            '<div class="modal-footer center" style="">' +
                                '<button class="center btn-primary btn" type="button" data-dismiss="modal">OK</button>' +                                
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';    

        var modal = $(modalHTML);

        modal.on('shown.bs.modal', function () {
            modal.find(".btn-primary:first").focus();
        });
        modal.on('hidden.bs.modal', function () {
            modal.remove();
        });
      
        // Show the modal
        $("body").append(modal);
        modal.modal('show');
	}
    
    /**
     * Show a force popup - Can not be dismiss
     * @param message 
     * @param title
     * @param link (link of button)
     * @param btn_text (text of button)
     */
    jAlertFocus = function(message,title,link,btn_text) {
    	if ($('.confirmation-modal').length > 0){
    		$('.confirmation-modal').modal('hide');
    	}
    	var button_text = 'OK';
    	if (btn_text != '' || btn_text != undefined)
    		button_text = btn_text;
    		
    	if (title == 'warning')
    		title = '<span style="color:red"><i class="icon-warning"></i></span>&nbsp; Warning';
        var modalHTML =
                '<div class="confirmation-modal modal " tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">'+ 
                        	'<div class="modal-header"><b>'+title+'</b></div>'+
                            '<div class="modal-body">' + message + '</div>' +
                            '<div class="modal-footer center" style="">' +
                                '<a href="'+link+'" class="center btn-primary btn" >'+button_text+'</a>' +                                
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
        
        if(title == undefined || title == '')
        	modalHTML =
                '<div class="confirmation-modal modal " tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">'+                        	
                            '<div class="modal-body">' + message + '</div>' +
                            '<div class="modal-footer center" style="">' +
                            	'<a href="'+link+'" class="center btn-primary btn" >'+button_text+'</a>' +                               
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';    

        var modal = $(modalHTML);

        modal.on('shown.bs.modal', function () {
            modal.find(".btn-primary:first").focus();
        });
        
       
      
        // Show the modal
        $("body").append(modal);
        modal.modal({
            backdrop: 'static',
            keyboard: false  // to prevent closing with Esc button (if you want this too)
        });
        modal.modal('show');
	}
   
    
})(jQuery);

//get input value with name is filter by "filter" value
(function ($) {
    $.fn.jbGetFilterValue = function (filter) {    	
    	var result = '';
    	result += $('input').jbGetOptionValue(filter);
    	result += $('select').jbGetOptionValue(filter);
    	return result;
    	
    };
    $.fn.jbGetOptionValue = function(filter){
    	var length = filter.length;
    	var result = '';
    	$(this).each(function(){
    		var name = $(this).attr('name');
    		if(name){
    			if(name.substring(0, length)  == filter){
    				result += '&'+name+'='+$(this).val();
        		}
    		}    		
    	});
    	return result;
    }
})(jQuery);

//check session by ajax return false if session is expired
function checkSession(){
	return jQuery.ajax({
	  	url: 'index.php?option=com_bookpro&controler=flight&task=flight.ajaxCheckSession',
	  	dataType: "html",
	  	async: !1
	 }).responseText;
	
}



