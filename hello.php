<?php
/**
 * @package WooCommerce RegMagic Integration
 * @version 0.1
 */
/*
Plugin Name: WooCommerce RegMagic Integration
Description: Build for gledon camp
Author: Dennis Zheng
Version: 1.0
Author URI: https://callmedada.com
Text Domain: glendon-camp-wooreg
*/

function enqueue_select2_jquery() {
    wp_register_style( 'select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
    wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_style( 'select2css' );
    wp_enqueue_script( 'select2' );
}

/**
 * Output campers field.
 */
function iconic_output_campers_field() {
	enqueue_select2_jquery();
	global $product;
	$user = wp_get_current_user();
	$camperfirst2 = get_user_meta( get_current_user_id(), "camperfirst2", true );
	$camperfirst1 = get_user_meta( get_current_user_id(), "camperfirst1", true );
	$camperfirst3 = get_user_meta( get_current_user_id(), "camperfirst3", true );

	?>
	<div class="iconic-campers-field" style="margin-bottom: 20px;">
		<label for="iconic-campers"><?php _e( 'Please choose Campers from your account', 'iconic' ); ?></label>
		<select class="js-example-basic-multiple" name="mycamp" multiple="multiple" id="campers" onchange="PopulateUserName()" style="width: 250px; padding-bottom: 5px;">
		<?php if (!empty($camperfirst1)){
			echo "<option value=".$camperfirst1.">".$camperfirst1." ".get_user_meta( get_current_user_id(), "camperlast1", true )."</option>";
		}
		if (!empty($camperfirst2)){
			echo "<option value=".$camperfirst2.">".$camperfirst2." ".get_user_meta( get_current_user_id(), "camperlast2", true )."</option>";
		}
		if (!empty($camperfirst3)){
			echo "<option value=".$camperfirst3.">".$camperfirst3." ".get_user_meta( get_current_user_id(), "camperlast3", true )."</option>";
		}
		?>
		</select>
		<input style="display:none;" type="text" id="iconic-campers" name="iconic-campers" placeholder="<?php _e( 'Enter campers text', 'iconic' ); ?>" >
	</div>

	<script>


		jQuery(document).ready(function($) {
			$(document).ready(function() {
			$('.js-example-basic-multiple').select2();
			PopulateUserName = function () {
				var dropdown = document.getElementById("campers");
				var field = document.getElementById("iconic-campers");
				var data = $('.js-example-basic-multiple').select2('data')		
				console.log(data)
				var final = ""
				for (var dataatom in data) {
					final += data[dataatom].text.toString() + ", "
				}
				$(field).val(final.slice(0,-2));
				
			}
		});
		});

	</script>

	<?php
}

add_action( 'woocommerce_before_add_to_cart_button', 'iconic_output_campers_field', 10 );


/**
 * Add campers text to cart item.
 *
 * @param array $cart_item_data
 * @param int   $product_id
 * @param int   $variation_id
 *
 * @return array
 */
function iconic_add_campers_text_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
	$campers_text = filter_input( INPUT_POST, 'iconic-campers' );

	if ( empty( $campers_text ) ) {
		return $cart_item_data;
	}

	$cart_item_data['iconic-campers'] = $campers_text;

	return $cart_item_data;
}

add_filter( 'woocommerce_add_cart_item_data', 'iconic_add_campers_text_to_cart_item', 10, 3 );

/**
 * Display campers text in the cart.
 *
 * @param array $item_data
 * @param array $cart_item
 *
 * @return array
 */
function iconic_display_campers_text_cart( $item_data, $cart_item ) {
	if ( empty( $cart_item['iconic-campers'] ) ) {
		return $item_data;
	}

	$item_data[] = array(
		'key'     => __( 'campers', 'iconic' ),
		'value'   => wc_clean( $cart_item['iconic-campers'] ),
		'display' => '',
	);

	return $item_data;
}

add_filter( 'woocommerce_get_item_data', 'iconic_display_campers_text_cart', 10, 2 );

/**
 * Add campers text to order.
 *
 * @param WC_Order_Item_Product $item
 * @param string                $cart_item_key
 * @param array                 $values
 * @param WC_Order              $order
 */
function iconic_add_campers_text_to_order_items( $item, $cart_item_key, $values, $order ) {
	if ( empty( $values['iconic-campers'] ) ) {
		return;
	}

	$item->add_meta_data( __( 'campers', 'iconic' ), $values['iconic-campers'] );
}

add_action( 'woocommerce_checkout_create_order_line_item', 'iconic_add_campers_text_to_order_items', 10, 4 );
