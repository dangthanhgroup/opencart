<?php   
class ControllerCommonHeader extends Controller {
	protected function index() {
		$this->data['title'] = $this->document->getTitle();
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = $this->config->get('config_ssl');
		} else {
			$this->data['base'] = $this->config->get('config_url');
		}
		
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();	 
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		
		$this->language->load('common/header');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_IMAGE;
		} else {
			$server = HTTP_IMAGE;
		}	
				
		if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->data['icon'] = $server . $this->config->get('config_icon');
		} else {
			$this->data['icon'] = '';
		}
		
		$this->data['name'] = $this->config->get('config_name');
				
		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$this->data['logo'] = $server . $this->config->get('config_logo');
		} else {
			$this->data['logo'] = '';
		}
		
		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		$this->data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
    	$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		$this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
		$this->data['text_account'] = $this->language->get('text_account');
    	$this->data['text_checkout'] = $this->language->get('text_checkout');
		
		
    	$this->data['postcode_text'] = $this->language->get('postcode_text');
    	$this->data['postcode_warning'] = $this->language->get('postcode_warning');
    	$this->data['postcode_error'] = $this->language->get('postcode_error');
    	$this->data['postcode_submit'] = $this->language->get('postcode_submit');
    	$this->data['postcode_title'] = $this->language->get('postcode_title');
    	$this->data['postcode_example'] = $this->language->get('postcode_example');
    	$this->data['header_delivery_1'] = $this->language->get('header_delivery_1');
    	$this->data['header_delivery_2'] = $this->language->get('header_delivery_2');
    	$this->data['header_delivery_00_1'] = $this->language->get('header_delivery_00_1');
    	$this->data['header_delivery_00_2'] = $this->language->get('header_delivery_00_2');
    	$this->data['header_delivered_in'] = $this->language->get('header_delivered_in');
    	$this->data['menu_home'] = $this->language->get('menu_home');
    	$this->data['menu_category'] = $this->language->get('menu_category');
    	$this->data['menu_checkout'] = $this->language->get('menu_checkout');
		
    	$this->session->data['postcode_title'] = $this->language->get('postcode_title');
    	$this->session->data['postcode_text'] = $this->language->get('postcode_text');
    	$this->session->data['postcode_warning'] = $this->language->get('postcode_warning');
    	$this->session->data['postcode_error'] = $this->language->get('postcode_error');
    	$this->session->data['postcode_error_to_short'] = $this->language->get('postcode_error_to_short');
    	$this->session->data['postcode_error_to_long'] = $this->language->get('postcode_error_to_long');
    	$this->session->data['postcode_ok'] = $this->language->get('postcode_ok');
    	$this->session->data['postcode_submit'] = $this->language->get('postcode_submit');
    	$this->session->data['postcode_example'] = $this->language->get('postcode_example');
		
		
		$this->data['home'] = $this->url->link('common/home');
		$this->data['wishlist'] = $this->url->link('account/wishlist');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['shopping_cart'] = $this->url->link('checkout/cart');
		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		
		if (isset($this->request->get['filter_name'])) {
			$this->data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$this->data['filter_name'] = '';
		}
		
		// Menu
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		
		$this->data['categories'] = array();
					
		$categories = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories as $category) {
			if ($category['top']) {
				$children_data = array();
				
				$children = $this->model_catalog_category->getCategories($category['category_id']);
				
				foreach ($children as $child) {
					$data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true	
					);		
						
					if ($this->config->get('config_product_count')) {
						$product_total = $this->model_catalog_product->getTotalProducts($data);
						
						$child['name'] .= ' (' . $product_total . ')';
					}
								
					$children_data[] = array(
						'name'  => $child['name'],
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])	
					);					
				}
				
				// Level 1
				$this->data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}
		
		$this->children = array(
			'module/language',
			'module/currency',
			'module/cart'
		);
				
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/header.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/common/header.tpl';
		} else {
			$this->template = 'default/template/common/header.tpl';
		}
		
    	$this->render();
	} 	
}
?>