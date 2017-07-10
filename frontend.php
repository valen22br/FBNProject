<?php

class Constructent_Shortcodes_Frontend
{
	private $images;
	private $tabs;
	private $toggle_type;

	/**
	 * Constructor
	 *
	 * @return Constructent_Shortcodes_Frontend
	 */
	function __construct()
	{
		// Remove some shortcodes which have to be modified
		$remove = array('button', 'accordions', 'accordion', 'toggles', 'toggle', 'tabs', 'tab', 'progress_bar', 'promo_box');

		foreach ( $remove as $shortcode )
		{
			remove_shortcode( $shortcode, array('FITSC_Frontend', $shortcode) );
		}

		// Register shortcode
		$shortcodes = array(
			'row',
			'column',
			'button',
			'toggles',
			'toggle',
			'accordions',
			'accordion',
			'tabs',
			'tab',
			'progress_bar',
			'icon',
			'icon_box',
			'testimonials',
			'portfolio',
			'projects',
			'posts',
			'images_carousel',
			'image',
			'team',
			'counter',
			'pie_chart',
			'promo_box',
			'heading',
			'bubble',
			'space',
			'dropcap',
		);

		foreach ( $shortcodes as $shortcode )
		{
			add_shortcode( $shortcode, array($this, $shortcode) );
		}

		add_filter( 'fitsc_content', array( $this, 'unwrap_single_image' ), 50 );

		add_action( 'init', array( $this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 5 );
	}

	/**
	 * Remove p tag around single image
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	function unwrap_single_image( $content )
	{
		return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
	}

	function register_scripts()
	{
		wp_register_script( 'waypoints', CONSTRUCTENT_SHORTCODES_URL . '/js/waypoints.min.js', array(), '2.0.5', true );
		wp_register_script( 'counterup', CONSTRUCTENT_SHORTCODES_URL . '/js/jquery.counterup.min.js', array( 'waypoints' ), '1.0', true );
		wp_register_script( 'owlcarousel', CONSTRUCTENT_SHORTCODES_URL . '/js/owl.carousel.min.js', array( 'jquery' ), '1.3.3', true );
		wp_register_script( 'jquery-circliful', CONSTRUCTENT_SHORTCODES_URL . '/js/jquery.circliful.min.js', array( 'jquery' ), '2.1.6', true );
		wp_register_script( 'imagesloaded', CONSTRUCTENT_SHORTCODES_URL . '/js/imagesloaded.pkgd.min.js', null, '3.1.8', true );
		wp_register_script( 'jquery-shuffle', CONSTRUCTENT_SHORTCODES_URL . '/js/jquery.shuffle.min.js', array( 'jquery', 'imagesloaded' ), '3.1.1', true );
	}

	function scripts()
	{
		wp_enqueue_style( 'constructent-shortcodes', CONSTRUCTENT_SHORTCODES_URL . '/css/frontend.css' );
		wp_enqueue_script( 'constructent-shortcodes', CONSTRUCTENT_SHORTCODES_URL . '/js/frontend.js', array( 'jquery', 'counterup', 'owlcarousel', 'jquery-circliful', 'jquery-shuffle' ), '1.0.0', true );
	}

	/**
	 * Show button shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function button( $atts, $content )
	{
		extract( shortcode_atts( array(
			'type'          => '', // Default | Rounded | Ghost
			'link'          => '#',
			'color'         => '',
			'size'          => '',
			'icon'          => '',
			'icon_position' => '',

			'id'            => '',
			'nofollow'      => '',
			'background'    => '',
			'text_color'    => '',
			'target'        => '',
			'align'         => '',
			'full'          => '',
			'class'         => '',
		), $atts ) );

		$classes = array('fitsc-button');
		if ( $type )
			$classes[] = $type;
		if ( $full )
			$classes[] = 'fitsc-full';
		if ( $class )
			$classes[] = $class;
		if ( 'right' == $icon_position )
			$classes[] = 'fitsc-icon-right';
		if ( $color )
			$classes[] = "fitsc-background-$color";
		if ( $align )
			$classes[] = "fitsc-align-$align";
		if ( $size )
			$classes[] = "fitsc-$size";
		$classes = implode( ' ', $classes );
		$style = '';
		if ( $background )
			$style .= "background:$background;";
		if ( $text_color )
			$style .= "color:$text_color;";

		if ( $icon )
		{
			$icon = '<i class="' . esc_attr( $icon ) . '"></i>';
			$content = $icon_position == 'right' ? ($content . $icon) : ($icon . $content);
		}

		$html = sprintf(
			'<a href="%s" class="%s"%s%s%s%s>%s</a>',
			esc_url( $link ),
			esc_attr( $classes ),
			$id ? ' id="' . esc_attr( $id ) . '"' : '',
			$nofollow ? ' rel="nofollow"' : '',
			$target ? ' target="' . esc_attr( $target ) . '"' : '',
			$style ? ' style="' . esc_attr( $style ) . '"' : '',
			$content
		);

		if ( $align == 'center' )
			$html = '<div style="text-align:center">' . $html . '</div>';

		return apply_filters( 'fitsc_shortcode_button', $html, $atts, $content );
	}

	/**
	 * Show row shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function row( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'background_image' => '',
			'background_color' => '',
			'parallax'         => '',
			'fluid'            => '',
			'no_gutter'        => 0,
			'overlay'          => 0,
			'class'            => '',
			'style'            => '',
		), $atts ) );

		if ( $background_image )
		{
			$class .= ' row-background';
			$style .= "; background-image: url($background_image);";
		}

		if ( $background_color )
			$class .= ' row-background row-background-' . $background_color;

		if ( $fluid )
		{
			$class .= ' row-fluid';

			if ( 'row-content' == $fluid )
				$class .= ' row-fluid-content';
		}

		if ( $no_gutter )
			$class .= ' row-no-gutter';

		if ( $parallax )
			$class .= ' row-parallax';

		if ( $overlay )
			$class .= ' overlay-enabled ' . esc_attr( $overlay );

		if ( $style )
			$style = ' style="' . esc_attr( $style ) . '"';

		return sprintf(
			'<div class="fitsc-row %s"%s>%s<div class="row">%s</div></div>',
			esc_attr( $class ),
			$style,
			$overlay ? '<div class="overlay"></div>' : '',
			do_shortcode( $content )
		);
	}

	/**
	 * Show column shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function column( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'span'  => 12,
			'class' => '',
			'style' => '',
		), $atts ) );
		$classes = array( 'fitsc-column', "col-md-$span" );

		if ( ! empty( $class ) )
		{
			if ( ! preg_match( '|col-sm-\d|', $class ) )
				$classes[] = "col-sm-$span";

			if ( ! preg_match( '|col-xs-\d|', $class ) )
				$classes[] = 'col-xs-12';

			$classes[] = esc_attr( $class );
		}
		else
		{
			$classes[] = "col-sm-$span";
			$classes[] = 'col-xs-12';
		}

		if ( $style )
			$style = ' style="' . esc_attr( $style ) . '"';

		return sprintf( '<div class="%s"%s>%s</div>', implode( ' ', $classes ), $style, apply_filters( 'fitsc_content', $content ) );
	}

	/**
	 * Show toggles shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function toggles( $atts, $content )
	{
		extract( shortcode_atts( array(
			'type' => '',
		), $atts ) );

		// Get all toggle titles
		preg_match_all( '#\[toggle [^\]]*?title=[\'"]?(.*?)[\'"]#', $content, $matches );

		if ( empty($matches[1]) )
			return '';

		// Set global variable `toggle_type` as current attribute
		// This variable will be used in [toggle] shortcode
		$this->toggle_type = $type;

		$html = sprintf(
			'<div class="fitsc-toggles %s">%s</div>',
			$type ? 'fitsc-' . $type : '',
			do_shortcode( $content )
		);

		// Reset global variable `toggle_type`
		$this->toggle_type = '';

		return apply_filters( 'fitsc_shortcode_toggles', $html, $atts, $content );
	}

	/**
	 * Show toggle shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function toggle( $atts, $content )
	{
		extract( shortcode_atts( array(
			'title'  => '',
			'icon'   => '',
			'active' => '',
		), $atts ) );
		if ( ! $title || ! $content )
			return '';

		$html = sprintf( '
			<div class="fitsc-toggle%s">
				<div class="fitsc-title">%s%s</div>
				<div class="fitsc-content"%s>%s</div>
			</div>',
			$active ? ' fitsc-active' : '',
			$icon && $this->toggle_type != 'circle' ? '<i class="' . $icon . '"></i> ' : '',
			$title,
			$active ? ' style="display: block;"' : '',
			apply_filters( 'fitsc_content', $content )
		);

		return apply_filters( 'fitsc_shortcode_toggle', $html, $atts, $content );
	}

	/**
	 * Show accordions shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function accordions( $atts, $content )
	{
		extract( shortcode_atts( array(
			'type' => '',
		), $atts ) );

		// Get all toggle titles
		preg_match_all( '#\[accordion [^\]]*?title=[\'"]?(.*?)[\'"]#', $content, $matches );

		if ( empty($matches[1]) )
			return '';

		// Set global variable `toggle_type` as current attribute
		// This variable will be used in [toggle] shortcode
		$this->toggle_type = $type;

		$html = sprintf(
			'<div class="fitsc-accordions %s">%s</div>',
			$type ? 'fitsc-' . $type : '',
			do_shortcode( $content )
		);

		// Reset global variable `toggle_type`
		$this->toggle_type = '';

		return apply_filters( 'fitsc_shortcode_accordions', $html, $atts, $content );
	}

	/**
	 * Show accordion shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function accordion( $atts, $content )
	{
		extract( shortcode_atts( array(
			'title'  => '',
			'icon'   => '',
			'active' => '',
		), $atts ) );
		if ( ! $title || ! $content )
			return '';

		$html = sprintf( '
			<div class="fitsc-accordion%s">
				<div class="fitsc-title">%s%s</div>
				<div class="fitsc-content"%s>%s</div>
			</div>',
			$active ? ' fitsc-active' : '',
			$icon && $this->toggle_type != 'circle' ? '<i class="' . $icon . '"></i> ' : '',
			$title,
			$active ? ' style="display: block;"' : '',
			apply_filters( 'fitsc_content', $content )
		);

		return apply_filters( 'fitsc_shortcode_accordion', $html, $atts, $content );
	}

	/**
	 * Shortcode tabs
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function tabs( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'type'      => 'horizontal',
			'only_icon' => false,
		), $atts );

		$this->tabs = array();
		do_shortcode( $content );
		$tabs = array();
		$panels = array();

		foreach ( $this->tabs as $index => $tab )
		{
			$tabs[] = sprintf(
				'<li%s><a href="#">%s%s</a></li>',
				$index ? '' : ' class="fitsc-active"',
				$tab['icon'] ? '<i class="' . esc_attr( $tab['icon'] ) . '"></i>' : '',
				esc_html( $tab['title'] )
			);

			$panels[] = sprintf(
				'<div class="fitsc-tab%s">%s</div>',
				$index ? '' : ' fitsc-active',
				$tab['content']
			);
		}

		return sprintf(
			'<div class="fitsc-tabs%s%s">
				<ul class="fitsc-nav">%s</ul>
				<div class="fitsc-content">%s</div>
			</div>',
			$atts['type'] ? ' fitsc-' . esc_attr( $atts['type'] ) : '',
			$atts['only_icon'] ? ' fitsc-only-icon' : '',
			implode( "\n", $tabs ),
			implode( "\n", $panels )
		);
	}

	/**
	 * Shortcode tab
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @return string
	 */
	function tab( $atts, $content )
	{
		$this->tabs[] = shortcode_atts( array(
			'title'   => '',
			'icon'    => '',
			'content' => apply_filters( 'fitsc_content', $content ),
		), $atts );

		return '';
	}

	/**
	 * Show progress bar shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function progress_bar( $atts, $content )
	{
		extract( shortcode_atts( array(
			'text'    => '',
			'percent' => 100,
			'color'   => '',
			'size'    => 'small',
		), $atts ) );

		$html = sprintf( '
			<div class="fitsc-progress-bar fitsc-progress-%s">
				<div class="fitsc-title">%s <span>%s</span></div>
				<div class="fitsc-percent-wrapper"><div class="fitsc-percent fitsc-percent-%s %s" data-percentage="%s"></div></div>
			</div>',
			esc_attr( $size ),
			$text,
			$percent . '%',
			$percent,
			$color ? 'fitsc-background-' . esc_attr( $color ) : 'fitsc-background-default',
			$percent
		);

		return apply_filters( 'fitsc_shortcode_progress_bar', $html, $atts, $content );
	}

	/**
	 * Show icon shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function icon( $atts, $content )
	{
		extract( shortcode_atts( array(
			'color' => '',
			'size'  => '',
			'type'  => '',
			'class' => '',
		), $atts ) );

		if ( empty($class) )
			return '';

		if ( $color )
			$class .= ' fitsc-color-' . esc_attr( $color );

		if ( $type )
			$class .= ' fitsc-icon-type-' . esc_attr( $type );

		$size = absint( $size );
		$style = $size ? ' style="font-size: ' . $size . 'px"' : '';

		return sprintf( '<i class="fitsc-icon %s"%s></i>', $class, $style );
	}

	/**
	 * Show icon_box shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function icon_box( $atts, $content )
	{
		extract( shortcode_atts( array(
			'title'         => '',
			'subtitle'      => '',
			'icon'          => '',
			'icon_position' => 'top',
			'url'           => '',
			'more_text'     => __( 'Read more', 'constructent' ),
			'class'         => '',
		), $atts ) );

		return sprintf(
			'<div class="fitsc-icon-box icon-box service-box icon-%s %s %s clearfix">
				<i class="%s"></i>
				<h5>%s</h5>
				%s
				<p class="content">%s</p>
				%s
			</div>',
			esc_attr( $icon_position ),
			esc_attr( $class ),
			$subtitle && $content ? 'icon-box-full' : '',
			$icon,
			wp_kses( $title, wp_kses_allowed_html( 'post' ) ),
			$subtitle ? '<p class="subtitle">' . wp_kses( $subtitle, wp_kses_allowed_html( 'post' ) ) . '</p>' : '',
			do_shortcode( $content ),
			$url && $more_text ? sprintf( '<a href="%s" class="read-more">%s</a>', esc_url( $url ), esc_html( $more_text ) ) : ''
		);
	}

	/**
	 * Show testimonial shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function testimonials( $atts, $content )
	{
		extract( shortcode_atts( array(
			'number' => 3,
		), $atts ) );

		$args = array(
			'post_type'      => 'testimonials',
			'posts_per_page' => $number,
		);
		$testimonials = new WP_Query( $args );
		if ( ! $testimonials->have_posts() )
			return '';

		$carousel = array();

		while ( $testimonials->have_posts() ) : $testimonials->the_post();
			$url = constructent_meta( 'website_url' );

			$carousel[] = sprintf(
				'<div class="item %s">
					<div class="testinonial-avatar">
						<div class="wrap-avatar"><img src="%s" alt="%s"></div>
					</div>
					<div class="testimonial-des">
						<span class="name-author"><i class="fa fa-quote-left"></i> %s</span>
						<span class="regency-author">( %s )</span>
					</div>
					<div class="testimonial-text">%s</div>
				</div>',
				0 == $testimonials->current_post ? 'active' : '',
				wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ),
				the_title_attribute( 'echo=0' ),
				$url ? '<a href="' . esc_url( $url ) . '" target="_blank">' . get_the_title() . '</a>' : get_the_title(),
				constructent_meta( 'regency' ),
				get_the_content()
			);

		endwhile;
		wp_reset_postdata();

		$classes = 'fitsc-testimonials';

		$html = '<div class="' . $classes . '">';
		$html .= implode( "\n", $carousel );
		$html .= '</div>';

		return $html;
	}

	/**
	 * Show portfolio shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function portfolio( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'number'     => 5,
			'type'       => 'all',
			'category'   => '',
			'filter'     => false,
			'layout'     => 'standard',
			'columns'    => 5,
			'image_size' => 'square',
			'orderby'    => 'date',
			'order'      => 'desc',
		), $atts );

		$args = array(
			'post_type'           => 'portfolio',
			'posts_per_page'      => intval( $atts['number'] ),
			'ignore_sticky_posts' => 1,
			'order'               => $atts['order'],
			'orderby'             => $atts['orderby'],
		);

		if ( $atts['category'] )
		{
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'portfolio_category',
					'field'    => 'slug',
					'terms'    => $atts['category'],
				)
			);
		}

		if ( 'completion' == $atts['orderby'] )
		{
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'completion';
		}

		if ( 'completed' == $atts['type'] )
		{
			$args['meta_query'] = array(
				array(
					'key'     => 'completion',
					'value'   => 100,
					'compare' => '=',
				),
			);
		}
		elseif ( 'under_construction' == $atts['type'] )
		{
			$args['meta_query'] = array(
				array(
					'key'     => 'completion',
					'value'   => 100,
					'compare' => '>',
				),
			);
		}

		$projects = new WP_Query( $args );

		if ( ! $projects->have_posts() )
		{
			return '';
		}

		$size = 'square' == $atts['image_size'] ? 'post-thumbnail' : 'constructent-portfolio-thumbnail';
		$items = array();
		$filter = array( '<a href="#" data-group="all">' . __( 'All Projects', 'constructent' ) . '</a>' );
		while( $projects->have_posts() )
		{
			$projects->the_post();

			$categories = get_the_terms( get_the_ID(), 'portfolio_category' );
			$group = array();
			if ( $categories && ! is_wp_error( $categories ) )
			{
				foreach( $categories as $category )
				{
					$group[] = '"' . $category->slug . '"';
					$filter[] = sprintf(
						'<a href="#" data-group="%s">%s</a>',
						esc_attr( $category->slug ),
						esc_html( $category->name )
					);
				}
			}

			$items[] = sprintf(
				'<figure class="project" data-groups="[%s]">
					<div class="figure-image">%s</div>
					<figcaption>
						<h3>%s</h3>
						<a href="%s" class="view-project">%s</a>
					</figcaption>
				</figure>',
				esc_attr( implode( ',', $group ) ),
				constructent_get_image( 'size=' . $size . '&echo=0' ),
				get_the_title(),
				esc_url( get_the_permalink() ),
				__( 'View Project', 'constructent' )
			);
		}
		$filter = array_unique( $filter );

		return sprintf(
			'<div class="fitsc-portfolio portfolio-%s columns-%d %s">
				%s
				<div class="projects" data-gutter="%d">%s</div>
			</div>',
			esc_attr( $atts['layout'] ),
			absint( $atts['columns'] ),
			$atts['filter'] ? 'show-filter' : '',
			$atts['filter'] ? '<div class="portfolio-filter">' . implode( "\n", $filter ) . '</div>' : '',
			'no-gutter' == $atts['layout'] ? 0 : 10,
			implode( "\n", $items )
		);
	}

	/**
	 * Show projects shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function projects( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'number'  => 2,
			'columns' => 2,
			'type'    => 'all',
			'orderby' => 'date',
			'order'   => 'desc',
		), $atts, 'projects' );

		$args = array(
			'posts_per_page'      => absint( $atts['number'] ),
			'post_type'           => 'portfolio',
			'ignore_sticky_posts' => 1,
			'order'               => $atts['order'],
			'orderby'             => $atts['orderby'],
		);

		if ( 'completion' == $atts['orderby'] )
		{
			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'completion';
		}

		if ( 'completed' == $atts['type'] )
		{
			$args['meta_query'] = array(
				array(
					'key'     => 'completion',
					'value'   => 100,
					'compare' => '=',
				),
			);
		}
		elseif ( 'under_construction' == $atts['type'] )
		{
			$args['meta_query'] = array(
				array(
					'key'     => 'completion',
					'value'   => 100,
					'compare' => '>',
				),
			);
		}

		$projects = new WP_Query( $args );
		if ( ! $projects->have_posts() )
			return '';

		$col = 12 / absint( $atts['columns'] );
		$class = "project col-md-$col col-sm-$col col-xs-12";
		$html = array();

		while ( $projects->have_posts() )
		{
			$projects->the_post();

			$thumbnail = '';
			$percent = constructent_meta( 'completion' );
			$images = constructent_meta( 'images', "type=image_advanced&size=constructent-project-thumbnail" );

			if ( empty($images) && has_post_thumbnail() )
			{
				$thumbnail = wp_get_attachment_image( get_post_thumbnail_id(), 'constructent-project-thumbnail' );
			}
			elseif ( ! empty($images) )
			{
				$carousel = array(
					'id'         => 'project-gallery-' . get_the_ID(),
					'indicators' => array(),
					'items'      => array(),
				);

				$current = 0;
				foreach ( $images as $image )
				{
					$carousel['indicators'][] = sprintf(
						'<li data-target="#%s" data-slide-to="%s" class="%s"></li>',
						$carousel['id'],
						$current,
						0 == $current ? 'active' : ''
					);

					$carousel['items'][] = sprintf(
						'<div class="item %s"><img src="%s" alt="gallery"></div>',
						0 == $current ? 'active' : '',
						esc_url( $image['url'] )
					);

					$current++;
				}

				$thumbnail = sprintf(
					'<div id="%s" class="carousel slide" data-ride="carousel" data-interval="false">
						<ol class="carousel-indicators">%s</ol>
						<div class="carousel-inner">%s</div>
					</div>',
					$carousel['id'],
					implode( "\n", $carousel['indicators'] ),
					implode( "\n", $carousel['items'] )
				);
			}

			$html[] = sprintf(
				'<div class="%s">
					<div class="project">
						<div class="project-info">
							<h3 class="project-name"><a href="%s" rel="bookmark">%s</a></h3>
							<div class="project-desc">%s</div>
							%s
						</div>
						<div class="project-thumbnails">
							%s
						</div>
					</div>
				</div>',
				$class,
				esc_url( get_permalink() ),
				get_the_title(),
				constructent_content_limit( 10, __( 'Read More', 'constructent' ), false ),
				do_shortcode( '[progress_bar percent="' . $percent . '" text="' . __( 'completion', 'constructent' ) . '"]' ),
				$thumbnail
			);
		}
		wp_reset_postdata();

		return sprintf(
			'<div class="fitsc-projects fitsc-projects-%s row">%s</div>',
			esc_attr( $atts['type'] ),
			implode( "\n", $html )
		);
	}

	/**
	 * Show posts shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function posts( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'content_limit' => 25,
			'more'          => '',
			'number'        => 3,
			'columns'       => 3,
			'category'      => '',
			'align'         => 'left',
		), $atts, 'posts' );

		$args = array(
			'posts_per_page'      => intval( $atts['number'] ),
			'post_type'           => 'post',
			'ignore_sticky_posts' => 1,
		);
		if ( $atts['category'] )
			$args['category_name'] = $atts['category'];

		$query = new WP_Query( $args );
		if ( ! $query->have_posts() )
			return '';

		$col = 12 / absint( $atts['columns'] );
		$col = "col-md-$col col-sm-$col col-xs-12";
		$posts = array();

		while ( $query->have_posts() )
		{
			$query->the_post();

			if ( 0 == ( $query->current_post%$atts['columns'] ) )
				$class = $col . ' first';
			else
				$class = $col;

			$thumbnail = constructent_get_image( 'size=constructent-post-thumbnail&echo=0' );

			$posts[] = sprintf(
				'<div class="%s">
					%s
					<h3 class="entry-title"><a href="%s">%s</a></h3>
					<div class="entry-summary">%s%s</div>
				</div>',
				implode( ' ', get_post_class( $class ) ),
				$thumbnail ? '<a href="' . get_permalink() . '" class="entry-thumbnail">' . $thumbnail . '<span></span></a>' : '',
				esc_url( get_permalink() ),
				get_the_title(),
				constructent_content_limit( intval( $atts['content_limit'] ), '', false ),
				$atts['more'] ? '<a href="' . get_permalink() . '" class="read-more">' . esc_html( $atts['more'] ) . '</a>' : ''
			);
		}
		wp_reset_postdata();

		return '<div class="fitsc-posts row clearfix align-' . esc_attr( $atts['align'] ) . '">' . implode( "\n", $posts ) . '</div>';
	}

	/**
	 * Show team shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function team( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'number'  => 10,
			'columns' => 4,
			'orderby' => 'date',
			'order'   => 'DESC',
		), $atts, 'team' );

		$args = array(
			'post_type'      => 'team_member',
			'posts_per_page' => absint( $atts['number'] ),
			'orderby'        => $atts['orderby'],
			'order'          => $atts['order'],
		);
		$members = new WP_Query( $args );

		if ( ! $members->have_posts() )
		{
			return '';
		}
		$socials = array('facebook', 'twitter', 'google', 'linkedin', 'tumblr');
		$list = array();
		while ( $members->have_posts() )
		{
			$members->the_post();

			$links = array();
			foreach ( $socials as $social )
			{
				$url = constructent_meta( $social );
				if ( $url )
					$links[] = sprintf(
						'<li><a href="%s" rel="nofollow" target="_blank"><i class="fa fa-%s"></i></a></li>',
						esc_url( $url ),
						$social == 'google' ? 'google-plus' : $social
					);
			}

			$list[] = sprintf(
				'<div class="team-member clearfix">
					%s
					<div class="member-bio">
						%s
					</div>
					<div class="member-info">
						<h5 class="name">%s</h5>
						<span class="position">%s</span>
					</div>
					%s
				</div>',
				constructent_get_image( 'size=constructent-member-thumbnail&echo=0' ),
				constructent_content_limit( 12, false, false ),
				get_the_title(),
				constructent_meta( 'job' ),
				$links ? '<ul class="social-icons">' . implode( "\n", $links ) . '</ul>' : ''
			);
		}
		wp_reset_postdata();

		return sprintf(
			'<div class="fitsc-team"><div class="team-members" data-items="%d">%s</div></div>',
			absint( $atts['columns'] ),
			implode( "\n", $list )
		);
	}

	/**
	 * Show clients shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function images_carousel( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'visible' => 6,
			'type'    => 'box',
		), $atts, 'images_carousel' );

		$this->images = array();
		do_shortcode( $content );

		if ( count( $this->images ) < absint( $atts['visible'] ) )
			$atts['visible'] = count( $this->images );

		return sprintf( '<div class="fitsc-images-carousel images-carousel images-%s clearfix" data-items="%d">%s</div>', esc_attr( $atts['type'] ), absint( $atts['visible'] ), implode( '', $this->images ) );
	}

	/**
	 * Show client shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function image( $atts, $content )
	{
		extract( shortcode_atts( array(
			'name'  => '',
			'image' => '',
			'url'   => '',
		), $atts ) );

		$this->images[] = sprintf(
			'<span class="fitsc-image">%s<img src="%s" alt="%s">%s</span>',
			$url ? '<a href="' . esc_url( $url ) . '" target="_blank">' : '',
			esc_url( $image ),
			esc_attr( $name ),
			$url ? '</a>' : ''
		);

		return '';
	}

	/**
	 * Show counter shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function counter( $atts, $content )
	{
		extract( shortcode_atts( array(
			'number' => '100',
			'icon'   => '',
		), $atts ) );

		return sprintf(
			'<div class="fitsc-counter">%s<span class="text">%s</span><span class="counter">%s</span></div>',
			$icon ? "<i class='$icon'></i>" : '',
			$content,
			$number
		);
	}

	/**
	 * Show pie chart shortcode
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content Shortcode content
	 *
	 * @return string
	 */
	function pie_chart( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'dimension' => '200',
			'type'      => 'full',
			'color'     => '',
			'width'     => '4',
			'percent'   => '100',
			'font_size' => '24',
		), $atts ) );

		$color = $color ? $color : constructent_option( 'color_scheme' );

		$colors = array(
			'rosy'        => '#f16c7c',
			'pink'        => '#ff0084',
			'pink-dark'   => '#e22092',
			'red'         => '#df4428',
			'magenta'     => '#a9014b',
			'orange'      => '#ff5c00',
			'orange-dark' => '#dd4b39',
			'yellow'      => '#eeb313',
			'green-light' => '#91bd09',
			'green-lime'  => '#32cd32',
			'green'       => '#238f23',
			'blue'        => '#00adee',
			'blue-dark'   => '#3b5998',
			'indigo'      => '#261e4c',
			'violet'      => '#9400d3',
			'cappuccino'  => '#af8e45',
			'brown'       => '#b77b48',
			'brown-dark'  => '#7a5230',
			'gray'        => '#dddddd',
			'gray-dark'   => '#666666',
			'black'       => '#333333',
			'white'       => '#ffffff',
		);

		$color = isset($colors[$color]) ? $colors[$color] : $color;

		return sprintf(
			'<div class="fitsc-piechart"><div class="piechart" data-type="%s" data-percent="%s" data-dimension="%s" data-bgcolor="rgba(255,255,255,.05)" data-width="%s" data-fgcolor="%s" data-text="%s" data-fontsize="%d"><div class="piechart-info">%s</div></div></div>',
			esc_attr( $type ),
			esc_attr( $percent ),
			esc_attr( $dimension ),
			esc_attr( $width ),
			esc_attr( $color ),
			esc_attr( $percent ) . '%',
			absint( $font_size ),
			esc_attr( $content )
		);
	}

	/**
	 * Show promo box shortcode
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function promo_box( $atts, $content )
	{
		extract( shortcode_atts( array(
			'type'             => '',
			'heading'          => '',
			'button1_text'     => '',
			'button1_link'     => '',
			'button1_color'    => '',
			'button1_target'   => '',
			'button1_nofollow' => '',
			'button2_text'     => '',
			'button2_link'     => '',
			'button2_color'    => '',
			'button2_target'   => '',
			'button2_nofollow' => '',
		), $atts ) );

		$button1 = '';
		if ( $button1_text && $button1_link )
			$button1 = "<a href='$button1_link'" .
				($button1_color ? " class='fitsc-button fitsc-background-$button1_color'" : 'class="fitsc-button"') .
				($button1_nofollow ? " rel='nofollow'" : '') .
				($button1_target ? " target='$button1_target'" : '') .
				">$button1_text</a>";

		$button2 = '';
		if ( $type == 'two-buttons' )
		{
			$button2 = "<a href='$button2_link'" .
				($button2_color ? " class='fitsc-button fitsc-background-$button2_color'" : 'class="fitsc-button"') .
				($button2_nofollow ? " rel='nofollow'" : '') .
				($button2_target ? " target='$button2_target'" : '') .
				">$button2_text</a>";
		}

		$content = sprintf( '
			<div class="fitsc-content">
				%s
				%s
			</div>',
			$heading ? '<h3>' . $heading . '</h3>' : '',
			$content ? '<p class="fitsc-text">' . do_shortcode( $content ) . '</p>' : ''
		);
		$buttons = sprintf( '
			<div class="fitsc-buttons">%s %s</div>',
			$button1,
			$button2
		);

		$html = sprintf( '
			<div class="fitsc-promo-box-wrap">
				<div class="fitsc-promo-box%s%s">%s</div>
			</div>',
			$type ? " fitsc-$type" : '',
			$button1 ? '' : ' no-button',
			$type ? $content . $buttons : $buttons . $content
		);

		return apply_filters( 'fitsc_shortcode_promo_box', $html, $atts, $content );
	}

	/**
	 * Show heading shortcode
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content Shortcode content
	 *
	 * @return string
	 */
	function heading( $atts, $content = null )
	{
		extract( shortcode_atts( array(
			'tag'         => 'h5',
			'type'        => 'default',
			'font_size'   => '',
			'font_weight' => 'medium',
			'align'       => '',
			'class'       => '',
			'style'       => '',
		), $atts ) );

		if ( $font_size )
			$style = "font-size: {$font_size}px; $style";

		return sprintf(
			'<%1$s class="fitsc-heading %2$s fitsc-font-%3$s %4$s %5$s clearfix"%6$s><span>%7$s</span></%1$s>',
			$tag,
			$type ? 'fitsc-heading-' . esc_attr( $type ) : '',
			esc_attr( $font_weight ),
			$align ? 'fitsc-align-' . esc_attr( $align ) : '',
			$class,
			$style ? ' style="' . esc_attr( $style ) . '"' : '',
			wp_kses( $content, wp_kses_allowed_html( 'post' ) )
		);
	}

	/**
	 * Bubble shortcode
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function bubble( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'number' => '',
			'icon'   => '',
		), $atts );

		return sprintf(
			'<div class="fitsc-bubble bubble-%s">
				<span class="bubble-icon">%s</span>
				<div class="bubble-text">%s</div>
			</div>',
			$atts['icon'] ? 'icon' : 'number',
			$atts['icon'] ? '<i class="' . esc_attr( $atts['icon'] ) . '"></i>' : esc_html( $atts['number'] ),
			apply_filters( 'fitsc_content', $content )
		);
	}

	/**
	 * Empty space shortcode
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function space( $atts, $content )
	{
		$atts = shortcode_atts( array(
			'height' => 30,
		), $atts );

		$style = 30 != absint( $atts['height'] ) ? ' style="height: ' . absint( $atts['height'] ) . 'px"' : '';

		return sprintf( '<div class="fitsc-space"%s></div>', $style );
	}

	/**
	 * Dropcap shortcode
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	function dropcap( $atts, $content )
	{
		return sprintf( '<span class="fitsc-dropcap dropcap">%s</span>', $content );
	}
}

new Constructent_Shortcodes_Frontend;
