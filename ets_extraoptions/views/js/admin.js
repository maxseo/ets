/**
 * 2007-2022 ETS-Soft
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
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
 *  @copyright  2007-2022 ETS-Soft
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of ETS-Soft
 */
$(document).ready(function(){
    ets_eto_sort_order_attribute();
    setTimeout(function(){ets_eto_hidden_alert();},3000);
    $(document).on('click','input[name="display_by_option_group"]',function(){
        if($('input[name="display_by_option_group"]:checked').val()==1)
            $('.form-group.id_ets_eto_attr_group').show();
        else
            $('.form-group.id_ets_eto_attr_group').hide();
    });
    $(document).on('click','#list-ca_attribute .list-action,#list-ca_specific_attribute .list-action',function(){
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
                            $this.removeClass('action-disabled').addClass('action-enabled').find('i').removeClass('fa-remove').removeClass('icon-remove').addClass('fa-check').addClass('icon-check');
                        }                        
                        else
                        {
                            $this.removeClass('action-enabled').addClass('action-disabled').find('i').removeClass('fa-check').removeClass('icon-check').addClass('fa-remove').addClass('icon-remove');
                        }
                        $this.attr('href',json.href);
                        $this.removeClass('disabled');
                        if(json.title)
                            $this.attr('title',json.title); 
                        showSuccessMessage(json.success);
                    }
                    if(json.errors)
                        showErrorMessage(json.errors);
                        
                                                                
                },
                error: function(error)
                {                                      
                    $this.removeClass('disabled');
                }
            });
        }
        return false;
    }); 
   $(document).on('click','.add_new_link',function(e){
        $('.module_error.alert').parent().remove();
        if($('.ets_eto_popup #ets_eto_attribute_form').length)
        {
            e.preventDefault();
            $('.ets_eto_popup').addClass('show');
            $('input[id^="name_"]').val('');
            $('textarea[id^="description_"]').val('');
            $('input#id_attribute').val('');
            $('input#price').val('');
            $('input#active_on').click();
            $('input#used_on').click();
            $('input#required_off').click();
            $('input#checked_off').click();
            $('input#use_tax_off').click();
            $('input#use_discount_off').click();
            $('input#display_by_option_group_off').click();
            if($('input[name="display_by_option_group"]:checked').val()==1)
                $('.form-group.id_ets_eto_attr_group').show();
            else
                $('.form-group.id_ets_eto_attr_group').hide();
        }
        if($('.ets_eto_popup #ets_eto_attr_group_form').length)
        {
            e.preventDefault();
            $('.ets_eto_popup').addClass('show');
            $('input[id^="name_"]').val('');
            $('#id_ets_eto_attr_group').val('');
        }   
   });
   $(document).on('click','.link_edit',function(e){
        if($('.ets_eto_popup #block-form-add-new-attributes').length)
        {
            e.preventDefault();
            if(!$(this).hasClass('loading'))
            {
                $(this).addClass('loading');
                var $this = $(this);
                var add_new_link = $(this).attr('href');
                $.ajax({
                    url: add_new_link,
                    data: 'ajax=1',
                    type: 'post',
                    dataType: 'json',                
                    success: function(json){   
                        $this.removeClass('loading');
                        $('#block-form-add-new-attributes').html(json.html_form);
                        $('.ets_eto_popup').addClass('show');
                        if($('input[name="display_by_option_group"]').length)
                        {
                            if($('input[name="display_by_option_group"]:checked').val()==1)
                                $('.form-group.id_ets_eto_attr_group').show();
                            else
                                $('.form-group.id_ets_eto_attr_group').hide();
                        }
                        
                    },
                    error: function(error)
                    {   
                        $this.removeClass('loading');
                    }
                });
            }
        }
   });
   $(document).on('click','.link_delete',function(e){
        e.preventDefault();
        if(confirm(confirm_delete_text))
        {
            if(!$(this).hasClass('loading'))
            {
                $(this).addClass('loading');
                var $this = $(this);
                var delete_link = $(this).attr('href');
                $.ajax({
                    url: delete_link,
                    data: 'ajax=1',
                    type: 'post',
                    dataType: 'json',                
                    success: function(json){   
                    if(json.success)
                    {
                        showSuccessMessage(json.success);
                        $this.parents('tr').remove();
                    }
                    if(json.errors)
                    {
                        showErrorMessage(json.errors);
                    }
                    },
                    error: function(error)
                    {   
                        $this.removeClass('loading');
                    }
                });
            }
        }
   });
   $(document).on('click','button[name="saveAttribute"],button[name="saveAttributeGroup"]',function(e){
        e.preventDefault();
        $('.module_error.alert').parent().remove();
        var $this = $(this);
        if(!$this.hasClass('loading'))
        {
            var formData = new FormData($this.parents('form').get(0));
            formData.append('ajax', 1);
            $this.addClass('loading');
            $.ajax({
                url: '',
                data: formData,
                type: 'post',
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(json){
                    $this.removeClass('loading');
                    if(json.success)
                    {
                        showSuccessMessage(json.success);
                        if(json.list_attributes)
                        {
                            if(json.specific)
                                $('.list-table-product-attributes').replaceWith(json.list_attributes);
                            else
                            {
                                $('.list-table-attributes').replaceWith(json.list_attributes);
                                ets_eto_sort_order_attribute();
                            }
                        }
                        if(json.list_attribute_groups)
                        {
                            $('.ets_extraoptions-panel.list-attribute-groups').replaceWith(json.list_attribute_groups);
                            ets_eto_sort_order_attribute();
                        }
                        $('.ets_eto_popup').removeClass('show');
                    }
                    if(json.errors)
                    {
                        $this.parent().before(json.errors)
                    }
                },
                error: function(xhr, status, error)
                {   
                    alert('error');
                    $this.removeClass('loading');
                }
            });
        }
   });
});
function ets_eto_sort_order_attribute()
{
    if($('.list-ca_attribute').length || $('#list-ca_attribute_group').length)
    {
        var $myAttributes = $(".list-ca_attribute").length  ? $(".list-ca_attribute"):$('#list-ca_attribute_group');
    	$myAttributes.sortable({
    		opacity: 0.6,
            handle: ".dragHandle",
    		update: function() {
    			var order = $(this).sortable("serialize") + "&action=updateAttributeOrdering";						
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
                        {
                            showSuccessMessage(jsonData.success);
                            var i=1;
                            if(jsonData.id_group!='' && $('.attribute-group-item-'+jsonData.id_group).length)
                            {
                                $('.attribute-group-item-'+jsonData.id_group+' .dragGroup span').each(function(){
                                    $(this).html(i+(jsonData.page-1)*20);
                                    i++;
                                });
                            }
                            else
                            {
                                $('.dragGroup span').each(function(){
                                    $(this).html(i+(jsonData.page-1)*20);
                                    i++;
                                });
                            }
                        }
                        if(jsonData.errors)
                        {
                            showErrorMessage(jsonData.errors);
                            $myAttributes.sortable("cancel");
                        }
                    }
        		});
    		},
        	stop: function( event, ui ) {
       		}
    	});
   }
}
function ets_eto_hidden_alert()
{
    if($('.module_error.alert.alert-danger').length)
    {
        $('.module_error.alert.alert-danger').parent().remove();
    }
    if($('.module_confirmation.alert.alert-success').length)
    {
        $('.module_confirmation.alert.alert-success').remove();
    }
}