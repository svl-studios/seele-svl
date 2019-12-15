<?php
/**
 * Options config for our theme.
 *
 * @package     Seele
 * @subpackage  Redux Options
 * @author      Requite Designs
 * @copyright   Copyright (c) 2015, Requite Designs
 * @link        http://www.requitedesigns.com
 * @since       Seele 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Redux' ) ) {
	return;
}

$opt_name = 'svl_options';

$theme = wp_get_theme();

$args = array(
	'opt_name'            => $opt_name,
	'display_name'        => $theme->get( 'Name' ),
	'display_version'     => $theme->get( 'Version' ),
	'menu_type'           => 'menu',
	'allow_sub_menu'      => true,
	'menu_title'          => 'Summerville Votes',
	'page_title'          => 'Summerville Votes',
	'async_typography'    => false,
	'admin_bar'           => true,
	'admin_bar_icon'      => 'dashicons-admin-settings',
	'admin_bar_priority'  => 50,
	'global_variable'     => '',
	'dev_mode'            => false,
	'update_notice'       => false,
	'customizer'          => false,
	'page_priority'       => null,
	'page_parent'         => 'themes.php',
	'page_permissions'    => 'manage_options',
	'menu_icon'           => '',
	'last_tab'            => '',
	'page_icon'           => 'dashicons-admin-settings',
	'menu_icon'           => 'dashicons-admin-settings',
	'page_slug'           => 'summervile-votes',
	'save_defaults'       => true,
	'default_show'        => false,
	'default_mark'        => '',
	'page_priority'       => 61,
	'show_import_export'  => false,
	'transient_time'      => 60 * MINUTE_IN_SECONDS,
	'output'              => true,
	'output_tag'          => true,
	'database'            => '',
	'show_options_object' => false,
);

Redux::setArgs( $opt_name, $args );

Redux::setSection( $opt_name, array(
	'id'     => 'event-options',
	'icon'   => 'fa fa-glass',
	'title'  => esc_html__( 'Event', 'seele' ),
	'fields' => array(
		array(
			'id'       => 'svl_event',
			'type'     => 'text',
			'title'    => esc_html__( 'Event Name', 'seele' ),
			'subtitle' => esc_html__( 'Enter the name of the event.', 'seele' ),
		),
		array(
			'id'             => 'svl_event_typo',
			'type'           => 'typography',
			'title'          => esc_html__( 'Font', 'seele' ),
			'output'         => array( '.svl-event' ),
			'subsets'        => false,
			'text-transform' => true,
			'default'        => array(
				'font-family'    => 'Montserrat',
				'font-weight'    => 700,
				'font-size'      => '40px',
				'text-align'     => 'center',
				'subsets'        => 'Latin',
				'text-transform' => 'none',
				'color'          => '#666',
			),
		),
		array(
			'id'     => 'svl_event_bg',
			'type'   => 'background',
			'title'  => esc_html__( 'Background', 'seele' ),
			'output' => array( 'body' ),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'id'     => 'event-date-options',
	'icon'   => 'fa fa-calendar',
	'title'  => esc_html__( 'Event Date', 'seele' ),
	'fields' => array(
		array(
			'id'       => 'svl_event_date',
			'type'     => 'text',
			'title'    => esc_html__( 'Event Date', 'seele' ),
			'subtitle' => esc_html__( 'Enter the date of the event.', 'seele' ),
		),
		array(
			'id'             => 'svl_event_date_typo',
			'type'           => 'typography',
			'title'          => esc_html__( 'Font', 'seele' ),
			'output'         => array( '.svl-event-date' ),
			'subsets'        => false,
			'text-transform' => true,
			'default'        => array(
				'font-family'    => 'Montserrat',
				'font-weight'    => 700,
				'font-size'      => '40px',
				'text-align'     => 'center',
				'subsets'        => 'Latin',
				'text-transform' => 'none',
			),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'id'     => 'event-vote-options',
	'icon'   => 'fa fa-pencil-square-o',
	'title'  => esc_html__( 'Contest Name', 'seele' ),
	'fields' => array(
		array(
			'id'       => 'svl_event_vote',
			'type'     => 'text',
			'title'    => esc_html__( 'Content Name', 'seele' ),
			'subtitle' => esc_html__( 'Enter the name of the event that will be voted upon.', 'seele' ),
		),
		array(
			'id'             => 'svl_event_vote_typo',
			'type'           => 'typography',
			'title'          => esc_html__( 'Font', 'seele' ),
			'output'         => array( '.svl-event-vote' ),
			'subsets'        => false,
			'text-transform' => true,
			'default'        => array(
				'font-family'    => 'Montserrat',
				'font-weight'    => 700,
				'font-size'      => '40px',
				'text-align'     => 'center',
				'subsets'        => 'Latin',
				'text-transform' => 'none',
			),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'id'     => 'event-vote-tag',
	'icon'   => 'fa fa-tag',
	'title'  => esc_html__( 'Voting Tag', 'seele' ),
	'fields' => array(
		array(
			'id'       => 'svl_vote_tag',
			'type'     => 'text',
			'title'    => esc_html__( 'Voting Tag', 'seele' ),
			'subtitle' => esc_html__( 'Enter the tag for the vote.', 'seele' ),
			'default'  => esc_html__( 'Tap one button to vote for your favorite, the click "Vote Now"!', 'seele' ),
		),
		array(
			'id'             => 'svl_vote_tag_typo',
			'type'           => 'typography',
			'title'          => esc_html__( 'Font', 'seele' ),
			'output'         => array( '.svl-vote-tag' ),
			'subsets'        => false,
			'text-transform' => true,
			'default'        => array(
				'font-family'    => 'Montserrat',
				'font-weight'    => 700,
				'font-size'      => '40px',
				'text-align'     => 'center',
				'subsets'        => 'Latin',
				'text-transform' => 'none',
			),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'id'    => 'merchant-options',
	'icon'  => 'fa fa-building',
	'title' => esc_html__( 'Merchants', 'seele' ),
) );

Redux::setSection( $opt_name, array(
	'id'         => 'merchant-options-names',
	'title'      => esc_html__( 'Participants', 'seele' ),
	'subsection' => true,
	'fields'     => array(
		array(
			'id'      => 'svl_merchants_per_row',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Number of merchants to deplay per row.', 'seele' ),
			'options' => array(
				'6' => 'Two',
				'4' => 'Three',
				'3' => 'Four',
			),
			'default' => '3',
		),
		array(
			'id'       => 'svl_merchants',
			'type'     => 'multi_text',
			'title'    => esc_html__( 'Participataing Merchants', 'seele' ),
			'subtitle' => esc_html__( 'Enter the names of participating merchants. Use the | character to add an address.  Ex:  Name|Address', 'seele' ),
		),
	),
) );

Redux::setSection( $opt_name, array(
	'id'         => 'merchant-options-styles',
	'title'      => esc_html__( 'Styling', 'seele' ),
	'subsection' => true,
	'fields'     => array(
		array(
			'id'             => 'svl_merchants_typo',
			'type'           => 'typography',
			'title'          => esc_html__( 'Button Font', 'seele' ),
			'output'         => array( '.vote-container .svl-merchant' ),
			'subsets'        => false,
			'text-transform' => true,
			'default'        => array(
				'font-family'    => 'Montserrat',
				'font-weight'    => 700,
				'font-size'      => '20px',
				'line-height'    => '30px',
				'text-align'     => 'center',
				'subsets'        => 'Latin',
				'text-transform' => 'uppercase',
			),
		),
		array(
			'id'      => 'svl_button_color',
			'type'    => 'color',
			'title'   => esc_html__( 'Button Background Color', 'seele' ),
			'output'  => array( 'background-color' => '.svl-merchant' ),
			'default' => '#fff',
		),
		array(
			'id'             => 'svl_addresses_typo',
			'type'           => 'typography',
			'title'          => esc_html__( 'Address Font', 'seele' ),
			'output'         => array( '.svl-merchant-address' ),
			'subsets'        => false,
			'text-transform' => true,
			'default'        => array(
				'font-family'    => 'Montserrat',
				'font-weight'    => 700,
				'font-size'      => '40px',
				'text-align'     => 'center',
				'subsets'        => 'Latin',
				'text-transform' => 'none',
			),
		),
		array(
			'id'      => 'svl_address_color',
			'type'    => 'color',
			'title'   => esc_html__( 'Address Background Color', 'seele' ),
			'output'  => array( 'background-color' => '.svl-merchant-address' ),
			'default' => '#fff',
		),

		array(
			'id'      => 'svl_sekected_color',
			'type'    => 'color',
			'title'   => esc_html__( 'Selected Background Color', 'seele' ),
			'output'  => array( 'background-color' => '.svl-merchant.selected,.svl-merchant-address.selected' ),
			'default' => '#fff',
		),

		array(
			'id'      => 'svl_selected_font_color',
			'type'    => 'color',
			'title'   => esc_html__( 'Selected Font Color', 'seele' ),
			'output'  => array( 'color' => '.svl-merchant.selected,.svl-merchant-address.selected' ),
			'default' => '#fff',
		),
	),
) );
