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
$(document).ready(function(){
   $('.tab-content.settings').show(); 
   $('.tab-sub-content.login').show();
   $('#module_form_reset_btn').hide();
   $('#btnSubmitRessetAddress').hide();
   if($('input[name="ETS_OPC_CAPTCHA_ENABLED"]:checked').val()==1)
   {
        $('.form-group.captchatype').show();
        $('.form-group.captcha').hide();
        $('.form-group.captcha.'+$('input[name="ETS_OPC_CAPTCHA_TYPE"]:checked').val()).show();
   }
   else
   {
        $('.form-group.captchatype').hide();
        $('.form-group.captcha').hide();
   }
   if($('input[name="ETS_OPC_SOCIAL_LOGIN_ENABLED"]:checked').val()==1)
   {
        $('.form-group.social').show();
        $('.form-group.social_create').show();
   }
   else
   {
        $('.form-group.social').hide(); 
        $('.form-group.social_create').hide();
   }
   if($('input[name="ETS_OPC_ADDRESS_GOOGLE_AUTOFILL_ENABLED"]:checked').val()==1)
   {
        $('.form-group.autofill').show();
   }
   else
      $('.form-group.autofill').hide();
   if($('input[name="ETS_OPC_TESTING_ENABLED"]:checked').val()==1)
   {
        $('.form-group.test').show();
   }
   else
      $('.form-group.test').hide(); 
   if($('#group-social-login .social-login-content').length)
   {
        $('#group-social-login .social-login-content').each(function(){
            $(this).html($('.form-group-content-social[data-item="'+$(this).data('item')+'"]').html());
        });
        $('.form-group-content-social').remove();
   }
   if($('#group-social-login').length)
   {
        var $mysocial = $("#group-social-login");
    	$mysocial.sortable({
    		opacity: 0.6,
            handle: ".position",
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updatePositionSocial";						
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: '',
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(jsonData)
        			{
                        if(jsonData.success)
                            $.growl.notice({ message: jsonData.success });
                        if(jsonData.error)
                            $mysocial.sortable("cancel");
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
    	});
    }
    if($('#list-fields-address').length)
    {
        var $myfields = $("#list-fields-address");
    	$myfields.sortable({
    		opacity: 0.6,
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updatePositionFieldAddress";						
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: '',
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(jsonData)
        			{
                        if(jsonData.success)
                            $.growl.notice({ message: jsonData.success });
                        if(jsonData.error)
                            $myfields.sortable("cancel");
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
    	});
    }
    $(document).on('click', '.js-add-custom-field', function (event) {
        event.preventDefault();
        $this = $(this);
        if (typeof ets_opc_languages !== 'undefined' && ets_opc_languages) {
            ets_opcRenderFieldsMethodPayment(this, ets_opc_languages, ets_opc_currency);
        }
    });
    $(document).on('click', '.js-btn-delete-field', function (event) { 
        event.preventDefault();
        if(confirm(confirm_delete_field_text)){
            $(this).closest('.custom-field').remove();
        }
    });
    $(document).on('change','.custom_field_type',function(){
        if($(this).val()=='radio' || $(this).val()=='checkbox' || $(this).val()=='select')
            $(this).parents('.form-group').next('.options').show();
        else
            $(this).parents('.form-group').next('.options').hide();
    });
    ets_opc_updateAdditionalFieldOrdering();
    $(document).on('change','#ETS_OPC_GUEST_DISPLAY_FIELD_optin,#ETS_OPC_GUEST_DISPLAY_FIELD_psgdpr',function(){
        var id= $(this).attr('id').replace('GUEST','CREATEACC');
        if(!$(this).is(':checked') && $('#'+id).is(':checked'))
        {
            $('#'+id).prop('checked',false).change();
        }
        if($(this).is(':checked') && !$('#'+id).is(':checked'))
        {
            $('#'+id).prop('checked',true);
        }
    });
    $(document).on('change','#ETS_OPC_CREATEACC_DISPLAY_FIELD_optin,#ETS_OPC_CREATEACC_DISPLAY_FIELD_psgdpr',function(){
        var id= $(this).attr('id').replace('CREATEACC','GUEST');
        if(!$(this).is(':checked') && $('#'+id).is(':checked'))
        {
            $('#'+id).prop('checked',false).change();
        }
        if($(this).is(':checked') && !$('#'+id).is(':checked'))
        {
            $('#'+id).prop('checked',true);
        }
    });
});

$(document).on('change','.ets_opc_field',function(){
    if(!$(this).is(':checked'))
    {
        $(this).parents('tr').find('.ets_opc_field_validate').prop('checked',false);
    }
    if($(this).hasClass('address_field'))
    {
        if($(this).parents('table').find('.address_field').length== $(this).parents('table').find('.address_field:checked').length)
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',true);
        else
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',false);
        if($(this).parents('table').find('.address_field_validate').length== $(this).parents('table').find('.address_field_validate:checked').length)
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',true);
        else
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',false);
    }
});
$(document).on('click','.ets_opc_field_validate',function(){
    if($(this).is(':checked'))
    {
        $(this).parents('tr').find('.ets_opc_field').prop('checked',true);
    }
    if($(this).hasClass('address_field_validate'))
    {
        if($(this).parents('table').find('.address_field').length== $(this).parents('table').find('.address_field:checked').length)
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',true);
        else
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',false);
        if($(this).parents('table').find('.address_field_validate').length== $(this).parents('table').find('.address_field_validate:checked').length)
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',true);
        else
            $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',false);
    }
});
$(document).on('click','#ETS_OPC_ADDRESS_DISPLAY_FIELD_all',function(e){
    if($(this).is(':checked'))
        $('.address_field').prop('checked',true);
    else
    {
        $('.address_field').prop('checked',false);
        $('.address_field_validate').prop('checked',false);
    }
    if($(this).parents('table').find('.address_field').length== $(this).parents('table').find('.address_field:checked').length)
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',true);
    else
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',false);
    if($(this).parents('table').find('.address_field_validate').length== $(this).parents('table').find('.address_field_validate:checked').length)
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',true);
    else
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',false);
});
$(document).on('click','#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all',function(e){
    if($(this).is(':checked'))
    {
        $('.address_field').prop('checked',true);
        $('.address_field_validate').prop('checked',true);
    }
    else
      $('.address_field_validate').prop('checked',false); 
    if($(this).parents('table').find('.address_field').length== $(this).parents('table').find('.address_field:checked').length)
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',true);
    else
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_all').prop('checked',false);
    if($(this).parents('table').find('.address_field_validate').length== $(this).parents('table').find('.address_field_validate:checked').length)
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',true);
    else
        $('#ETS_OPC_ADDRESS_DISPLAY_FIELD_VALIDATE_all').prop('checked',false);
});
$(document).on('click','#module_form_reset_btn',function(e){
    if(!$(this).hasClass('loading'))
    {
        $(this).addClass('loading');
        var formData = new FormData();
        formData.append('ajax', 1);
        formData.append('btnSubmitRessetDesign',1);
        var url_ajax= $('#module_form').attr('action');
        $.ajax({
            url: url_ajax,
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $('#module_form_reset_btn').removeClass('loading');
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    $('input[name="ETS_OPC_DESIGN_COLOR1"]').val('#0cb7e2').css('background-color','#0cb7e2').css('color','#ffffff');
                    $('input[name="ETS_OPC_DESIGN_COLOR2"]').val('#427e8d').css('background-color','#427e8d').css('color','#ffffff');
                    $('input[name="ETS_OPC_DESIGN_COLOR3"]').val('#b2ced2').css('background-color','#b2ced2').css('color','#000000');
                    $('input[name="ETS_OPC_DESIGN_COLOR4"]').val('#2592a9').css('background-color','#2592a9').css('color','#ffffff');
                    $('input[name="ETS_OPC_DESIGN_COLOR5"]').val('#ffffff').css('background-color','#ffffff').css('color','#000000');
                    $('input[name="ETS_OPC_DESIGN_COLOR6"]').val('#ffffff').css('background-color','#ffffff').css('color','#000000');
                    $('input[name="ETS_OPC_DESIGN_COLOR7"]').val('#0cb8e2').css('background-color','#0cb8e2').css('color','#ffffff');
                    $('input[name="ETS_OPC_DESIGN_COLOR8"]').val('#427e8d').css('background-color','#427e8d').css('color','#ffffff');
                    $('input[name="ETS_OPC_DESIGN_COLOR9"]').val('#ecfbff').css('background-color','#ecfbff').css('color','#000000');
                }
                else if(json.errors)
                {  
                    $.growl.error({ message: json.errors });
                }
            },
            error: function(xhr, status, error)
            {     
                $('#module_form_reset_btn').removeClass('loading');
            }
        });
    }
    return false;
});
$(document).on('click','input[name="ETS_OPC_ADDRESS_GOOGLE_AUTOFILL_ENABLED"]',function(){
    if($('input[name="ETS_OPC_ADDRESS_GOOGLE_AUTOFILL_ENABLED"]:checked').val()==1)
    {
        $('.form-group.autofill').show();
    }
    else
      $('.form-group.autofill').hide();
});
$(document).on('click','.social-login-header .setting',function(){
    $(this).parent().next('.social-login-content').toggle();
});
$(document).on('click','.ets_opc_slider',function(){
   $('input[name="'+$(this).data('field')+'"]').prop('checked',false);
   if($(this).is(':checked'))
   {
       $('#'+$(this).data('field')+'_on').prop('checked',true); 
   } 
   else
      $('#'+$(this).data('field')+'_off').prop('checked',true);  
});
$(document).on('click','input[name="ETS_OPC_SOCIAL_LOGIN_ENABLED"]',function(){
    if($('input[name="ETS_OPC_SOCIAL_LOGIN_ENABLED"]:checked').val()==1)
    {
        $('.form-group.social').show();
        $('.form-group.social_create').show();
    }
    else
    {
        $('.form-group.social').hide();
        $('.form-group.social_create').hide();
    }
});
$(document).on('click','input[name="ETS_OPC_FACEBOOK_ENABLED"],input[name="ETS_OPC_GOOGLE_ENABLED"],input[name="ETS_OPC_PAYPAL_ENABLED"]',function(){
    if($(this).val()==1)
    {
        $('.ets_opc_slider[data-field="'+$(this).attr('name')+'"]').prop('checked',true);
    }
    else
        $('.ets_opc_slider[data-field="'+$(this).attr('name')+'"]').prop('checked',false);
});
$(document).on('click','input[name="ETS_OPC_CAPTCHA_ENABLED"],input[name="ETS_OPC_CAPTCHA_TYPE"]',function(){
    if($('input[name="ETS_OPC_CAPTCHA_ENABLED"]:checked').val()==1)
    {
        $('.form-group.captchatype').show();
        $('.form-group.captcha').hide();
        $('.form-group.captcha.'+$('input[name="ETS_OPC_CAPTCHA_TYPE"]:checked').val()).show();
    }
    else
    {
        $('.form-group.captchatype').hide();
        $('.form-group.captcha').hide();
    }
});
$(document).on('click','input[name="ETS_OPC_TESTING_ENABLED"]',function(){
    if($('input[name="ETS_OPC_TESTING_ENABLED"]:checked').val()==1)
    {
        $('.form-group.test').show();
    }
    else
      $('.form-group.test').hide();
});
$(document).on('click','#list-tabs li.tab',function(){
    if($(this).hasClass('tab_custom'))
        return true;
    $('#list-tabs li.tab').removeClass('active');
    $(this).addClass('active');
    $('.tab-content').hide();
    $('.tab-content.'+$(this).data('tab')).show();
    $('#fieldset_0 .panel-heading').html($(this).html());
    if($(this).data('tab')=='design')
    {
        $('#module_form_reset_btn').show();
    }
    else
        $('#module_form_reset_btn').hide();
    if($(this).data('tab')=='address')
    {
        $('#btnSubmitRessetAddress').show();
    }
    else
        $('#btnSubmitRessetAddress').hide();
        
});
$(document).on('click','#sub-tabs li.sub-tab',function(){
    $('#sub-tabs li.sub-tab').removeClass('active');
    $(this).addClass('active');
    $('.tab-sub-content').hide();
    $('.tab-sub-content.'+$(this).data('tab')).show();
});
$(document).on('click','.add_ip_button',function(){
    if(!$('#ETS_OPC_TEST_API').val())
        $('#ETS_OPC_TEST_API').val($(this).data('ip'));
    else
    {
        if($('#ETS_OPC_TEST_API').val().indexOf($(this).data('ip'))<0)
        {
            $('#ETS_OPC_TEST_API').val($('#ETS_OPC_TEST_API').val()+','+$(this).data('ip'));
        }
    }    
});
$(document).on('click', '.ets_solo_callback_url', function () {
    var range, selection;
    if (window.getSelection && document.createRange) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents($(this)[0]);
        selection.removeAllRanges();
        selection.addRange(range);
    } else if (document.selection && document.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText($(this)[0]);
        range.select();
    }
    document.execCommand('copy');
    if ($(this).data('msg'))
        $.growl.notice({ message: $(this).data('msg') });
});
$(document).on('click','#btnSubmitRessetAddress',function(e){
    e.preventDefault();
    if(!$(this).hasClass('loading'))
    {
        var formData = new FormData();
        formData.append('ajax', 1);
        formData.append('btnSubmitRessetAddress',1);
        var url_ajax= $('#module_form').attr('action');
        $('button[name="btnSubmitRessetAddress"]').addClass('loading');
        $.ajax({
            url: url_ajax,
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $('button[name="btnSubmitRessetAddress"]').removeClass('loading');
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    $('#list-fields-address').html(json.list_fields_address);
                }
                else if(json.errors)
                {  
                    $.growl.error({ message: json.errors });
                }
            },
            error: function(xhr, status, error)
            {     
                $('button[name="btnSubmitRessetAddress"]').removeClass('loading');
            }
        });
    }
    
});
$(document).on('click','button[name="saveConfig"]',function(e){
    e.preventDefault();
    if(!$('button[name="saveConfig"]').hasClass('loading'))
    {
        $('button[name="saveConfig"]').addClass('loading');
        var formData = new FormData($(this).parents('form').get(0));
        formData.append('ajax', 1);
        var url_ajax= $('#module_form').attr('action');
        $('.bootstrap .module_error').remove();
        $.ajax({
            url: url_ajax,
            data: formData,
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(json){
                $('button[name="saveConfig"]').removeClass('loading');
                if(json.success)
                {
                    $.growl.notice({ message: json.success });
                    $('.additional .additional-info-setting').replaceWith(json.html_additional_info);
                    ets_opc_updateAdditionalFieldOrdering();
                }
                else if(json.errors)
                {  
                    $('#module_form .form-wrapper').append(json.errors);
                }
            },
            error: function(xhr, status, error)
            {     
                $('button[name="saveConfig"]').removeClass('loading');
            }
        });
    }
});
$(document).on('click','.onepagecheckout-right .list-action',function(){
    if(!$(this).hasClass('disabled'))
    {            
        $(this).addClass('disabled');
        var $this= $(this);
        $.ajax({
            url: $(this).attr('href')+'&ajax=1',
            data: {},
            type: 'post',
            dataType: 'json',                
            success: function(json){ 
                if(json.success)
                {
                    if(json.enabled=='1')
                    {
                        $this.removeClass('action-disabled').addClass('action-enabled');
                        $this.html('<i class="icon-check"></i>');
                    }                        
                    else
                    {
                        $this.removeClass('action-enabled').addClass('action-disabled');
                        $this.html('<i class="icon-remove"></i>');
                    }
                    $this.attr('href',json.href);
                    $this.removeClass('disabled');
                    if(json.title)
                        $this.attr('title',json.title); 
                    $.growl.notice({ message: json.success }); 
                }
                if(json.errors)
                    $.growl.error({message:json.errors});
                    
                                                            
            },
            error: function(error)
            {                                      
                $this.removeClass('disabled');
            }
        });
    }
    return false;
});
$(document).on('click','.onepagecheckout-right .list-action-payment',function(){
    if(!$(this).hasClass('disabled'))
    {            
        $(this).addClass('disabled');
        var $this= $(this);
        var $active = parseInt($(this).attr('data-active'));
        var module_name = $this.data('module');
        $.ajax({
            url: $active ==1 ? $this.data('link-disable') : $this.data('link-active'),
            data: {},
            type: 'post',
            dataType: 'json',                
            success: function(json){ 
                var data = json[module_name];
                $this.removeClass('disabled');
                if(data.status)
                {
                    if($active==0)
                    {
                        $this.removeClass('action-disabled').addClass('action-enabled');
                        $this.html('<i class="icon-check"></i>');
                        $this.attr('data-active','1');
                        $this.attr('title',Click_to_enable_text);
                    }                        
                    else
                    {
                        $this.removeClass('action-enabled').addClass('action-disabled');
                        $this.html('<i class="icon-remove"></i>');
                        $this.attr('data-active','0');
                        $this.attr('title',Click_to_disable_text);
                    }
                    $.growl.notice({ message: data.msg }); 
                }
                else
                    $.growl.error({message:json.msg});
                                                            
            },
            error: function(error)
            {                                      
                $this.removeClass('disabled');
            }
        });
    }
    return false;
});
function ets_opcRenderFieldsMethodPayment(input, langs, currency)
{
    var date = new Date();
    var rand_num = parseInt(date.getTime());
    custom_field_name_html = '';
    custom_field_name_html += '<div class="form-group custom-field">';
        custom_field_name_html += '<div class="form-group row">';
            custom_field_name_html += '<label class="control-label required col-lg-3">' + custom_field_title + '</label>';
            custom_field_name_html += '<div class="col-lg-6">';
            for (var l = 0; l < langs.length; l++) {
                lang = langs[l];
                custom_field_name_html += '<div class="form-group row trans_field trans_field_' + lang.id_lang + ' ' + (l > 0 ? 'hidden' : '') + '">';
                    custom_field_name_html += '<div class="col-lg-9">';
                        custom_field_name_html += '<input type="text" name="custom_field['+rand_num+'][title][' + lang.id_lang + ']" value="" class="form-control '+(lang.id_lang == currency.id ? 'required' : '')+'" data-error="'+pmf_title_required+'">';
                    custom_field_name_html += '</div>';
                    if(langs.length >1)
                    {
                        custom_field_name_html += '<div class="col-lg-2">';
                            custom_field_name_html += '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
                            custom_field_name_html += lang.iso_code + ' ';
                            custom_field_name_html += '<span class="caret"></span>';
                            custom_field_name_html += '</button>';
                            custom_field_name_html += '<ul class="dropdown-menu">';
                            for (var i = 0; i < langs.length; i++) {
                                custom_field_name_html += '<li><a href="javascript:ets_opcHideOtherLang(' + langs[i].id_lang + ')" title="">' + langs[i].name + '</a></li>';
                            }
                            custom_field_name_html += '</ul>';
                        custom_field_name_html += '</div>';
                    }
                custom_field_name_html += '</div>';
            }
            custom_field_name_html += '</div>';
        custom_field_name_html += '</div>';
    custom_field_name_html += '<div class="form-group row">';
    custom_field_name_html += '<label class="control-label col-lg-3">' + custom_field_type + '</label>';
    custom_field_name_html += '<div class="col-lg-5">';
    custom_field_name_html += '<select name="custom_field['+rand_num+'][type]" class="form-control custom_field_type">';
    custom_field_name_html += '<option value="text" selected>'+Text_type+'</option>';
    custom_field_name_html += '<option value="textarea">'+Textarea_type+'</option>';
    custom_field_name_html += '<option value="radio">'+Radio_type+'</option>';
    custom_field_name_html += '<option value="checkbox">'+Checkbox_type+'</option>';
    custom_field_name_html += '<option value="select">'+Select_type+'</option>';
    custom_field_name_html += '<option value="file">'+File_type+'</option>';
    custom_field_name_html += '<option value="date_time">'+Date_time_type+'</option>';
    custom_field_name_html += '<option value="date">'+Date_type+'</option>';
    custom_field_name_html += '<option value="number">'+Number_type+'</option>';
    custom_field_name_html += '</select>';
    custom_field_name_html += '</div>';
    custom_field_name_html += '</div>';
    
    custom_field_name_html += '<div class="form-group row options" style="display:none">';
        custom_field_name_html += '<label class="control-label required col-lg-3">' + custom_field_options_text + '</label>';
        custom_field_name_html += '<div class="col-lg-6">';
        for (var l = 0; l < langs.length; l++) {
            lang = langs[l];
            custom_field_name_html += '<div class="form-group row trans_field trans_field_' + lang.id_lang + ' ' + (l > 0 ? 'hidden' : '') + '">';
            custom_field_name_html += '<div class="col-lg-9">';
            custom_field_name_html += '<textarea name="custom_field['+rand_num+'][options][' + lang.id_lang + ']" class="form-control"></textarea>';
            custom_field_name_html += '<div class="help-block">'+Each_value_on_1_line_text+'</div>';
            custom_field_name_html += '</div>';
            if(langs.length >1)
            {
                custom_field_name_html += '<div class="col-lg-2">';
                    custom_field_name_html += '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
                    custom_field_name_html += lang.iso_code + ' ';
                    custom_field_name_html += '<span class="caret"></span>';
                    custom_field_name_html += '</button>';
                    custom_field_name_html += '<ul class="dropdown-menu">';
                    for (var i = 0; i < langs.length; i++) {
                        custom_field_name_html += '<li><a href="javascript:ets_opcHideOtherLang(' + langs[i].id_lang + ')" title="">' + langs[i].name + '</a></li>';
                    }
                    custom_field_name_html += '</ul>';
                custom_field_name_html += '</div>';
            }
            custom_field_name_html += '</div>';
        }
        custom_field_name_html += '</div>';   
    custom_field_name_html += '</div>';
    
    custom_field_name_html += '<div class="form-group row">';
        custom_field_name_html += '<label class="control-label col-lg-3">' + custom_field_description_text + '</label>';
        custom_field_name_html += '<div class="col-lg-6">';
        for (var l = 0; l < langs.length; l++) {
            lang = langs[l];
            custom_field_name_html += '<div class="form-group row trans_field trans_field_' + lang.id_lang + ' ' + (l > 0 ? 'hidden' : '') + '">';
            custom_field_name_html += '<div class="col-lg-9">';
            custom_field_name_html += '<textarea name="custom_field['+rand_num+'][description][' + lang.id_lang + ']" class="form-control"></textarea>';
            custom_field_name_html += '</div>';
            if(langs.length >1)
            {
                custom_field_name_html += '<div class="col-lg-2">';
                    custom_field_name_html += '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
                    custom_field_name_html += lang.iso_code + ' ';
                    custom_field_name_html += '<span class="caret"></span>';
                    custom_field_name_html += '</button>';
                    custom_field_name_html += '<ul class="dropdown-menu">';
                    for (var i = 0; i < langs.length; i++) {
                        custom_field_name_html += '<li><a href="javascript:ets_opcHideOtherLang(' + langs[i].id_lang + ')" title="">' + langs[i].name + '</a></li>';
                    }
                    custom_field_name_html += '</ul>';
                custom_field_name_html += '</div>';
            }
            custom_field_name_html += '</div>';
        }
        custom_field_name_html += '</div>';   
        custom_field_name_html += '<div class="col-lg-1">';
            
        custom_field_name_html += '</div>';
    custom_field_name_html += '</div>';
    
    custom_field_name_html += '<div class="form-group row ">';
        custom_field_name_html += '<label class="control-label col-lg-3">'+required_text+'</label>';
        custom_field_name_html += '<div class="col-lg-3">';
            custom_field_name_html += '<select name="custom_field['+rand_num+'][required]" class="form-control">';
                custom_field_name_html += '<option value="1">'+yes_text+'</option>';
                custom_field_name_html += '<option value="0">'+no_text+'</option>';
            custom_field_name_html += '</select>';
        custom_field_name_html += '</div>';
    custom_field_name_html +=  '</div>';

    custom_field_name_html += '<div class="form-group row ">';
        custom_field_name_html += '<label class="control-label col-lg-3">'+Enabled_text+'</label>';
        custom_field_name_html += '<div class="col-lg-9">';
            custom_field_name_html +=  '<span class="switch prestashop-switch fixed-width-lg">';
            custom_field_name_html += '<input type="radio" name="custom_field['+rand_num+'][enable]" id="custom_field_'+rand_num+'_enable_on" value="1" class="custom_field_enable" checked="checked">';
            custom_field_name_html += '<label for="custom_field_'+rand_num+'_enable_on">'+yes_text+'</label>';
            custom_field_name_html += '<input type="radio" name="custom_field['+rand_num+'][enable]" id="custom_field_'+rand_num+'_enable_off" class="custom_field_enable" value="0">';
            custom_field_name_html += '<label for="custom_field_'+rand_num+'_enable_off">'+no_text+'</label>';
            custom_field_name_html += '<a class="slide-button btn"></a>';
            custom_field_name_html += '</span>'
        custom_field_name_html += '</div>';
    custom_field_name_html +=  '</div>';
    custom_field_name_html += '<a class="btn btn-default btn-sm btn-delete-field js-btn-delete-field" href="javascript:void(0)"><span class="ets_svg_icon"><svg viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></span>' + delete_text + '</a>';
    custom_field_name_html += '</div>';

    $(input).closest('.form-group').before(custom_field_name_html);
}
function ets_opc_updateAdditionalFieldOrdering()
{
    if($('#opc_custom_fields_append').length)
    {
        var $myfield = $("#opc_custom_fields_append");
    	$myfield.sortable({
    		opacity: 0.6,
            cursor: 'move',
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updateAdditionalFieldOrdering";	
                var $this=  $(this);					
                $.ajax({
        			type: 'POST',
        			headers: { "cache-control": "no-cache" },
        			url: '',
        			async: true,
        			cache: false,
        			dataType : "json",
        			data:order,
        			success: function(json)
        			{
                        if(json.success)
                        {
                            $.growl.notice({ message: json.success });
                        }
                        if(json.errors)
                        {
                            $.growl.error({message:json.errors});
                            $myfield.sortable("cancel");
                        }
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
    	});
    }
}
function ets_opcHideOtherLang(id_lang) {
    $('.trans_field').addClass('hidden');
    $('.trans_field_' + id_lang).removeClass('hidden');
}