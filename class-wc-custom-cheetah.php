<?php // phpcs:disable
/**
 * Cheetah Payment Gateway.
 *
 * Provides a Cheetah Payment Gateway.
 *
 * @class       WC_Custom_Cheetah
 * @extends     WC_Payment_Gateway
 * @since       1.0.0
 * @package     WooCommerce/Classes/Payment
 * @author      guillaume
 */

if ( !defined('ABSPATH')){
    exit;
}

/**
 * WC_Custom_Cheetah class.
 */

class WC_Custom_Cheetah extends WC_Payment_Gateway {
    /** @var bool Whether or not logging is enabled */
	public static $log_enabled = false;
    public $api_key = "";
    /** @var WC_Logger Logger instance */
	public static $log = false;
	/**
	 * Constructor for the gateway.
	 */
    public function __construct()
    {
        $this->id = 'cheetah';
        $this->has_fields = false;
        $this->order_button_text = __('Proceed to Cheetah','cheetah');
        $this->method_title = __('Cheetah','cheetah');
        $this->method_description = '';

        // Timeout after 3 days. Default to 3 days as pending Bitcoin txns
		// are usually forgotten after 2-3 days.

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
        $this->title       = $this->get_option( 'title' );
		$this->description = $this->get_option( 'description' );
		$this->debug       = 'yes' === $this->get_option( 'debug', 'no' );

		self::$log_enabled = $this->debug;

        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'woocommerce_validate_api_key' ) );
        
		// add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, '_custom_query_var' ), 10, 2 );
		// add_action( 'woocommerce_api_wc_gateway_coinbase', array( $this, 'handle_webhook' ) );
    }

    public function woocommerce_validate_api_key() {
        $api_key = isset($_POST['woocommerce_cheetah_api_key']) ? $_POST['woocommerce_cheetah_api_key'] : '';
        $this->api_key = $api_key;
        // update_option('custom_cheetah_api_key', $api_key);
        $result = $this->validate_api_key($api_key);
        if ( !$result['result'] ){
            $this->add_error(__('Invalid API key. Please enter a valid key.','cheetah'));
            update_option('custom_cheetah_api_key',$api_key);
            update_option('custom_cheetah_api_key_success',false);
            update_option('custom_cheetah_api_key_error', true);
        } else {
            update_option('custom_cheetah_api_key', $api_key);
            update_option('custom_cheetah_api_key_error', false);
            update_option('custom_cheetah_api_key_success',true);
            
        }
    }

    public function generate_settings_html($form_fields = array(),$echo = true) {
        $html = "<table class = 'form-table'><tbody>";
        foreach($this->form_fields as $key=>$value){
            $html .= "<tr valign = 'top'>
                <th scope = 'row' class='titledesc'>
                    <label class='woocommerce_cheetah_".$key."'>".$value["title"]."</label>
                    <span class= 'woocommerce_help_tip'></span>
                </th>
                <td class='forminp'>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span>".$value["title"]."</span>
                        </legend>
                        <label for='woocommerce_cheetah_".$key."'>
                            <input type='".$value['type']."' name = 'woocommerce_cheetah_".$key."' id = 'woocommerce_cheetah_".$key."' value = '".($key != "api_key"? $value['default'] : get_option('custom_cheetah_api_key'))."'/>
                        </label><br>
                    </fieldset>
                </td>
            </tr>";
        }
        if ( get_option( 'custom_cheetah_api_key_error' ) ) {
            $html .= "<tr valign='top'><div class='notice notice-error'>
                <p> Invalid API key. Please enter a valid key.</p>
            </div></tr>";
        } else if ( get_option('custom_cheetah_api_key_success')){
            $html .= "<tr valign ='top'><div class='notice notice-success'>
                <p> API key validated successfully!</p>
            </div></tr>" ;
        }
        $html .= "</tbody></table>";
        echo $html;
    }
    public static function log( $message, $level = 'info' ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = wc_get_logger();
			}
			self::$log->log( $level, $message, array( 'source' => 'cheetah' ) );
		}
	}

    public function init_form_fields() {
		$this->form_fields = array(
			'enabled'        => array(
				'title'   => __( 'Enable/Disable', 'woocommerce' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Custom Cheetah Payment', 'cheetah' ),
				'default' => 'yes',
			),
			'title'          => array(
				'title'       => __( 'Title', 'woocommerce' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Cheetah', 'cheetah' ),
				'desc_tip'    => true,
			),
			'description'    => array(
				'title'       => __( 'Description', 'woocommerce' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
				'default'     => __( 'Cheetah payment with bitcoin, etherium', 'cheetah' ),
			),
			'api_key'        => array(
				'title'       => __( 'API Key', 'woocommerce' ),
				'type'        => 'text',
				'default'     => $this->get_option('custom_cheetah_api_key'),
				'description' => 'This controls the API key to query server',
			)
		);
	}
    public function validate_api_key($api_key) {
        $graphql_url = 'https://cheetah-backend.herokuapp.com/graphql';
        $query = 'query { validateApiKey(apiKey : "'.$api_key.'") }';
        $response = wp_remote_get($graphql_url, array(
          'headers' => array(
            'Content-Type' => 'application/json'
          ),
          'body' => array(
            'query' => $query
          )
        ));
        if (is_wp_error($response)) {
          return false;
        }
      
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        if (!empty($data['errors'])) {
            // Show error modal
            $result['message'] = $data['errors'][0]['message'];
            $result['result'] = false;
            return $result;
        } else {
            $result['result'] = true;
            $result['message'] = 'API key is validated successfully';
            return $result;
        }
    }
    public function get_api_key() {
        return $this->api_key;
    }

    /**
	 * Process the payment and return the result.
	 * @param  int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
		// Mark order as completed
		$order = wc_get_order( $order_id );
		// $order->payment_complete();
	
		// Redirect to custom HTML page
		$redirect_url = home_url().'/cheetah';
		WC()->session->delete_session('order_id');
		WC()->session->set('order_id',$order_id);
		WC()->session->set('order_product',$order);
		return array(
			'result' => 'success',
			'redirect' => $redirect_url
		);
	}
}