<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2021, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\AvaTax\API\Responses;

use WC_AvaTax_API_Response;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API nexus list response class.
 *
 * @since 1.13.0
 */
class Nexus_List_Response extends WC_AvaTax_API_Response {


	/**
	 * Gets the supported country list from Nexus.
	 *
	 * @since 1.13.0
	 *
	 * @return string[]
	 */
	public function get_country_list() : array {

		// filters all the available locations to get only their countries
		return array_unique( array_map( static function( $location ) {
			return $location->country;
		}, $this->response_data->value ?? [] ) );
	}


}
