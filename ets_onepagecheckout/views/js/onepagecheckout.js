/**
 * 2007-2023 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please contact us for extra customization service at an affordable price
 *
 *  @author ETS-Soft <etssoft.jsc@gmail.com>
 *  @copyright  2007-2023 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
var changed_firstname_customer = false;
var changed_lastname_customer = false;
var changed_firstname_invoice_customer = false;
var changed_lastname_invoice_customer = false;
$(document).ready(function(){
    $(document).on('click','#form_ets_onepagecheckout input,#form_ets_onepagecheckout select',function(){
        $('#onepagecheckout-information-errros').html('');
    });
    $(document).on('change','#shipping_address_firstname',function(){
        if($(this).val())
            changed_firstname_customer = true;
        else
            changed_firstname_customer = false;  
    });
    $(document).on('change','#shipping_address_lastname',function(){
        if($(this).val())
            changed_lastname_customer = true;
        else
            changed_lastname_customer = false;  
    });
    $(document).on('change','#invoice_address_firstname',function(){
        if($(this).val())
            changed_firstname_invoice_customer = true;
        else
            changed_firstname_invoice_customer = false;  
    });
    $(document).on('change','#invoice_address_lastname',function(){
        if($(this).val())
            changed_lastname_invoice_customer = true;
        else
            changed_lastname_invoice_customer = false;  
    });
    $(document).on('change input','#customer_create_firstname,#customer_guest_firstname',function(){
        if(!changed_firstname_customer)
        {
            $('#shipping_address_firstname').parents('.form-group.row').find('.ets_opc_error').remove();
            $('#shipping_address_firstname').parent().removeClass('form-error');
            $('#shipping_address_firstname').val($(this).val());
        }
        if(!changed_firstname_invoice_customer)
        {
            $('#invoice_address_firstname').parents('.form-group.row').find('.ets_opc_error').remove();
            $('#invoice_address_firstname').parent().removeClass('form-error');
            $('#invoice_address_firstname').val($(this).val());
        }
    });
    $(document).on('change input','#customer_create_lastname,#customer_guest_lastname',function(){
        if(!changed_lastname_customer)
        {
            $('#shipping_address_lastname').parents('.form-group.row').find('.ets_opc_error').remove();
            $('#shipping_address_lastname').parent().removeClass('form-error');
            $('#shipping_address_lastname').val($(this).val());
        }
        if(!changed_lastname_invoice_customer)
        {
            $('#invoice_address_lastname').parents('.form-group.row').find('.ets_opc_error').remove();
            $('#invoice_address_lastname').parent().removeClass('form-error');
            $('#invoice_address_lastname').val($(this).val());
        }
    });
    $(document).on('change','#invoice_address_lastname',function(){
        if(!$(this).val())
            changed_lastname_invoice_customer = true;
        else
            changed_lastname_invoice_customer = false;  
    });
    $(document).on('updatePayMentMethod',function(){
        if(window.ps_checkout)
        {
            window.ps_checkout.app = false;
            var DOMContentLoaded_event = document.createEvent("Event");
            DOMContentLoaded_event.initEvent("DOMContentLoaded", true, true);
            window.document.dispatchEvent(DOMContentLoaded_event);
        }
    });
    ets_opc_displayShipping();
    if($('#login-g-recaptcha-response-3').length>0)
    {
        grecaptcha.ready(function() {
            grecaptcha.execute(login_google3_site_key, {action: 'homepage'}).then(function(token) {
                $('#login-g-recaptcha-response-3').val(token);
            });
        });
    }
    ets_opc_change_type_option();
    if ($(".datepicker").length > 0)
		$(".datepicker").datepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd',
            timeFormat: 'hh:mm:ss',            
		});

	if ($(".datetimepicker").length > 0)
	$('.datetimepicker').datetimepicker({
		prevText: '',
		nextText: '',
		dateFormat: 'yy-mm-dd',
		currentText: 'Now',
		closeText: 'Done',
		ampm: false,
		amNames: ['AM', 'A'],
		pmNames: ['PM', 'P'],
		timeFormat: 'hh:mm:ss tt',
        formatTime :'hh:mm:ss tt',
		timeSuffix: '',
		timeOnlyTitle: 'Choose Time',
		timeText: 'Time',
		hourText: 'Hour',
		minuteText: 'Minute',
	});
    $(document).on('change','.custom-file-input',function(){
        $(this).next('.custom-file-label').html($(this).val().replace('C:\\fakepath\\',''));
    }); 
    $(document).on('click','.ps-number-up',function(){
        var quantity = $(this).parent().prev('input[type="number"]').val()!='' ? parseInt($(this).parent().prev('input[type="number"]').val())+1 :1;
        $(this).parent().prev('input[type="number"]').val(quantity);
        $(this).parent().prev('input[type="number"]').change();
    });
    $(document).on('click','.ps-number-down',function(){
        var quantity = $(this).parent().prev('input[type="number"]').val()!=''? parseInt($(this).parent().prev('input[type="number"]').val())-1 :-1;
        $(this).parent().prev('input[type="number"]').val(quantity);
        $(this).parent().prev('input[type="number"]').change();
    });
});
$(document).on('click','#modal .close',function(e){
    $('#modal').addClass('fade').removeClass('in').fadeOut( 200 );
    return false;
});

//$(document).on('click','#cta-terms-and-conditions-0',function(e){
//    $('#modal').addClass('in').removeClass('fade').fadeIn( 300 );
//    return false;
//});

$(document).on('click','input[name="type-checkout-options"]',function(){
    ets_opc_change_type_option()
});
$(document).on('change','#use_shipping_address,#use_invoice_address',function(){
    var $this = $(this);
    if($this.val()=='new')
    {
        $('.js-address-form.'+$(this).data('type')+ ' input:not(.ets-onepage-js-country)').val('');
        $('.js-address-form.'+$(this).data('type')+ ' .form-ok').removeClass('form-ok');
        $('.js-address-form.'+$(this).data('type')+ ' .form-error').removeClass('form-error');
        return false;
    }
    else
    {
        $('.block-onepagecheckout.block-shipping,.block-onepagecheckout.block-payment').addClass('loading');
        $.ajax({
            url: '',
            data: {
                ajax:1,
                id_address :$this.val(),
                getAddressFrom:1,
                address_type : $this.data('type'),
                type_checkout_options: $('input[name="type-checkout-options"]:checked').length ? $('input[name="type-checkout-options"]:checked').val():'login',
            },
            type: 'post',
            dataType: 'json',                
            success: function(json){    
                 $('.js-address-form.'+$this.data('type')).replaceWith(json.address_form);
                 if(json.shipping_methods)
                 {
                    $('.block-shipping .block-content').html(json.shipping_methods);
                    ets_opc_displayShipping();
                 }
            },
            error: function(error)
            { 
                $('.block-onepagecheckout').removeClass('loading');
            }
        });
    }
});
$(document).on('click','#use_another_address_for_invoice',function(){
   if($(this).is(':checked'))
   {
        $('#invoice-addresses').show();
        $('.yes_invoice_address').show();
        $('.no_invoice_address').hide();
   }
   else
   {
        $('#invoice-addresses').hide(); 
        $('.yes_invoice_address').hide();
        $('.no_invoice_address').show();
   }
});
if($('input[name="payment-option"].checked').length)
{
    $('input[name="payment-option"].checked').click();
    setTimeout(function(){
        $('.js-additional-information,.js-payment-option-form').hide();
        $('#'+$('input[name="payment-option"].checked').attr('id')+'-additional-information').show();
        $('#pay-with-'+$('input[name="payment-option"].checked').attr('id')+'-form').show();  
    },500);
}
$(document).on('change','input[name="payment-option"]',function(){
    $('.js-additional-information,.js-payment-option-form').hide();
    $('#'+$(this).attr('id')+'-additional-information').show();
    $('#pay-with-'+$(this).attr('id')+'-form').show();
    var payment = $(this).data('module-name');
    $.ajax({
        url: '',
        data: {
            ajax:1,
            ets_opc_change_payment :1,
            payment:payment,
        },
        type: 'post',
        dataType: 'json',                
        success: function(){    
                                                   
        }
    });
});
$(document).on('change','select',function(){
    if($(this).parent().find('.ets_opc_error').length)
        $(this).parent().find('.ets_opc_error').remove(); 
});
$(document).on('change','.ets-onepage-js-country',function(){
    var address_type = $(this).data('type');
    var id_country= $(this).val();
    if($('#'+address_type+'_postal_code').length && $('#'+address_type+'_postal_code').val())
    {
        validate_field('#'+address_type+'_postal_code');
    }
    if($(this).val())
    {
        $('.block-onepagecheckout.block-shipping,.block-onepagecheckout.block-payment').addClass('loading');
        $.ajax({
            url: '',
            data: {
                ajax:1,
                id_country :id_country,
                address_type:address_type,
                getAddressStates:1,
                type_checkout_options: $('input[name="type-checkout-options"]:checked').length ? $('input[name="type-checkout-options"]:checked').val():'login',
            },
            type: 'post',
            dataType: 'json',
            success: function(json){
                if(json.states)
                {
                    $('.js-address-form.'+address_type+' .form-group.address_state').show();
                }
                else
                {
                    $('.js-address-form.'+address_type+' .form-group.address_state').hide();
                }
                $('.js-address-form.'+address_type+' .form-group.address_state .col-md-8').html(json.states);
                $('.block-shipping .block-content').html(json.shipping_methods);
                ets_opc_displayShipping();
                ets_refresh_shipping_cart(0);
            },
            error: function(error)
            {
                $('.block-onepagecheckout').removeClass('loading');
            }
        });
    }
});
$(document).on('change','.shipping_address .address_state select',function(){
    var address_type = $(this).data('type');
    var id_state= $(this).val();
    if(id_state!=0)
    {
        $('.block-onepagecheckout.block-shipping,.block-onepagecheckout.block-payment').addClass('loading');
        $.ajax({
            url: '',
            data: {
                ajax:1,
                id_country :$('.shipping_address .ets-onepage-js-country').val(),
                id_state:id_state,
                postal_code: $('#shipping_address_postal_code').val(),
                getShippingMethodByStates:1,
                type_checkout_options: $('input[name="type-checkout-options"]:checked').length ? $('input[name="type-checkout-options"]:checked').val():'login',
            },
            type: 'post',
            dataType: 'json',
            success: function(json){
                if(json.shipping_methods)
                {
                    $('.block-shipping .block-content').html(json.shipping_methods);
                    ets_opc_displayShipping();
                    ets_refresh_shipping_cart(0);
                }
                $('.block-onepagecheckout').removeClass('loading');
            },
            error: function(error)
            {
                $('.block-onepagecheckout').removeClass('loading');
            }
        });
    }

});
$(document).on('click','button[name="submitCustomerLogin"]',function(e){
    e.preventDefault();
    $(this).addClass('loading');
    $('#onepagecheckout-information-errros').html('');
    $.ajax({
        url: '',
        data: {
            ajax:1,
            email :$('#customer_login_email').val(),
            password:$('#customer_login_password').val(),
            g_recaptcha_response : $('input[name="g-recaptcha-response"]').length ? $('input[name="g-recaptcha-response"]').val():($('textarea[name="g-recaptcha-response"]').length ? $('textarea[name="g-recaptcha-response"]').val():''),
            submitCustomerLogin:1,
        },
        type: 'post',
        dataType: 'json',                
        success: function(json){ 
            if(json.hasError)
            {
                if(json.field_errors)
                {
                    $(json.field_errors).each(function(){
        				if($('#'+this.field).length)
                        {
                            if($('#'+this.field).parent().find('.ets_opc_error').length)
                                $('#'+this.field).parent().find('.ets_opc_error').html(this.error);
                            else if($('#'+this.field).parents('.input-group').next('.ets_opc_error').length)
                                $('#'+this.field).parents('.input-group').next('.ets_opc_error').html(this.error);
                            else
                                 $('#'+this.field).parent().append('<span class="ets_opc_error">'+this.error+'</span>')
                        }
        			});
                }
                else
                    $('#onepagecheckout-information-errros').html(json.errors);
            }
            else
            {
                $('.block-customer .block-content').html(json.customer_block);
                $('#delivery-addresses').html(json.shipping_address);
                $('#invoice-addresses').html(json.invoice_address);
                $('.block-shipping .block-content').html(json.shipping_methods);
                ets_opc_displayShipping();
                ets_refresh_shipping_cart(0);
                $('button[name="submitCompleteMyOrder"]').removeAttr('disabled');
                $('.block-onepagecheckout.block-address').show();
                $('#invoice-addresses.js-address-selector').hide();
            }
            $('button[name="submitCustomerLogin"]').removeClass('loading');
        },
        error: function(error)
        { 
            $('button[name="submitCustomerLogin"]').removeClass('loading');
        }
    });
});
$(document).on('click','button[name="submitCompleteMyOrder"]',function(e){ 
    e.preventDefault();
    if($('.loading').length)
        return false;
    if($('input[name="type-checkout-options"]:checked').length)
    {
        if($('input[name="type-checkout-options"]:checked').data('type')=='login')
        {
            $('#onepagecheckout-information-errros').html('<div class="bootstrap"><div class="module_error alert alert-danger"><button class="close" type="button" data-dismiss="alert">x</button>'+login_before_text+'</div></div>');
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
            return false;
        }
    }
    $('button[name="submitCompleteMyOrder"]').addClass('loading');
    $('.ets_opc_error').remove();
    $('#onepagecheckout-information-errros').html('');
    var formData = new FormData($(this).parents('form').get(0));
    formData.append('submitCompleteMyOrder', 1);
    $.ajax({
        url: $('#form_ets_onepagecheckout').attr('action'),
        data: formData,
        type: 'post',
        dataType: 'json',  
        processData: false,
        contentType: false,              
        success: function(json){ 
            $('button[name="submitCompleteMyOrder"]').removeClass('loading');
            if(json.java_script)
                $('body').append(json.java_script);
            if(json.hasError)
            {
                if(json.field_errors)
                {
                    $(json.field_errors).each(function(){
        				if($('#'+this.field).length)
                        {
                            if($('#'+this.field).parents('.form-group').find('.ets_opc_error').length)
                            {
                                $('#'+this.field).parents('.form-group').find('.ets_opc_error').html(this.error);
                            }
                            else if($('#'+this.field).parents('.input-group').next('.ets_opc_error').length)
                                $('#'+this.field).parents('.input-group').next('.ets_opc_error').html(this.error);
                            else {
                                if ( $('#'+this.field).parent('label').length > 0 &&  $('#'+this.field).parents('.checkbox').length > 0){
                                    $('#'+this.field).parents('.checkbox').append('<span class="ets_opc_error">'+this.error+'</span>');
                                } else if ( $('#'+this.field).parents('.input-group').length > 0 ) {
                                    $('#'+this.field).parents('.input-group').after('<span class="ets_opc_error">'+this.error+'</span>');
                                } else if ( $('#'+this.field).parents('.ps-number').length > 0 ) {
                                    $('#'+this.field).parents('.ps-number').parent().append('<span class="ets_opc_error">'+this.error+'</span>');
                                } else {
                                    $('#'+this.field).parent().append('<span class="ets_opc_error">'+this.error+'</span>');
                                }
                            }
                            
                                 
                        }
        			});
                }
                $('#onepagecheckout-information-errros').html(json.errors);
                document.body.scrollTop = 0; // For Safari
                document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
                
            }
            else
            {
                if(json.jsCustomer){
                    prestashop.customer = json.jsCustomer;
                }
                if(json.customer_block){
                    $('.block-customer .block-content').html(json.customer_block);
                    $('#delivery-addresses').html(json.shipping_address);
                    $('#invoice-addresses').html(json.invoice_address);
                }
                if($('input[name="payment-option"]:checked').length)
                {
                    if($('#button-card:visible').length)
                    {
                        $('#button-card button').click();
                        $('#ps_checkout-binary-form').removeClass('popup');
                    }
                    else if($('#button-paypal:visible').length)
                    {
                        $('#ps_checkout-binary-form').addClass('popup');
                    }
                    else if($('#'+$('input[name="payment-option"]:checked').attr('id')+'-additional-information .paypal-buttons-context-iframe').length)
                    {
                        $('#'+$('input[name="payment-option"]:checked').attr('id')+'-additional-information .paypal-buttons-context-iframe').addClass('popup');
                    }
                    else
                    {
                        $('#ps_checkout-binary-form').removeClass('popup');
                        var payment_name = $('input[name="payment-option"]:checked').data('module-name');
                        if($('.js-payment-ps_checkout.js-payment-'+payment_name).length)
                        {
                            $('.js-payment-ps_checkout.js-payment-'+payment_name).removeClass('disabled').show();
                            $('.block-onepagecheckout.block-payment').addClass('popup');
                        }
                        else
                        {
                            if($('#pay-with-'+$('input[name="payment-option"]:checked').attr('id')+'-form form').attr('action'))
                                $('#pay-with-'+$('input[name="payment-option"]:checked').attr('id')+'-form form').submit();
                            else
                                $('#payment-confirmation button').click();
                        }
                        
                    }
                }
                else if(json.link_checkout_free)
                    window.location.href = json.link_checkout_free;
                
            }
            
        },
        error: function(error)
        { 
            $('button[name="submitCompleteMyOrder"]').removeClass('loading');
        }
    });
});
$(document).mouseup(function (e)
{
    if($('.block-onepagecheckout.block-payment.popup').length)
    {
        var container_map = $('.block-onepagecheckout.block-payment.popup');
        if (!container_map.is(e.target)&& container_map.has(e.target).length === 0)
        {
            $('.block-onepagecheckout.block-payment.popup').removeClass('popup');
            $('.js-payment-ps_checkout').hide();
        }
    }
    
});
$(document).on('change','.delivery-options-list input[type="radio"]',function(){
    $('.block-onepagecheckout.block-payment').addClass('loading');
    $('#onepagecheckout-information-errros').html('');
    $.ajax({
        url: $('#form_ets_onepagecheckout').attr('action'),
        data: $('.delivery-options-list :input').serialize()+'&updateCarrier=1&id_country='+$('.shipping_address .ets-onepage-js-country').val()+'&type_checkout_options='+($('input[name="type-checkout-options"]:checked').length ? $('input[name="type-checkout-options"]:checked').val():'login')+($('#shipping_address_id_state').length ? '&id_state='+$('#shipping_address_id_state').val() :'')+($('#shipping_address_postal_code').length ? '&postal_code='+$('#shipping_address_postal_code').val():''),
        type: 'post',
        dataType: 'json',                
        success: function(json){ 
            $(this).parents('.block-onepagecheckout').removeClass('loading');
            $('.block-payment .block-content').html(json.payment_methods);
            $(document).trigger('updatePayMentMethod');
            if(!json.payment_methods)
            {
                $('.block-payment .block-content').parent().hide();
                $('.block-onepagecheckout.block-shipping').parent().removeClass('col-lg-12').addClass('col-lg-12');
            }
            else
            {
                $('.block-payment .block-content').parent().show();
                $('.block-onepagecheckout.block-shipping').parent().removeClass('col-lg-12').addClass('col-lg-12');
            }
            if($('input[name="payment-option"].checked').length)
            {
                $('input[name="payment-option"].checked').click();
            }
            if(json.total_more_free)
            {
                $('.buy_more_fee_shipping').show();
                $('.box_more_fee_shipping').show();
                $('.box_more_fee_shipping .end').html(json.total_has_free);
                $('.box_more_fee_shipping .box_total_cart').css('width',json.total_percent_free+'%');
                $('.buy_more_fee_shipping strong').html(json.total_more_free);
            }
            else
            {
                $('.buy_more_fee_shipping').hide();
                $('.box_more_fee_shipping').hide();
            }
            ets_refresh_shipping_cart(false);
        },
        error: function(error)
        { 
            $(this).parents('.block-onepagecheckout').removeClass('loading');
        }
    });
});
$(document).on('click','input[name="gift"]',function(){
    if($(this).is(':checked'))
        $('#gift').show();
    else
        $('#gift').hide();
    $.ajax({
        url: $('#form_ets_onepagecheckout').attr('action'),
        data: 'submitChangeGift=1&gift='+($(this).is(':checked') ? '1':'0'),
        type: 'post',
        dataType: 'json',                
        success: function(json){ 
            ets_refresh_shipping_cart(false);
        },
        error: function(error)
        { 
            $(this).parents('.block-onepagecheckout').removeClass('loading');
        }
    });
});
$(document).on('click','.add_new_address',function(){
    $('.js-address-form.'+$(this).data('type')+ ' input:not(.ets-onepage-js-country)').val('');
    $('.js-address-form.'+$(this).data('type')+ ' select').val('');
    $('.js-address-form.'+$(this).data('type')+ ' .form-ok').removeClass('form-ok');
    $('.js-address-form.'+$(this).data('type')+ ' .form-error').removeClass('form-error');
    $('select[name="'+$(this).data('type')+'"]').val('');
    return false;
});
$(document).on('click','input[name="type-checkout-options"]',function(){
    $.ajax({
        url: '',
        data: 'changeTypeCheckoutOptions=1&type_checkout_options='+$('input[name="type-checkout-options"]:checked').val()+'&id_country='+$('#delivery-addresses .ets-onepage-js-country').val()+($('#shipping_address_id_state').length ? '&id_state='+$('#shipping_address_id_state').val() :'')+($('#shipping_address_postal_code').length ? '&postal_code='+$('#shipping_address_postal_code').val():''),
        type: 'post',
        dataType: 'json',                
        success: function(json){
            if(json.payment_methods)
            {
                $('.block-payment .block-content').html(json.payment_methods);
                if($('input[name="payment-option"].checked').length)
                {
                    $('input[name="payment-option"].checked').click();
                }
                $(document).trigger('updatePayMentMethod');
                $(document).trigger('DOMContentLoaded');
            }
        },
        error: function(error)
        { 

        }
    });
});
$( document ).ajaxError(function( event, jqxhr, settings, thrownError){
    if (settings.url &&  settings.url.indexOf('action=refresh') >0) {

    }
});
$(document ).ajaxComplete(function( event, xhr, settings ) {
    var data_post = settings.data;
    if(typeof data_post!= 'object' && data_post )
    {
        if(data_post.indexOf('addDiscount')>=0 && data_post.indexOf('discount_name')>=0 )
            $('.loading').removeClass('loading');
    }
    if(xhr.responseText && xhr.responseText.indexOf("cart_detailed")>=0)
    {
        var json= $.parseJSON(xhr.responseText);
        
        if($('.block-shopping-cart').attr('data-change-cart'))
        {
            $('.block-shopping-cart').attr('data-change-cart','');
            ets_refresh_shipping_cart(1);
        }
        $(document).trigger('updatePayMentMethod');
        $(document).trigger('DOMContentLoaded');
    }
});
$(document).on('change','input[name="product-quantity-spin"]',function(){
    $('.block-onepagecheckout.block-payment').addClass('loading');
    $('.block-onepagecheckout.block-shop-license-info').addClass('loading');
    $('.block-shopping-cart').attr('data-change-cart','1');
});
$(document).on('click','.remove-from-cart,a[data-link-action="ets-remove-voucher"]',function(){
    if(!$('.block-shopping-cart').hasClass('loading'))
    {
        if($(this).hasClass('remove-from-cart'))
            var del_product = 1;
        else
            var del_product= 0;
        if(confirm(del_product ? confirm_product: confirm_discount))
        {
            $('.block-shopping-cart').addClass('loading');
            if(del_product)
            {
                $('.block-onepagecheckout.block-shipping').addClass('loading');
                $('.block-onepagecheckout.block-shop-license-info').addClass('loading');
            }
            var url_ajax = $(this).attr('href');
            $.ajax({
                url: url_ajax,
                data: 'ajax=1&action=update',
                type: 'post',
                dataType: 'json',                
                success: function(json){ 
                    ets_refresh_shipping_cart(del_product);    
                },
                error: function(error)
                { 
                    $('.loading').removeClass('loading');
                }
            });
        }
    }
    return false;
});
$(document).on('click','#promo-code button',function(e){
    e.preventDefault();
    $(this).addClass('loading');
    var formData = new FormData($(this).parents('form').get(0));
    formData.append('ajax',1);
    formData.append('action','update');
    $.ajax({
        url: $('[data-link-action="add-voucher"]').attr('action'),
        data: formData,
        type: 'post',
        dataType: 'json',  
        processData: false,
        contentType: false,              
        success: function(json){ 
            
            if(json.hasError)
            {
                $(".promo-code .js-error").show().find(".js-error-text").text(json.errors[0]);
                $('#promo-code button').removeClass('loading');
            }
            else
            {
                ets_refresh_shipping_cart(0);
            }
            
        },
        error: function(error)
        { 
            $('#promo-code button').removeClass('loading');
        }
    });
    
});
function ets_refresh_shipping_cart(del_product)
{
    if($(".js-cart").data("refresh-url"))
    {
        $.ajax({
            url: $(".js-cart").data("refresh-url"),
            data: 'id_country='+$('#shipping_address_id_country').val()+'&del_product='+del_product+($('#shipping_address_id_state').length ? '&id_state='+$('#shipping_address_id_state').val() :'')+($('#shipping_address_postal_code').length ? '&postal_code='+$('#shipping_address_postal_code').val():'')+($('.block-shop-license-info').length ? '&'+$('.block-shop-license-info input').serialize():''),
            type: 'post',
            dataType: 'json',                
            success: function(json){ 
                $('.loading').removeClass('loading');
                if(json.cart_detailed=='empty')
                {
                    $('.cart-container').html('<div class="alert alert-warning">'+json.text_info+'</div>');
                    window.location.href = json.url_cart;
                }
                else
                {
                    
                    if(json.isAvailable)
                    {
                        $('button[name="submitCompleteMyOrder"]').attr('disabled','disabled');
                        $('#onepagecheckout-information-errros').html(json.isAvailable);
                    }
                    else
                    {
                        $('button[name="submitCompleteMyOrder"]').removeAttr('disabled');
                        $('#onepagecheckout-information-errros').html('');
                    }
                    $(".cart-detailed-totals").replaceWith(json.cart_detailed_totals);
                    $(".cart-summary-items-subtotal").replaceWith(json.cart_summary_items_subtotal);
                    $(".cart-summary-totals").replaceWith(json.cart_summary_totals);
                    $(".cart-detailed-actions").replaceWith(json.cart_detailed_actions);
                    $(".cart-voucher").replaceWith(json.cart_voucher);
                    $(".cart-overview").replaceWith(json.cart_detailed);
                    if(json.is_virtual_cart)
                        $('.block-onepagecheckout.block-gift').hide();
                    else
                        $('.block-onepagecheckout.block-gift').show();
                    if(json.gift_label)
                    {
                        $('.gift_label label').html(json.gift_label);
                    }
                    $(".js-cart-line-product-quantity").each(function(e, t) {
                            var n = $(t);
                            n.attr("value", n.val());
                    });
                    if(del_product==1)
                    {
                        $('.block-shipping .block-content').html(json.shipping_methods);
                        ets_opc_displayShipping();
                        if(json.shipping_methods)
                        {
                            $('.block-shipping .block-content').parent().show();
                            $('.block-onepagecheckout.block-payment').parent().removeClass('col-lg-12').addClass('col-lg-12');
                        }
                        else
                        {
                            $('.block-shipping .block-content').parent().hide();
                            $('.block-onepagecheckout.block-payment').parent().removeClass('col-lg-12').addClass('col-lg-12');
                        }
                    }
                    if($('.block-onepagecheckout.block-shop-license-info').length)
                    {
                        $('.block-onepagecheckout.block-shop-license-info .block-content').html(json.hookDisplayShopLicenseField);
                        if(json.hookDisplayShopLicenseField)
                        {
                            $('.block-onepagecheckout.block-shop-license-info').show();
                            if($('.ets-sl-input-domain-product').length)
                            {
                                
                               $('.ets-sl-input-domain-product').each(function(){
                                    try{
                                        var url = new URL($(this).val());
                                        var text_url = url.hostname.replace(/^(www\.)/,'').trim();
                                        
                                        $(this).val(text_url);
                                        $(this).removeClass('error');
                                        $(this).addClass('success');
                                    }
                                    catch (e) {
                                        $(this).removeClass('success');
                                        $(this).addClass('error');
                                    }
                                    if($(this).val().indexOf("www.")===0)
                                    {
                                        $(this).val($(this).val().replace('www.',''));
                                    }
                               }); 
                            }
                        }
                        else
                            $('.block-onepagecheckout.block-shop-license-info').hide();
                    }
                    if(json.dir_js)
                    {
                        if($('body .ets_opc_extra_js').length)
                        {
                            $('body .ets_opc_extra_js').html('<script type="text/javascript" src="'+json.dir_js+'" ></script>');
                        }
                        else
                        {
                            $('body').append('<div class="ets_opc_extra_js"><script type="text/javascript" src="'+json.dir_js+'" ></script></div>');
                        }
                    }
                    prestashop.emit("updatedCart", {
                        eventType: "updateCart",
                        resp: json
                    });
                }
            },
            error: function(error)
            { 
                $('.loading').removeClass('loading');
            }
        });
    }
}

function ets_opc_displayShipping()
{
    $.ajax({
        url: $('#form_ets_onepagecheckout').attr('action'),
        data: 'updateCarrier=1'+($('.delivery-options-list :input').length ? '&'+$('.delivery-options-list :input').serialize():'')+'&id_country='+$('.shipping_address .ets-onepage-js-country').val()+'&type_checkout_options='+($('input[name="type-checkout-options"]:checked').length ? $('input[name="type-checkout-options"]:checked').val():'login')+($('#shipping_address_id_state').length ? '&id_state='+$('#shipping_address_id_state').val() :'')+($('#shipping_address_postal_code').length ? '&postal_code='+$('#shipping_address_postal_code').val():''),
        type: 'post',
        dataType: 'json',
        success: function(json){
            if(json.total_more_free)
            {
                $('.buy_more_fee_shipping').show();
                $('.box_more_fee_shipping').show();
                $('.box_more_fee_shipping .end').html(json.total_has_free);
                $('.box_more_fee_shipping .box_total_cart').css('width',json.total_percent_free+'%');
                $('.buy_more_fee_shipping strong').html(json.total_more_free);
            }
            else
            {
                $('.buy_more_fee_shipping').hide();
                $('.box_more_fee_shipping').hide();
            }
            if(json.payment_methods)
            {
                $('.block-payment .block-content').html(json.payment_methods);
                if($('input[name="payment-option"].checked').length)
                {
                    $('input[name="payment-option"].checked').click();
                }
                $(document).trigger('updatePayMentMethod');
            }
            $('.block-onepagecheckout').removeClass('loading');
        },
        error: function(error)
        {
            $('.buy_more_fee_shipping').hide();
            $('.box_more_fee_shipping').hide();
        }
    });
    if(!$('.delivery-options-list input[type="radio"]:checked').length)
    {
        $('.buy_more_fee_shipping').hide();
        $('.box_more_fee_shipping').hide();
    }
    $(document).trigger("hooksShippingLoaded");
}
function ets_opc_change_type_option()
{
    if($('input[name="type-checkout-options"]:checked').length)
    {
        $('.type-checkout-option').hide();
         $('.type-checkout-option.'+$('input[name="type-checkout-options"]:checked').data('type')).show();
         if($('input[name="type-checkout-options"]:checked').data('type')=='login')
         {
            $('.block-onepagecheckout.block-address').hide();
         }
         else
         {
            $('.block-onepagecheckout.block-address').show();
         }
    }
    if(ETS_OPC_USE_NAME_ACCOUNT)
    {
        if($('input[name="type-checkout-options"]:checked').data('type')=='create')
        {
            $('.js-address-form .form-group.firstname').hide();
            $('.js-address-form .form-group.lastname').hide();
        } 
    }
}
$(document).on('click','.js-payment-ps_checkout.popup',function(e){
    e.preventDefault();
    $('.js-payment-ps_checkout').removeClass('popup');
});
$(document).on('click','.js-payment-ps_checkout.popup #ps_checkout-buttons-container',function(evt){
    evt.stopPropagation();
});