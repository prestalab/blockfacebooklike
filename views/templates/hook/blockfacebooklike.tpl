{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    PrestaShop SA    <contact@prestashop.com>
* @copyright 2007-2014 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

<div class="block facebook-box">
	<h4 class="dropdown-cntrl">{$FB_title|escape:'none'}</h4>
	<div class="dropdown-content">
		<a href="{$FB_page_URL}" target="_blank" class="likeButton">{l s='Like' mod='blockfacebooklike'}</a>
		<div class="block_content">
			<div class="fb_info_top">
				{if $company_logo}
				<img src="https://graph.facebook.com/{$FB_data.id|escape:'none'}/picture" alt="" class="fb_avatar" />
				{/if}
				<div class="fb_info">
					{if $company_name}
					<div>{$FB_data.name|escape:'none'}</div>
					{/if}				
				</div>
			</div>
			<div class="fb_fans">{l s='%s people like' sprintf=$FB_data.likes mod='blockfacebooklike'} <a href="{$FB_page_URL}" target="_blank">{$FB_data.name}</a>.</div>
			{if $show_faces}
			{$err|escape:'none'}
			<ul class="fb_followers">
				{include file="$modulePath"}
			</ul>
			{/if}
		</div>
	</div>
</div>
