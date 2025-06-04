<?php
use RioVizual\Helpers\Utils;

class OxyEl_Riovizual_Element extends OxyEl {

	public $slug = 'riovizual_table';

	public $js_added = false;

	public $is_oxygen_iframe = false;
	public $is_oxygen_element = false;

	public function custom_init() {

		$this->is_oxygen_iframe = (
			isset($_REQUEST) &&											// phpcs:ignore WordPress.Security.NonceVerification	
			isset($_REQUEST['oxygen_iframe'])							// phpcs:ignore WordPress.Security.NonceVerification
		);

		$this->is_oxygen_element = (
			isset($_REQUEST) &&											// phpcs:ignore WordPress.Security.NonceVerification							
			isset($_REQUEST['action']) &&								// phpcs:ignore WordPress.Security.NonceVerification						
			($_REQUEST['action'] === 'oxy_render_oxy-' . $this->slug)	// phpcs:ignore WordPress.Security.NonceVerification
		);
	}

	public function name() {
		return __('Riovizual', 'riovizual');
	}

	public function slug() {
		return $this->slug;
	}

	public function icon() {
		return 'https://riovizual.com/wp-content/uploads/2025/05/riovizual-white-logo-icon.png';
	}

	public function button_place() {
		return 'first';
	}

	public function controls() {
		// Table selector	        
		$this->addOptionControl([
			'type' 		=> 'dropdown',  
			'name' 		=> __('Select a Table', 'riovizual'),
			'slug' 		=> 'riovizual',
			'default' 	=> 'default',
			'value' 	=> Utils::get_available_tables()
		]);

		$this->addCustomControl(
			'<a href="'.admin_url('post-new.php?post_type=wp_block&plugin=riovizual').'" class="oxygen-button-list-button" target="_blank" style="text-align: center;text-decoration: none;height: 32px;border-radius: var(--oxy-border-radius);background-color: var(--oxy-mid);display: flex;align-items: center;justify-content: center;padding-left: 5px;padding-right: 5px;color: white;font-size: var(--oxy-small-text-size);box-shadow: inset 0 1px 3px 0 rgba(0, 0, 0, 0.12);margin-right: 5px;margin-bottom: 5px;cursor: pointer;line-height: var(--oxy-small-line-height);">'.__('Create New Table', 'riovizual').'</a>',
			'create_table_button'
		);
	}

	public function render($options, $defaults, $content) {
		// Read options
		$table_id = absint(isset($options['riovizual']) ? $options['riovizual'] : 0);
		
		if($table_id === 0) {
			if($this->is_oxygen_element) {
				echo  '<div class="rio_oxygen_no_table_id">Please select a table and press "Apply Params"</div>';
			}	
		} else {
			$shortcode = sprintf('[riovizual id="%s"]', $table_id);
			echo do_shortcode($shortcode);	
		}
	}
}

new OxyEl_Riovizual_Element();