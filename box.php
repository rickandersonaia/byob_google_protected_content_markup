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
		add_filter( 'the_content', array( $this, 'more_tag_protection' ), 1 );
		add_action( 'wp_footer', array( $this, 'JSON_LD_markup' ) );
	}

	/**
	 *  Box API method for defining template options - note this is an options object
	 */
	protected function template_options() {
		return array(
			'title'  => __( 'Protected Content Markup', 'byobgpcm' ),
			'fields' => array(
				'use_protected_content_markup' => array(
					'type'       => 'checkbox',
					'label'      => __( 'Does this template contain protected content?', 'byobgpcm' ),
					'options'    => array(
						'yes' => __( 'Use protected content markup on this template', 'byobgpcm' )
					),
					'dependents' => array( 'yes' )
				),
				'organization_name'                    => array(
					'type'        => 'text',
					'label'       => __( 'Organization name is required', 'byobgpcm' ),
					'width' => 'full',
					'placeholder' => 'ACME Company',
					'parent'      => array(
						'use_protected_content_markup' => 'yes'
					),
					'tooltip'     => __( 'Google requires that the name of the organization be published', 'byobgpcm' )
				),
				'logo_url'                    => array(
					'type'        => 'text',
					'label'       => __( 'Organization logo URL', 'byobgpcm' ),
					'width' => 'full',
					'placeholder' => 'http://acmeco.com/logo-img.jpg',
					'parent'      => array(
						'use_protected_content_markup' => 'yes'
					),
					'tooltip'     => __( 'Google requires a logo image for the organization - use an absolute url', 'byobgpcm' )
				),
				'use_more_protection'          => array(
					'type'    => 'checkbox',
					'label'   => __( 'Does this template protect content using the "More Tag"?', 'byobgpcm' ),
					'options' => array(
						'yes' => __( 'Include "More Tag" protection', 'byobgpcm' )
					),
					'parent'  => array(
						'use_protected_content_markup' => 'yes'
					)
				),
				'selector1'                    => array(
					'type'        => 'text',
					'label'       => __( 'Selector to protect', 'byobgpcm' ),
					'width'       => 'medium',
					'code'        => 'true',
					'placeholder' => '.post_box',
					'parent'      => array(
						'use_protected_content_markup' => 'yes'
					),
					'tooltip'     => __( 'place the class selector you wish to be marked as protected.  Include the class indicator', 'byobgpcm' )
				),
				'selector2'                    => array(
					'type'        => 'text',
					'label'       => __( 'Selector to protect', 'byobgpcm' ),
					'width'       => 'medium',
					'code'        => 'true',
					'placeholder' => '.comment_list',
					'parent'      => array(
						'use_protected_content_markup' => 'yes'
					),
					'tooltip'     => __( 'place the class selector you wish to be marked as protected.  Include the class indicator', 'byobgpcm' )
				),
				'selector3'                    => array(
					'type'        => 'text',
					'label'       => __( 'Selector to protect', 'byobgpcm' ),
					'width'       => 'medium',
					'code'        => 'true',
					'placeholder' => '.post_box',
					'parent'      => array(
						'use_protected_content_markup' => 'yes'
					),
					'tooltip'     => __( 'place the class selector you wish to be marked as protected.  Include the class indicator', 'byobgpcm' )
				),
				'selector4'                    => array(
					'type'        => 'text',
					'label'       => __( 'Selector to protect', 'byobgpcm' ),
					'width'       => 'medium',
					'code'        => 'true',
					'placeholder' => '.post_box',
					'parent'      => array(
						'use_protected_content_markup' => 'yes'
					),
					'tooltip'     => __( 'place the class selector you wish to be marked as protected.  Include the class indicator', 'byobgpcm' )
				)
			)
		);
	}

	public function more_tag_protection( $content ) {
		global $post;
		if ( isset( $this->template_options['use_protected_content_markup']['yes'] ) &&
		     isset( $this->template_options['use_more_protection']['yes'] ) ) {

			$unfiltered_content = $post->post_content;
			$content_fragments  = explode( '-->', $unfiltered_content, 2 );

			if ( $content_fragments ) {
				$first_half  = strval( $content_fragments[0] ) . '-->';
				$second_half = '<div class="paywall">' . $content_fragments[1] . '</div>';

				return $first_half . $second_half;

			} else {
				return $content;
			}

		} else {
			return $content;
		}
	}

	public function JSON_LD_markup() {
		global $post;
		$post_image_url = $this->select_post_image( $post->ID );
		$author_name    = get_the_author_meta( 'display_name', $post->post_author );
		$org_name = !empty($this->template_options['organization_name']) ? esc_html($this->template_options['organization_name']) : "";
		$logo_url = !empty($this->template_options['logo_url']) ? esc_url($this->template_options['logo_url']) : "";

		if ( isset( $this->template_options['use_protected_content_markup']['yes'] ) ) {
			?>
			<script type="application/ld+json">
				{
				  "@context": "http://schema.org",
				  "@type": "Article",
				  "mainEntityOfPage": {
				    "@type": "WebPage",
				    "@id": "<?php echo get_the_permalink( $post->ID ) ?>"
				  },
				  "headline": "<?php echo get_the_title( $post->ID ) ?>",
				  <?php if ( $post_image_url ) {
				echo '"image": "' . $post_image_url . '",' . "\n";
				?>
				  "datePublished": "<?php echo $post->post_date; ?>",
				  "dateModified": "<?php echo $post->post_modified; ?>",
				  "author": {
				    "@type": "Person",
				    "name": "<?php echo $author_name; ?>"
				  },
				  "publisher": {
				     "name": "<?php echo $org_name; ?>",
				     "@type": "Organization",
				     "logo": {
				        "@type": "ImageObject",
				        "url": "<?php echo $logo_url; ?>"
				     }
				  },
				  "description": "<?php echo get_the_excerpt( $post->ID ) ?>",
				  "isAccessibleForFree": "False",
				  "hasPart":<?php echo $this->build_the_protected_selector_array(); ?>
				}



			</script>
			<?php
		}
		}
	}

	public function build_the_protected_selector_array() {
		$selectors       = array( 'selector1', 'selector2', 'selector3', 'selector4' );
		$custom_selector = "";
		$t               = "\t\t\t\t\t";

		$given_selector = "$t{\n";
		$given_selector .= "$t\"@type\": \"WebPageElement\",\n";
		$given_selector .= "$t\"isAccessibleForFree\": \"False\",\n";
		$given_selector .= "$t\"cssSelector\" : \".paywall\"\n";
		$given_selector .= "$t}\n";

		foreach ( $selectors as $selector ) {
			if ( ! empty( $this->template_options[ $selector ] ) ) {
				$custom_selector .= "$t{\n";
				$custom_selector .= "$t\"@type\": \"WebPageElement\",\n";
				$custom_selector .= "$t\"isAccessibleForFree\": \"False\",\n";
				$custom_selector .= "$t\"cssSelector\" : \"" . esc_attr( $this->template_options[ $selector ] ) . "\"\n";
				$custom_selector .= "$t},\n";
			}
		}
		if ( ! empty( $custom_selector ) ) {
			$selector_array = "[\n" . $custom_selector . $given_selector . "$t]\n";
		} else {
			$selector_array = "\n" . $given_selector;
		}

		return $selector_array;
	}

	public function select_post_image( $post_id ) {
		$post_image = get_the_post_thumbnail_url( $post_id );
		if ( $post_image ) {
			return $post_image;
		}

		$post_images = get_attached_media( 'image', $post_id );
		$post_image  = $post_images[0];
		if ( ! empty( $post_image->ID ) ) {
			return wp_get_attachment_image_src( $post_image->ID, 'full' );
		}

		return false;
	}

}
