jQuery(document).ready(function($) {
    jQuery(".img-toogle").click(function() {
        //jQuery('.passenger').slideToggle();
        passenger = jQuery(this).closest('.box.item');
        if (passenger.find('.control-group').is(":visible")) {
            jQuery(this).addClass('plusimage');
            jQuery(this).removeClass('minusimage');

        } else {
            jQuery(this).removeClass('plusimage');
            jQuery(this).addClass('minusimage');
        }
        passenger.find('.control-group').slideToggle();

    });
    var depart = new Date(jQuery('#pass_start').val());
    var end = new Date(jQuery('#pass_return_date').val());
    if(end!='')
        depart=end;
    console.log(p_mindate);
    jQuery(".calendar").datepicker({
        minDate: new Date(p_mindate),
        changeMonth: true,
        changeYear: true,
        dateFormat: dateFormat,
        yearRange: (new Date().getFullYear()) + ':' + ((new Date().getFullYear()) + 20),        
        onClose: function() {
            if (jQuery(this).val() == '') {
                jQuery(this).addClass("required-red");
            } else {
                jQuery(this).removeClass("required-red");

            }
        }
    });
    function diffOf2Dates(todaysDate, configDate)
    {
        /*var udate="2011-08-18 11:49:01.0";
         var configDate=new Date(udate);*/

        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var firstDate = todaysDate; // Todays date
        var secondDate = new Date(configDate);

        var diffDays = Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay));
        console.info(firstDate + ", " + secondDate);
        //console.info(Math.ceil(diffDays));

        return Math.ceil(diffDays);
    }
    var depart = new Date(jQuery('#pass_start').val());
    var end = new Date(jQuery('#pass_return_date').val());
    if(end!='')
        depart=end;
    var today = new Date();
    var year = new Date(depart).getFullYear() - 18;
    jQuery(".birthday").datepicker({
        dateFormat: dateFormat,
        changeMonth: true,
        maxDate: "today",        
        changeYear: true,
        showOn: "focus",
        showButtonPanel: true,
        onSelect: function(selected) {
            item_passenger = jQuery(this).closest('.box.item');
            item_passenger.find('.inputbox.dateofissue.passport_date_of_issue').datepicker("option", {
                minDate: new Date(selected)
            });
            item_passenger.find('.inputbox.dateofissue.document_date_of_issue').datepicker("option", {
                minDate: new Date(selected)
            });
            item_passenger.find('.inputbox.dateofissue.visa_date_of_issue').datepicker("option", {
                minDate: new Date(selected)
            });

            var birthday = new Date(jQuery(this).val());
            var depart = new Date(jQuery('#pass_start').val());
            if (diffOf2Dates(birthday, depart) < 30)
            {
                text_note = 'Doctor\'s note is required for infant under 30 days old';
                jQuery(this).next().next('.invalid-error').remove();
                jQuery(this).next().after('<span class="invalid-error">' + text_note + '</span>');
            }
            else
            {
                jQuery(this).next().next('.invalid-error').remove();
            }


        }
    });

    jQuery(".birthday.adult").datepicker("option", {

        maxDate: new Date(a_maxdate),
        minDate: new Date(a_mindate),
        yearRange: new Date(a_mindate).getFullYear() + ':' + new Date(a_maxdate).getFullYear()

    });

    jQuery(".birthday.children").datepicker("option", {
        minDate: new Date(c_mindate),
        maxDate: new Date(c_maxdate),
        yearRange: new Date(c_mindate).getFullYear() + ':' + new Date(c_maxdate).getFullYear()
    });
    day=0;
    jQuery(".birthday.infant ").datepicker("option", {
        minDate: new Date(i_mindate),
        maxDate: 'today',
        yearRange: new Date(i_mindate).getFullYear() + ':' + new Date().getFullYear()
    });

    function calculate_age(birth_month, birth_day, birth_year)
    {
        var depart = new Date(jQuery('#pass_start').val());
        var end = new Date(jQuery('#pass_return_date').val());
        if(end!='')
            depart=end;
        depart_year = depart.getFullYear();
        depart_month = depart.getMonth();
        depart_day = depart.getDate();
        age = depart_year - birth_year;

        if (depart_month < (birth_month - 1))
        {
            age--;
        }
        if (((birth_month - 1) == depart_month) && (depart_day < birth_day))
        {
            age--;
        }
        return age;
    }

    jQuery(".suntotal").live("change", function() {
        total = 0;
        item = jQuery(this).closest('.item');
        item.find('.suntotal').each(function(index) {
            total += (Number(jQuery(this).val()));
        });
        if (total > 100) {
            jQuery(this).next('.invalid-error').remove();
            //jQuery(this).after('<span class="invalid-error">max is 100.</span>');
            //jQuery(this).next('.invalid-error').blink();
        } else {
            jQuery(this).next('.invalid-error').remove();
        }

    });
    jQuery(".passenger_required,.infant_required").live("blur", function() {
        if (jQuery(this).val() == 0 || jQuery(this).val() == '') {
            jQuery(this).addClass("required-red");
            if (jQuery(this).hasClass('numeric'))
                jQuery(this).val(0);
        } else {
            jQuery(this).removeClass("required-red");

        }
    });
    var maxWidth = 0;
    jQuery('.control-group .control-label').each(function(i) {
        if (this.offsetWidth > maxWidth)
            maxWidth = this.offsetWidth;
    });

    jQuery('.control-group .control-label').each(function(i) {
        jQuery(this).css({width: maxWidth + 'px'});
    });

    jQuery('.control-group').each(function(i) {
        if (i % 2 == 1)
            jQuery(this).css({'background-color': '#FBFCFC'});
    });
    
	
	

});

function autoFill(){
// 	jConfirm('', 'Confirmation Dialog', function(r) {
// 	    jAlert('Confirmed: ' + r, 'Confirmation Results');
// 	});
	
	var firstname = jQuery('[name="person[adult][0][firstname]"]').val();
	jQuery('#firstname').val(firstname);
	var lastname = jQuery('[name="person[adult][0][lastname]"]').val();
	jQuery('#lastname').val(lastname);
	var country = jQuery('[name="person[adult][0][nationality]"]').val();
	jQuery('#country_id').val(country);
	var birthday = jQuery('[name="person[adult][0][birthday]"]').val();
	jQuery('#customer_birthday').val(birthday);		
	
	var mobile = jQuery('[name="person[adult][0][alertnatephone]"]').val();
	
// 	var phone = jQuery('[name="person[adult][0][phone]"]').val();	
// 	jQuery('#telephone').val(phone);
	jQuery('#telephone').val('');
	
	
	var email = jQuery('[name="person[adult][0][email]"]').val();
	jQuery('#email').val(email);	

	return false;
}