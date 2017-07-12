<?php

/*
  Name: BYOB Box Base
  Author: Rick Anderson - BYOBWebsite.com
  Version: 2.3
  Requires: 2.3
  Description: A starting point for creating a Thesis 2.3 box.
  Class: byob_box_base
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

// ANY place you see "my" or "byob" make sure you replace it with your own prefix


class byob_box_base extends thesis_box {

//	 search and replace
//	 BYOBBB
//	 byobbb

	public $type = 'box';

	public function translate() {
		$this->title = $this->name = __( 'BYOB Box Base', 'byobbb' );
	}

	/**
	 *  Box API method of providing a pseudo constructor method
	 */
	protected function construct() {
		global $byob_ah;;

		if ( ! defined( 'BYOBBB_PATH' ) ) {
			define( 'BYOBBB_PATH', dirname( __FILE__ ) );
		}
		if ( ! defined( 'BYOBBB_URL' ) ) {
			define( 'BYOBBB_URL', THESIS_USER_BOXES . '/' . basename( __DIR__ ) );
		}


		if ( is_admin() ) {
			if ( ! class_exists( 'byob_asset_handler' ) ) {
				include_once( BYOBBB_PATH . '/byob_asset_handler.php' );
			}
			if ( ! isset( $my_asset_handler ) ) {
				$byob_ah = new byob_asset_handler;
			}
		}
	}


	/**
	 * @return array of HTML Option settings
	 *
	 * Box API Method for formatting HTML Options
	 */
	protected function html_options() {
		global $thesis;
		$html = $thesis->api->html_options( array(
			'div'     => 'div',
			'section' => 'section',
			'article' => 'article'
		), 'div' );

		return $html;
	}

	/**
	 * @return array of Skin Content Options
	 *
	 * Box API Method for formatting Skin Content Options
	 */
	protected function options() {
		return array(
			'message' => array(
				'type'    => 'textarea',
				'label'   => __( 'Enter your message here', 'byobbb' ),
				'tooltip' => __( 'HTML is allowed.', 'byobbb' ),
				'code'    => true
			)
		);
	}

	/**
	 *  Box API method for defining template options - note this is an options object
	 */
	protected function template_options() {
		return array(
			'title'  => __( 'Footer Widgets', 'byobscfw' ),
			'fields' => array(
				'remove_footer_widgets' => array(
					'type'    => 'checkbox',
					'options' => array(
						'yes' => __( 'Remove footer widgets from this template', 'byobscfw' )
					)
				)
			)
		);
	}

	/**
	 *  Box API method for defining post meta
	 */
	protected function post_meta() {
		return array(
			'title'  => __( 'Footer Widgets', 'byobscfw' ),
			'fields' => array(
				'remove_footer_widgets' => array(
					'type'    => 'checkbox',
					'options' => array(
						'yes' => __( 'Remove footer widgets from this template', 'byobscfw' )
					)
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

		if ( $message ) {
			echo "$tab<$html$id$class>$message</$html>";
		}
	}

}
