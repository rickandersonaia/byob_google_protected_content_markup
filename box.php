<?php

/*
  Name: BYOB Google Protected Content Markup
  Author: Rick Anderson - BYOBWebsite.com
  Version: 2.5
  Requires: 2.5
  Description: This adds the option of using protected content markup that Google requires if you are exposing your paywall content to Google.  The settings are found in Template Options and must be set for each template.  It also adds a JSON LD object for each protected section on a page.
  Class: byob_google_protected_content_markup
  Docs: https://www.byobwebsite.com/
  License: MIT

  Copyright 2017 BYOBWebsite.
  DIYthemes, Thesis, and the Thesis Theme are registered trademarks of DIYthemes, LLC.

  Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 */


class byob_google_protected_content_markup extends thesis_box {


	public $type = false;

	public function translate() {
		$this->title = __( 'BYOB Google Protected Content Markup', 'byobgpcm' );
	}

	/**
	 *  Box API method of providing a pseudo constructor method
	 */
	protected function construct() {
		global $byob_ah;;

		if ( ! defined( 'BYOBGPCM_PATH' ) ) {
			define( 'BYOBGPCM_PATH', dirname( __FILE__ ) );
		}
		if ( ! defined( 'BYOBGPCM_URL' ) ) {
			define( 'BYOBGPCM_URL', THESIS_USER_BOXES . '/' . basename( __DIR__ ) );
		}


		if ( is_admin() ) {
			if ( ! class_exists( 'byob_asset_handler' ) ) {
				include_once( BYOBGPCM_PATH . '/byob_asset_handler.php' );
			}
			if ( ! isset( $my_asset_handler ) ) {
				$byob_ah = new byob_asset_handler;
			}
		}
	}

	/**
	 *  Box API method for defining template options - note this is an options object
	 */
	protected function template_options() {
		return array(
			'title'  => __( 'Protected Content Markup', 'byobgpcm' ),
			'fields' => array(
				'use_protected_content_markup' => array(
					'type'    => 'checkbox',
					'options' => array(
						'yes' => __( 'Use protected content markup on this template', 'byobgpcm' )
					),
					'dependents' => array('yes')
				),
				'use_more_protection' => array(
					'type'    => 'checkbox',
					'options' => array(
						'yes' => __( 'Include "More Tag" protection', 'byobgpcm' )
					),
					'parent' => array(
						'use_protected_content_markup' => 'yes')
				),
				'selector1' => array(
					'type'    => 'text',
					'width' => 'medium',
					'code' => 'true',
					'placeholder' => '.post_box',
					'parent' => array(
						'use_protected_content_markup' => 'yes'),
					'tooltip' => __( 'place the full selector you wish to be marked as protected.  Include the class and or ID indicator', 'byobgpcm' )
				),
				'selector2' => array(
					'type'    => 'text',
					'width' => 'medium',
					'code' => 'true',
					'placeholder' => '.comment_list',
					'parent' => array(
						'use_protected_content_markup' => 'yes'),
					'tooltip' => __( 'place the full selector you wish to be marked as protected.  Include the class and or ID indicator', 'byobgpcm' )
				),
				'selector3' => array(
					'type'    => 'text',
					'width' => 'medium',
					'code' => 'true',
					'placeholder' => '#commentform',
					'parent' => array(
						'use_protected_content_markup' => 'yes'),
					'tooltip' => __( 'place the full selector you wish to be marked as protected.  Include the class and or ID indicator', 'byobgpcm' )
				),
				'selector4' => array(
					'type'    => 'text',
					'width' => 'medium',
					'code' => 'true',
					'placeholder' => '.post_box',
					'parent' => array(
						'use_protected_content_markup' => 'yes'),
					'tooltip' => __( 'place the full selector you wish to be marked as protected.  Include the class and or ID indicator', 'byobgpcm' )
				)
			)
		);
	}
	/**
	 * @param array $args - a variable containing $depth and other potential data
	 *
	 * Box API Method of outputting HTML at the location of the box in the template
	 * Typically echo'd rather than returned
	 */
	public function html( $args = array() ) {
		global $thesis;
		extract( $args = is_array( $args ) ? $args : array() );
		$depth   = isset( $depth ) ? $depth : 0;
		$tab     = str_repeat( "\t", $depth );
		$html    = ! empty( $this->options['html'] ) ? trim( esc_attr( $this->options['html'] ) ) : 'div';
		$class   = ! empty( $this->options['class'] ) ? ' class="' . trim( esc_attr( $this->options['class'] ) ) . '"' : '';
		$id      = ( ! empty( $this->options['id'] ) ? ' id="' . trim( esc_attr( $this->options['id'] ) ) . '"' : '' );
		$message = ! empty( $this->options['message'] ) ? trim( wp_kses_post( $this->options['message'] ) ) : false;

		echo "$tab<$html$id$class>\n";
			if ( $message ) {
				echo $message;
			}
			echo $this->rotator(array_merge($args, array('depth' => $depth + 1)));
		echo "$tab</$html>\n";
	}

}
