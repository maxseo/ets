{*
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
* needs please, contact us for extra customization service at an affordable price
*
*  @author ETS-Soft <etssoft.jsc@gmail.com>
*  @copyright  2007-2022 ETS-Soft
*  @license    Valid for 1 website (or project) for each purchase of license
*  International Registered Trademark & Property of ETS-Soft
*}


<div class="row product-line-grid align-items-center">
  <!--  product left content: image-->
    <div class="col-auto no-padr text-right">
        <div class="cart-line-product-actions">
          <a
              class                       = "remove-from-cart"
              rel                         = "nofollow"
              href                        = "{$product.remove_from_cart_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
              data-link-action            = "delete-from-cart"
              data-id-product             = "{$product.id_product|escape:'javascript':'UTF-8'}"
              data-id-product-attribute   = "{$product.id_product_attribute|escape:'javascript':'UTF-8'}"
              data-id-customization   	  = "{$product.id_customization|escape:'javascript':'UTF-8'}"
              data-id-attribute           = "{$product.id_combination|intval}"
          >
            {if !isset($product.is_gift) || !$product.is_gift}
            <i class="lni lni-close"></i>
            {/if}
          </a>
        
          {block name='hook_cart_extra_product_actions'}
            {hook h='displayCartExtraProductActions' product=$product}
          {/block}
        
        </div>
    </div>
  <div class="product-line-grid-left col-2">
    <span class="product-image media-middle">
    {if $lpb_miniature_settings.show_rect_img == 'on'}
        <img src="{$product.cover.bySize.cart_rect.url}" alt="{$product.name|escape:'quotes':'UTF-8'}">
    {else}
        <img src="{$product.cover.bySize.cart_default.url}" alt="{$product.name|escape:'quotes':'UTF-8'}">
    {/if}
    </span>
  </div>


  <div class="product-line-grid-body col">
    <div class="product-line-info product-price {if $product.has_discount}has-discount{/if}">
      {if $product.has_discount}
        <div class="product-discount">
          <span class="regular-price">{$product.regular_price}</span>
          {if $product.discount_type === 'percentage'}
            <span class="discount discount-percentage">
                -{$product.discount_percentage_absolute}
              </span>
          {else}
            <span class="discount discount-amount">
                -{$product.discount_to_display}
              </span>
          {/if}
        </div>
      {/if}
      <div class="current-price">
        <span class="price">{$product.price}</span>
        {if $product.unit_price_full}
          <div class="unit-price-cart">{$product.unit_price_full}</div>
        {/if}
      </div>
      {hook h='displayProductPriceBlock' product=$product type="unit_price"}
    </div>
    
    <div class="product-line-info">
      <a class="product-name" href="{$product.url}" data-id_customization="{$product.id_customization|intval}">{$product.name}</a>
    </div>
   

    {foreach from=$product.attributes key="attribute" item="value"}
      <div class="product-line-info">
        <span class="label">{$attribute}:</span>
        <span class="value">{$value}</span>
      </div>
    {/foreach}

    {if is_array($product.customizations) && $product.customizations|count}
      <br>
      {block name='cart_detailed_product_line_customization'}
        {foreach from=$product.customizations item="customization"}
          <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
          <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                </div>
                <div class="modal-body">
                  {foreach from=$customization.fields item="field"}
                    <div class="product-customization-line row">
                      <div class="col-sm-3 col-4 label">
                        {$field.label}
                      </div>
                      <div class="col-sm-9 col-8 value">
                        {if $field.type == 'text'}
                          {if (int)$field.id_module}
                            {$field.text|cleanHtml nofilter}
                          {else}
                            {$field.text}
                          {/if}
                        {elseif $field.type == 'image'}
                          <img src="{$field.image.small.url}">
                        {/if}
                      </div>
                    </div>
                  {/foreach}
                </div>
              </div>
            </div>
          </div>
        {/foreach}
      {/block}
    {/if}
  </div>


  <div class="product-line-grid-right col-lg-4 col-md-5 col-3">
    <div class="row align-items-center">

      <div class="col-md-6 col-12 no-padl-sm-down mx-auto text-center price">
        <span class="product-price">
          <strong>
            {if isset($product.is_gift) && $product.is_gift}
              <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
            {else}
              {$product.total}
            {/if}
          </strong>
        </span>
        
      </div>
      
      <div class="col-md-6 col-12 no-padl-sm-down mx-auto text-center cart-qty">
        {if isset($product.is_gift) && $product.is_gift}
          <span class="gift-quantity">{$product.quantity|intval}</span>
        {else}
          <input
            class="js-cart-line-product-quantity"
            data-down-url="{$product.down_quantity_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
            data-up-url="{$product.up_quantity_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
            data-update-url="{$product.update_quantity_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
            data-product-id="{$product.id_product|intval}"
            type="number"
            value="{$product.quantity|intval}"
            name="product-quantity-spin"
            min="{$product.minimal_quantity}"
          />
        {/if}
      </div>
      

      
    </div>
  </div>

  <div class="clearfix"></div>
</div>

{*
<div class="product-line-grid">
  <!--  product line left content: image-->
  <div class="product-line-grid-left col-md-3 col-xs-4">
    <span class="product-image media-middle">
      {if $product.cover}
        <img src="{$product.cover.bySize.cart_default.url|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
      {else}
        <img src="{$urls.no_picture_image.bySize.cart_default.url|escape:'html':'UTF-8'}" />
      {/if}
    </span>
  </div>

  <!--  product line body: label, discounts, price, attributes, customizations -->
  <div class="product-line-grid-body col-md-4 col-xs-8">
    <div class="product-line-info">
      <a class="label" href="{$product.url|escape:'html':'UTF-8'}" data-id_customization="{$product.id_customization|intval}">{$product.name|escape:'html':'UTF-8'}</a>
    </div>

    <div class="product-line-info product-price h5 {if $product.has_discount}has-discount{/if}">
      {if $product.has_discount}
        <div class="product-discount">
          <span class="regular-price">{$product.regular_price|escape:'html':'UTF-8'}</span>
          {if $product.discount_type === 'percentage'}
            <span class="discount discount-percentage">
                -{$product.discount_percentage_absolute|escape:'html':'UTF-8'}
              </span>
          {else}
            <span class="discount discount-amount">
                -{$product.discount_to_display|escape:'html':'UTF-8'}
              </span>
          {/if}
        </div>
      {/if}
      <div class="current-price">
        <span class="price">{$product.price|escape:'html':'UTF-8'}</span>
        {if $product.unit_price_full}
          <div class="unit-price-cart">{$product.unit_price_full|escape:'html':'UTF-8'}</div>
        {/if}
      </div>
    </div>

    <br/>

    {foreach from=$product.attributes key="attribute" item="value"}
      <div class="product-line-info">
        <span class="label">{$attribute|escape:'html':'UTF-8'}:</span>
        <span class="value">{$value|escape:'html':'UTF-8'}</span>
      </div>
    {/foreach}
    {if is_array($product.customizations) && $product.customizations|count}
      <br>
      {block name='cart_detailed_product_line_customization'}
        {foreach from=$product.customizations item="customization"}
          <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization|intval}">{l s='Product customization' mod='ets_extraoptions'}</a>
          <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization|intval}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='ets_extraoptions'}">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <h4 class="modal-title">{l s='Product customization' mod='ets_extraoptions'}</h4>
                </div>
                <div class="modal-body">
                  {foreach from=$customization.fields item="field"}
                    <div class="product-customization-line row">
                      <div class="col-sm-3 col-xs-4 label">
                        {$field.label|escape:'html':'UTF-8'}
                      </div>
                      <div class="col-sm-9 col-xs-8 value">
                        {if $field.type == 'text'}
                          {if (int)$field.id_module}
                            {$field.text nofilter}
                          {else}
                            {$field.text|escape:'html':'UTF-8'}
                          {/if}
                        {elseif $field.type == 'image'}
                          <img src="{$field.image.small.url|escape:'html':'UTF-8'}">
                        {/if}
                      </div>
                    </div>
                  {/foreach}
                </div>
              </div>
            </div>
          </div>
        {/foreach}
      {/block}
    {/if}
  </div>

  <!--  product line right content: actions (quantity, delete), price -->
  <div class="product-line-grid-right product-line-actions col-md-5 col-xs-12">
    <div class="row">
      <div class="col-xs-4 hidden-md-up"></div>
      <div class="col-md-10 col-xs-6">
        <div class="row">
          <div class="col-md-6 col-xs-6 qty">
            {if isset($product.is_gift) && $product.is_gift}
              <span class="gift-quantity">{$product.quantity|intval}</span>
            {else}
              <input
                class="js-cart-line-product-quantity"
                data-down-url="{$product.down_quantity_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
                data-up-url="{$product.up_quantity_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
                data-update-url="{$product.update_quantity_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
                data-product-id="{$product.id_product|intval}"
                type="number"
                value="{$product.quantity|intval}"
                name="product-quantity-spin"
              />
            {/if}
          </div>
          <div class="col-md-6 col-xs-2 price">
            <span class="product-price">
              <strong>
                {if isset($product.is_gift) && $product.is_gift}
                  <span class="gift">{l s='Gift' mod='ets_extraoptions'}</span>
                {else}
                  {$product.total|escape:'html':'UTF-8'}
                {/if}
              </strong>
            </span>
          </div>
        </div>
      </div>
      <div class="col-md-2 col-xs-2 text-xs-right">
        <div class="cart-line-product-actions">
          <a
              class                       = "remove-from-cart"
              rel                         = "nofollow"
              href                        = "{$product.remove_from_cart_url|escape:'html':'UTF-8'}&id_combination={$product.id_combination|intval}"
              data-link-action            = "delete-from-cart"
              data-id-product             = "{$product.id_product|intval}"
              data-id-product-attribute   = "{$product.id_product_attribute|intval}"
              data-id-customization   	  = "{$product.id_customization|intval}"
              data-id-attribute           = "{$product.id_combination|intval}"
          >
            {if !isset($product.is_gift) || !$product.is_gift}
            <i class="material-icons float-xs-left">delete</i>
            {/if}
          </a>

          {block name='hook_cart_extra_product_actions'}
            {hook h='displayCartExtraProductActions' product=$product}
          {/block}

        </div>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>
</div>
*}