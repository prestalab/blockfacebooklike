<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class BlockFaceBookLike extends Module
{
	private $html = '';
	private $post_errors = array();

	public function __construct()
	{
		$this->name = 'blockfacebooklike';
		$this->tab = 'front_office_features';
		$this->version = '1.3.1';
		$this->author = 'prestapro.ru';
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Facebook Like Box');
		$this->description = $this->l('Put your Facebook fan page like box on your site.');
		$this->cache_name = $_SERVER['DOCUMENT_ROOT']
			.Context::getContext()->shop->physical_uri.'modules/'.$this->name.'/views/templates/hook/fans.tpl';
	}

	public function install()
	{
		if (!parent::install() ||
			!$this->registerHook('footer') ||
			!$this->registerHook('header') ||
			!Configuration::updateValue('PLLB_URL', 'prestapro.ru') ||
			!Configuration::updateValue('PLLB_TITLE', 'Facebook') ||
			!Configuration::updateValue('PLLB_FACES', 1) ||
			!Configuration::updateValue('PLLB_COMPANY_NAME', 0) ||
			!Configuration::updateValue('PLLB_COMPANY_LOGO', 0) ||
			!Configuration::updateValue('PLLB_NUM', 8))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() ||
		!Configuration::deleteByName('PLLB_URL') ||
		!Configuration::deleteByName('PLLB_NUM') ||
		!Configuration::deleteByName('PLLB_TITLE') ||
		!Configuration::deleteByName('PLLB_COMPANY_NAME') ||
		!Configuration::deleteByName('PLLB_COMPANY_LOGO') ||
		!Configuration::deleteByName('PLLB_FACES'))
			return false;
		return true;
	}
	private function postProcess()
	{
		Configuration::updateValue('PLLB_URL', Tools::getValue('url'));
		Configuration::updateValue('PLLB_TITLE', Tools::getValue('title'));
		Configuration::updateValue('PLLB_FACES', Tools::getValue('faces_true'));
		Configuration::updateValue('PLLB_COMPANY_NAME', Tools::getValue('company_name_true'));
		Configuration::updateValue('PLLB_COMPANY_LOGO', Tools::getValue('company_logo_true'));
		Configuration::updateValue('PLLB_NUM', Tools::getValue('num'));
		$this->html .= '<div class="conf confirm alert alert-success">'.$this->l('Settings updated').'</div>';
	}
	public function getContent()
	{
		$msg = '';
		$this->html .= '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submit') && !Tools::isSubmit('cleanCache'))
		{
			if (!count($this->post_errors))
				$this->postProcess();
			else
			{
				foreach ($this->post_errors as $err)
					$this->html .= '<div class="alert error alert-danger">'.$err.'</div>';
			}
		}
		if (Tools::isSubmit('cleanCache'))
			$msg = $this->cleanCache();
		$this->displayForm($msg);
		return $this->html;
	}
	private function displayForm($message)
	{
		if (!(Language::getLanguages()))
			return false;
		$fields = array(
			array(
				'form' => array(
					'legend' => array(
						'title' => $this->l('Settings')
					),
					'input' => array(
						array(
							'label' => $this->l('Box Title'),
							'type' => 'text',
							'name' => 'title',
							'hint' => $this->l('The title of box on the front page')
						),
						array(
							'label' => $this->l('Facebook Page Name'),
							'type' => 'text',
							'name' => 'url',
							'hint' => $this->l('The name of the Facebook Page')
						),
						array(
							'label' => $this->l('Face number'),
							'type' => 'text',
							'name' => 'num',
							'hint' => $this->l('Nubmer of visible faces')
						),
						array(
							'label' => $this->l('Show Faces'),
							'type' => 'checkbox',
							'name' => 'faces',
							'values' => array(
								'query' => array(
									array(
										'val' => 'true',
										'text' => ''
									)
								),
								'id' => 'val',
								'name' => 'text'
							)
						),
						array(
							'label' => $this->l('Show Company name'),
							'type' => 'checkbox',
							'name' => 'company_name',
							'values' => array(
								'query' => array(
									array(
										'val' => 'true',
										'text' => ''
									)
								),
								'id' => 'val',
								'name' => 'text'
							)
						),
						array(
							'label' => $this->l('Show Company Logo'),
							'type' => 'checkbox',
							'name' => 'company_logo',
							'values' => array(
								'query' => array(
									array(
										'val' => 'true',
										'text' => ''
									)
								),
								'id' => 'val',
								'name' => 'text'
							)
						),
					),
					'submit' => array(
						'title' => $this->l('Update')
					),
					'buttons' => array(
						'clear_cache' => array(
							'name' => 'cleanCache',
							'type' => 'submit',
							'title' => $this->l('Clean cache'),
							'icon' => 'process-icon-remove'
						)
					)
				)
			)
		);
		$helper_form = new HelperForm();
		$helper_form->submit_action = 'submit';
		$helper_form->fields_value = array(
			'title' => Configuration::get('PLLB_TITLE'),
			'url' => Configuration::get('PLLB_URL'),
			'num' => Configuration::get('PLLB_NUM'),
			'faces_true' => Configuration::get('PLLB_FACES'),
			'company_name_true' => Configuration::get('PLLB_COMPANY_NAME'),
			'company_logo_true' => Configuration::get('PLLB_COMPANY_LOGO')
		);
		$helper_form->currentIndex = $_SERVER['REQUEST_URI'];
		$helper_form->token = Tools::getValue('token');
		$this->html .= $message.$helper_form->generateForm($fields);
		$this->html .= '<style>
			.process-icon-remove:before
			{
				content: "\f014";
			}
		</style>';
	}
	public function getFacebookData($name)
	{
		$clr_data = array();
		$data = array();
		$facebook_info = explode(',', Tools::file_get_contents('https://graph.facebook.com/'.$name));
		$waste = array("'{'", "'}'", "'\"'");
		foreach ($facebook_info as $key => $info)
		{
			$tmp = explode('":"', preg_replace($waste, '', $info));
			$clr_data[$key] = $tmp[0];
		}
		foreach ($clr_data as $k => $v)
			$data[$k] = explode(':', $v);
		foreach ($data as $id => $value)
		{
			$this->n($id);
			if ($value[0] == 'name') Configuration::updateValue('PLLB_DATA_NAME', $value[1]);
			if ($value[0] == 'likes') Configuration::updateValue('PLLB_DATA_LIKES', $value[1]);
			if ($value[0] == 'id') Configuration::updateValue('PLLB_DATA_ID', $value[1]);
		}

	}
	public function getAttribute($attrib, $tag)
	{
		$re = '/'.$attrib.'=["\']?([^"\' ]*)["\' ]/is';
		preg_match($re, $tag, $match);
		if ($attrib == 'src')
			$match[1] = str_replace('&amp;', '&', $match[1]);
		if ($match)
			return urldecode($match[1]);
		else
			return false;
	}

	public function cleanCache()
	{
		if (file_exists($this->cache_name) && !unlink($this->cache_name))
			$cleaned = '<div class="alert error alert-danger">Cache doesn\'t cleaned</div>';
		else
			$cleaned = '<div class="alert conf confirm alert-success">Cache has been cleaned</div>';
		return $cleaned;
	}

	public function hookFooter()
	{
		$fb_data = array();
		$facebook_username = Configuration::get('PLLB_URL');
		$cache_name = $this->cache_name;
		$err = '';
		// generate the cache version if it doesn't exist or it's too old!
		$age_in_seconds = 3600; // 1 hour

		if (!file_exists($cache_name) || (filemtime($cache_name) + $age_in_seconds < time()) && function_exists('curl_init'))
		{
			//$dom = simplexml_load_file($cacheName);
			$curl = 'https://www.facebook.com/plugins/likebox.php?href=https://www.facebook.com/'.$facebook_username;
			$ch = curl_init($curl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20100101 Firefox/15.0.1');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			$result = curl_exec($ch);
			curl_close($ch);
			$doc = new DOMDocument('1.0', 'utf-8');
			ini_set('display_errors', 'off');
			$doc->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'.$result);
			//print_r($doc->saveHTML());
			$people_list = array();
			$i = 0;

			foreach ($doc->getElementsByTagName('ul')->item(0)->childNodes as $child)
			{
				if ($i < Configuration::get('PLLB_NUM'))
				{
					$raw = $doc->saveXML($child);
					$li = preg_replace('/<li[^>]+\>/i', '', $raw);
					$people_list[$i] = preg_replace('/<\/li>/i', '', $li);
					$i++;
				}
			}
			if (!$file_holder = fopen($cache_name, 'w'))
				$err = "Can't open settings file!".$cache_name.'<br/>';
			else
			{
				$license = '{*
* 2007-2015 PrestaShop
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
* @copyright 2007-2015 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

';
				fwrite($file_holder, $license);
				foreach ($people_list as $key => $code)
				{
					$this->n($key);
					$name = $this->getAttribute('title', $code);
					$image = $this->getAttribute('src', $code);
					$link = $this->getAttribute('href', $code);

					$data = Tools::file_get_contents($image);
					$img_in_base64 = 'data:image/jpg;base64,'.call_user_func('base64_encode', $data);

					if ($link != '')
						$wrapper = '<a href='.$link.' title='.$name.' target="_blank"><img src="'.$img_in_base64.'" alt="" /></a>';
					else
						$wrapper = '<span title='.$name.'><img src="'.$img_in_base64.'" alt="" /></span>';

					$li = '<li>'.$wrapper.'<div class="fb_name">'.$name.'</div></li>';
					if (!fwrite($file_holder, $li))
						$err = 'Can\'t write settings! '.$cache_name.'<br/>';
				}
				fclose($file_holder);
				$this->getFacebookData($facebook_username);
			}
		}

		$fb_data['name'] = Configuration::get('PLLB_DATA_NAME');
		$fb_data['likes'] = Configuration::get('PLLB_DATA_LIKES');
		$fb_data['id'] = Configuration::get('PLLB_DATA_ID');

		$this->smarty->assign(array(
			'FB_page_URL' => 'https://www.facebook.com/'.$facebook_username,
			'FB_data' => $fb_data,
			'show_faces' => Configuration::get('PLLB_FACES'),
			'FB_title' => Configuration::get('PLLB_TITLE'),
			'company_name' => Configuration::get('PLLB_COMPANY_NAME'),
			'company_logo' => Configuration::get('PLLB_COMPANY_LOGO'),
			'modulePath' => $this->cache_name,
			'err' => $err
		));

		return $this->display(__FILE__, 'views/templates/hook/blockfacebooklike.tpl');
	}
	public function hookHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/blockfacebooklike.css', 'all');
	}
	public function hookRightColumn($params)
	{
		return $this->hookFooter($params);
	}

	public function hookLeftColumn($params)
	{
		return $this->hookFooter($params);
	}
	public function n($var)
	{
		return $var;
	}
}