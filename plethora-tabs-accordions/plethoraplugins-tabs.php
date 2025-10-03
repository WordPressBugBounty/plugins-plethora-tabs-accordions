<?php
/**
 * Plugin Name:       Plethora Tabs + Accordions
 * Description:       User-friendly tabs or accordion block for the default Wordpress editor. Quickly switch between horizontal/vertical or accordion layout, change the plugin theme, and edit tab labels and content and see the effects immediately in Live Preview. You can select one of the predefined themes Basic and Tabby, and a Minimal theme that makes it easy to add your own styles.  
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           2.1
 * Plugin URI: 		  https://plethoradesign.com
 * Author:            Plethora Plugins
 * Author URI:        https://plethoradesign.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       plethoraplugins-tabs
 *
 * @package           plethoraplugins
 * @since 1.0.0
 */
 
// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

define('plethoraplugins_pro__tabs', TRUE);

// plethoraplugins_pro__tabs_sections_labels: an array where the key is the section, and the value is the label.  If not specified, the key will be used as the label for the section.
define('plethoraplugins_pro__tabs_sections_labels', [
	'main'=>'Basic',
	'htabs'=>'Horizontal Tabs',
	'accordions'=>'Accordions',
	'accordionicon'=>'Expand/Collapse Icons'
]);
// plethoraplugins_pro__tabs_sections_layout: an array where the key is the section and the value is an array of child sections, so that sections can display nested in a more organized fashion
define('plethoraplugins_pro__tabs_sections_layout', [
	'main'=>[],
	'htabs'=>[],
	'accordions'=>array('accordionicon'=>[]),
]);

global $plethoraplugins_pro_tabs_normalized_options;
global $plethoraplugins_pro_tabs_defaults;
global $plethoraplugins_pro_tabs_option_definitions;

add_action( 'admin_notices', function() {
	
	// Show FAQ Schema feature notice
	$faq_schema_enabled = plethoraplugins_pro_tabs_get_defaults('enableFaqSchema');
	if(!$faq_schema_enabled && current_user_can('manage_options') && !get_option('plethoraplugins_pro_tabs_faq_notice_dismissed')) {
		echo '<div class="notice notice-info is-dismissible" data-notice="plethoraplugins_pro_tabs_faq_notice"><p>';
		echo '<strong>Tabs + Accordions (Pro):</strong> ';
		echo __( 'New FAQ Schema feature available! Enable it in the <a href="'. esc_url( plethoraplugins_pro_tabs_get_settings_url() ) .'">plugin settings</a> to automatically add structured data to your accordions for better SEO.' );
		echo '</p></div>';
	}
});

function plethoraplugins_pro_tabs_is_settings_page(){
	if(function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		return $screen && ($screen->id == 'settings_page_plethoraplugins-tabs-settings');
	}
	return false;
}
function plethoraplugins_pro_tabs_get_settings_url(){
	return get_admin_url(null, 'options-general.php?page=plethoraplugins-tabs-settings');
}

add_action( 'init', function () {
    //register_block_type( __DIR__ );
	$registry = WP_Block_Type_Registry::get_instance();
	if ( ! $registry->get_registered(__DIR__)&& !defined( 'plethoraplugins__tabs' )) {
		$tabsRegistered = register_block_type(
			__DIR__,
			array(
				'render_callback' => 'plethoraplugins_pro_tabs_render_callback',
			)
		);
		//if(!$tabsRegistered) die('tabs not registered');
	}
	if ( ! $registry->get_registered('plethoraplugins/tab')&& !defined( 'plethoraplugins__tabs' ) ) {
		$tabRegistered = register_block_type(
			'plethoraplugins/tab',
			array(
				'render_callback' => 'plethoraplugins_pro_tab_render_callback',
			)
		);
		//if(!$tabRegistered) die('tab not registered');
	}
} );
function plethoraplugins_pro_tabs_activate(){
	register_uninstall_hook( __FILE__, 'plethoraplugins_pro_tabs_uninstall' );
}
register_activation_hook( __FILE__, 'plethoraplugins_pro_tabs_activate' );

// And here goes the uninstallation function:
function plethoraplugins_pro_tabs_uninstall(){
	//	codes to perform during unistallation
	$deletesettingsonuninstall = plethoraplugins_pro_tabs_get_defaults('deletesettingsonuninstall');
	if('deletesettingsonuninstall') delete_option('plethoraplugins_tabs_options');
	
}
function plethoraplugins_pro_tabs_get_themes(){
    return array(
        ''=>'Basic (Default)', 
        'tabby'=>'Tabby', 
        'minimal'=>'Minimal',
    );
}
function plethoraplugins_pro_tabs_get_layouts(){
    return array( 
        ''=>'Horizontal (Default)', 
        'vertical'=>'Vertical', 
        'accordion'=>'Accordion',
    );
}
function plethoraplugins_pro_tabs_get_js_loading_behaviors(){
    return array( 
        ''=>'Conditional - loads if page has block (Default)', 
        'all'=>'All pages', 
        'none'=>'Do NOT load on any page (advanced users only)',
    );
}
function plethoraplugins_pro_tabs_get_htabresponsives(){
    return array(
        ''=>'Collapse to Accordion (Default)', 
        'wrap'=>'Wrap', 
        'none'=>'None',
    );
}
function plethoraplugins_pro_tabs_get_hresponsiveaccordionscollapsedinitiallys(){
    return array(
        ''=>'False (Default)', 
        'true'=>'True', 
    );
}
function plethoraplugins_pro_tabs_get_accordionheadinglevels(){
    return array(
        ''=>'H3 (Default)',
        'h1'=>'H1', 
        'h2'=>'H2',
        'h4'=>'H4',
        'h5'=>'H5',
        'h6'=>'H6',
    );
}

function plethoraplugins_pro_tabs_get_accordionautoclose(){
    return array(
        ''=>'True (Default)', 
        'false'=>'False',
    );
}
function plethoraplugins_pro_tabs_get_initialactivetab(){
    return array(
        ''=>'First tab (Default)', 
        '-1'=>'None', 
    );
}
function plethoraplugins_pro_tabs_get_accordionicontypes(){
    return array(
        ''=>'Rotating chevron (Default)', 
        'single_state'=>'Custom rotating-icon', 
        'two_state'=>'Two-state icon', 
        'none'=>'None', 
    );
}
function plethoraplugins_pro_tabs_get_option_definitions($forceFresh=false){
	global $plethoraplugins_pro_tabs_option_definitions;
	if($forceFresh || !(isset($plethoraplugins_pro_tabs_normalized_options) && is_array($plethoraplugins_pro_tabs_normalized_options))) {
		$plethoraplugins_pro_tabs_option_definitions = array(
			'theme'=>array('section'=>'main','label'=>'Theme','default'=>'basic', 'options'=>plethoraplugins_pro_tabs_get_themes()), 
			'layout'=>array('section'=>'main','label'=>'Layout','default'=>'horizontal', 'options'=>plethoraplugins_pro_tabs_get_layouts()),
			'mobilebreakpoint'=>array('section'=>'main','label'=>'Mobile Breakpoint','default'=>'', 'type'=>'nullableinteger', 'jsKey'=>'mobileBreakpoint', 'inputSuffix'=>'px'), 
			'deletesettingsonuninstall'=>array('section'=>'main','label'=>'Delete settings on uninstall?','default'=>FALSE, 'type'=>'boolean'),  
			'jsloadingbehavior'=>array('section'=>'main','label'=>'JS Loading Behavior','default'=>'', 'options'=>plethoraplugins_pro_tabs_get_js_loading_behaviors()),
			'enablefaqschema'=>array('section'=>'main','label'=>'Enable FAQ Schema by default','default'=>FALSE, 'type'=>'boolean', 'jsKey'=>'enableFaqSchema'),
			'htabresponsive'=>array('section'=>'htabs','label'=>'Responsive Behavior','default'=>'accordion', 'jsKey'=>'hTabResponsive', 'options'=>plethoraplugins_pro_tabs_get_htabresponsives()), 
			'hresponsiveaccordionscollapsedinitially'=>array('section'=>'htabs','label'=>'↳ Responsive Accordions: All Collapsed Initially','default'=>'false', 'jsKey'=>'hResponsiveAccordionsCollapsedInitially', 'options'=>plethoraplugins_pro_tabs_get_hresponsiveaccordionscollapsedinitiallys()), 
			'accordionheadinglevel'=>array('section'=>'accordions','label'=>'Heading Level','default'=>'h3', 'jsKey'=>'accordionHeadingLevel', 'options'=>plethoraplugins_pro_tabs_get_accordionheadinglevels()), 
			'accordionautoclose'=>array('section'=>'accordions','label'=>'Auto Close','default'=>'true', 'jsKey'=>'accordionAutoClose', 'options'=>plethoraplugins_pro_tabs_get_accordionautoclose()),
			'initialactivetab'=>array('section'=>'main','label'=>'Initially Active Tab Index','default'=>0, 'jsKey'=>'initialActiveTab', 'options'=>plethoraplugins_pro_tabs_get_initialactivetab()), 
			'accordioniconsize'=>array('section'=>'accordionicon','label'=>'Icon Size','default'=>'', 'jsKey'=>'accordionIconSize', 'placeholder'=>'.75rem'), 
			'accordionicontype'=>array('section'=>'accordionicon','label'=>'Icon Type','default'=>'', 'jsKey'=>'accordionIconType', 'options'=>plethoraplugins_pro_tabs_get_accordionicontypes()), 
			'accordionicontwostateclosed'=>array('section'=>'accordionicon','label'=>'Icon Two State Closed','default'=>'', 'jsKey'=>'accordionIconTwoStateClosed'), 
			'accordionicontwostateopen'=>array('section'=>'accordionicon','label'=>'Icon Two State Open','default'=>'', 'jsKey'=>'accordionIconTwoStateOpen'),  
			'accordioniconsinglestate'=>array('section'=>'accordionicon','label'=>'Custom rotating-icon','default'=>'', 'jsKey'=>'accordionIconSingleState'), 
		);
	}
	return $plethoraplugins_pro_tabs_option_definitions;
}

function plethoraplugins_pro_tabs_get_options($forceFresh=false){
	global $plethoraplugins_pro_tabs_normalized_options;
	if($forceFresh || !(isset($plethoraplugins_pro_tabs_normalized_options) && is_array($plethoraplugins_pro_tabs_normalized_options))) {
		$options = get_option('plethoraplugins_tabs_options');
		if(!is_array($options)) $options = [];
		$optionDefinitions = plethoraplugins_pro_tabs_get_option_definitions();
		foreach($optionDefinitions as $key=>$def){
			if(!(isset($options[$key]) && $options[$key])) $options[$key] = $def['default'];
			if(isset($def['options']) && !in_array($options[$key], array_keys($def['options']) ))  $options[$key] = $def['default'];
		}
		$plethoraplugins_pro_tabs_normalized_options = $options;
	}
	return $plethoraplugins_pro_tabs_normalized_options;
}
function plethoraplugins_pro_tabs_get_defaults($whichSetting=NULL, $forceFresh=FALSE){
	global $plethoraplugins_pro_tabs_defaults;
	
	if($forceFresh || !(isset($plethoraplugins_pro_tabs_defaults) && is_array($plethoraplugins_pro_tabs_defaults))) {
		$options = plethoraplugins_pro_tabs_get_options();
		$optionDefinitions = plethoraplugins_pro_tabs_get_option_definitions();
		$settings = array();
		foreach($optionDefinitions as $key=>$def){
			$jsKey = isset($def['jsKey']) ? $def['jsKey'] : $key;
			$settings[$jsKey] = $options[$key];
			if(isset($def['type'])){
				switch($def['type']){
					case 'nullableinteger':
						$settings[$jsKey] = $settings[$jsKey] ? intval($settings[$jsKey]) : NULL;
						break;
					case 'integer':
						$settings[$jsKey] = intval($settings[$jsKey]);
						break;
					case 'nullableboolean':
						$settings[$jsKey] = $settings[$jsKey] ? ($settings[$jsKey] === 'true') : NULL;
						break;
					case 'boolean':
						$settings[$jsKey] = ($settings[$jsKey] === 'true' || $settings[$jsKey] === '1' || $settings[$jsKey] === 1 || $settings[$jsKey] === TRUE);
						break;
				}
			}
		}
		$plethoraplugins_pro_tabs_defaults = $settings;
	}
	else $settings = $plethoraplugins_pro_tabs_defaults;
    if($whichSetting) return isset($settings[$whichSetting]) ? $settings[$whichSetting] : NULL;
    return $settings;
}
function plethoraplugins_pro_tabs_get_settings(){
    $defaults = plethoraplugins_pro_tabs_get_defaults();
    return array(
        'defaults'=>$defaults,
    );
}
function plethoraplugins_pro_tabs_options_validate( $input ) {
    return $input;
}
function plethoraplugins_pro_tabs_settings_text() {
    echo '';
}
function plethoraplugins_pro_tabs_sprint_input($key, $options=NULL, $optionDefinitions=NULL){
    if(!$options) $options = plethoraplugins_pro_tabs_get_options();
    if(!$optionDefinitions) $optionDefinitions = plethoraplugins_pro_tabs_get_option_definitions();
    $def = $optionDefinitions[$key];
    $o = '';//$key . ': ';
	
    if(isset($def['hide']) && $def['hide']) return '';
    if(isset($def['readonly']) && $def['readonly']) return esc_html($options[$key]);
	$type = isset($def['type']) ? $def['type'] : 'text';
	if(isset($def['options'])) $type = 'select';
	
	$default = isset($def['default']) ? $def['default'] : '';
	$class = isset($def['class']) ? $def['class'] : '';
	
	if($type == 'boolean') {
		$isChecked = ($options[$key] === '1' || $options[$key] === 1 || $options[$key] === TRUE || $options[$key] === 'true');
		$o .= '<input id="plethoraplugins_pro_tabs_setting_' . esc_attr($key) . '" name="plethoraplugins_tabs_options[' . esc_attr($key) . ']" type="checkbox" value="1" data-pds-tabs--default="' . esc_attr($default) . '" ' . ($isChecked ? 'checked="checked"' : '') . ' class="' . esc_attr($class) . '"/>';
	}
    elseif($type == 'select') {
        $o .= '<select id="plethoraplugins_pro_tabs_setting_' . esc_attr($key) . '" name="plethoraplugins_tabs_options[' . esc_attr($key) . ']" data-pds-tabs--default="" class="' . esc_attr($class) . '">';
        foreach($def["options"] as $value=>$label){
            $o .= '<option value="' . esc_attr($value) . '" ' . (($options[$key] == $value) ? 'selected' : ''). '>' . wp_strip_all_tags($label) . "</option>";
        }
        $o .= '</select>';
    }
    else {
		$inputTypeAtt = "text";
		switch($type){
			case "nullableinteger":
			case "integer":
				$inputTypeAtt = "number";
				break;
		}
		$placeholder = isset($def['placeholder']) ? $def['placeholder'] : '';
        $o .= '<input id="plethoraplugins_pro_tabs_setting_' . esc_attr($key) . '" name="plethoraplugins_tabs_options[' . esc_attr($key) . ']" type="' . esc_attr($inputTypeAtt) . '" value="' . esc_attr( $options[$key] ) . '" placeholder="' . esc_attr($placeholder) . '"  data-pds-tabs--default="' . esc_attr($default) . '" class="' . esc_attr($class) . '"/>';
    }
	if(isset($def['inputSuffix'])) $o .= $def['inputSuffix'];
    return $o;
}
function plethoraplugins_pro_tabs_theme(){
    echo plethoraplugins_pro_tabs_sprint_input('theme');
}
function plethoraplugins_pro_tabs_layout(){
    echo plethoraplugins_pro_tabs_sprint_input('layout');
}
function plethoraplugins_pro_tabs_jsloadingbehavior(){
    echo plethoraplugins_pro_tabs_sprint_input('jsloadingbehavior');
}
function plethoraplugins_pro_tabs_deletesettingsonuninstall(){
    echo plethoraplugins_pro_tabs_sprint_input('deletesettingsonuninstall');
}
function plethoraplugins_pro_tabs_enablefaqschema(){
    echo plethoraplugins_pro_tabs_sprint_input('enablefaqschema');
}
function plethoraplugins_pro_tabs_htabresponsive(){
    echo plethoraplugins_pro_tabs_sprint_input('htabresponsive');
}
function plethoraplugins_pro_tabs_hresponsiveaccordionscollapsedinitially(){
    echo plethoraplugins_pro_tabs_sprint_input('hresponsiveaccordionscollapsedinitially');
}
function plethoraplugins_pro_tabs_mobilebreakpoint(){
    echo plethoraplugins_pro_tabs_sprint_input('mobilebreakpoint');
}
function plethoraplugins_pro_tabs_accordionheadinglevel(){
    echo plethoraplugins_pro_tabs_sprint_input('accordionheadinglevel');
}

function plethoraplugins_pro_tabs_accordionautoclose(){
    echo plethoraplugins_pro_tabs_sprint_input('accordionautoclose');
}
function plethoraplugins_pro_tabs_initialactivetab(){
    echo plethoraplugins_pro_tabs_sprint_input('initialactivetab');
}
function plethoraplugins_pro_tabs_accordionicontype(){
    echo plethoraplugins_pro_tabs_sprint_input('accordionicontype');
}
function plethoraplugins_pro_tabs_accordioniconsize(){
    echo plethoraplugins_pro_tabs_sprint_input('accordioniconsize');
}
function plethoraplugins_pro_tabs_accordionicontwostateclosed(){
    echo plethoraplugins_pro_tabs_sprint_input('accordionicontwostateclosed');
}
function plethoraplugins_pro_tabs_accordionicontwostateopen(){
    echo plethoraplugins_pro_tabs_sprint_input('accordionicontwostateopen');
}
function plethoraplugins_pro_tabs_accordioniconsinglestate(){
    echo plethoraplugins_pro_tabs_sprint_input('accordioniconsinglestate');
}

function plethoraplugins_pro_tabs_register_settings() {
    register_setting( 'plethoraplugins_tabs_options', 'plethoraplugins_tabs_options', 'plethoraplugins_pro_tabs_options_validate' );
    add_settings_section( 'default_settings', __('Site-Wide Default Settings'), 'plethoraplugins_pro_tabs_settings_text', 'plethoraplugins_pro_tabs' );
    $optionDefinitions = plethoraplugins_pro_tabs_get_option_definitions();
	$optionDefinitionsBySection = [];
    foreach($optionDefinitions as $key=>$def){
		$optionDefinitionsBySection[$def['section']][$key] = $def;
	}
	$sectionLabels = plethoraplugins_pro__tabs_sections_labels;
	foreach($optionDefinitionsBySection as $section=>$sectionDefs) {
		$sectionLabel = isset($sectionLabels[$section]) ? $sectionLabels[$section] : $section;
		add_settings_section( $section, __($sectionLabel), 'plethoraplugins_pro_tabs_settings_text', 'plethoraplugins_pro_tabs_' . $section );
		foreach($sectionDefs as $key=>$def){
			add_settings_field( 
				'plethoraplugins_pro_tabs_' . $key, $def['label'], 
				'plethoraplugins_pro_tabs_' . $key, 
				'plethoraplugins_pro_tabs_' . $section, 
				$def['section'], 
				array('label_for'=>'plethoraplugins_pro_tabs_setting_' . $key) );
		}
	}
}
add_action( 'admin_init', 'plethoraplugins_pro_tabs_register_settings' );


function plethoraplugins_pro_tabs_apply_defaults($block_attributes){
    $defaults = plethoraplugins_pro_tabs_get_defaults();
    foreach($defaults as $key=>$defaultValue){
        if(!isset($block_attributes[$key]) || !$block_attributes[$key]) $block_attributes[$key] = $defaultValue;
    }
    return $block_attributes;
}
function plethoraplugins_pro_tab_apply_defaults($block_attributes){
    $defaults = plethoraplugins_pro_tabs_get_defaults(); //TODO: create dedicated 'tab' version of this function, instead of "tabs"
    //$defaults['accordionAutoClose'] = TRUE; //for now...
    $defaults['initialActive'] = FALSE; //for now...
    $defaults['parentLayout'] = $defaults['layout']; //for now...
    $defaults['parentAccordionHeadingLevel'] = $defaults['accordionHeadingLevel']; //for now...
    foreach($defaults as $key=>$defaultValue){
        if(!isset($block_attributes[$key]) || !$block_attributes[$key]) $block_attributes[$key] = $defaultValue;
    }
    return $block_attributes;
}
function plethoraplugins_pro_tabs_text_to_class($txt){
    if(!$txt) return '';
    $txt = sanitize_title_with_dashes($txt);
    $txt = str_replace('-', '_', $txt);
    $txt = sanitize_html_class($txt);
    return $txt;
}
function plethoraplugins_pro_tabs_generate_anchor($txt){
    if(!$txt) return '';
    $txt = str_replace('<br>', '_', $txt);
    $txt = str_replace('<BR>', '_', $txt);
    $txt = str_replace('<BR/>', '_', $txt);
    return plethoraplugins_pro_tabs_text_to_class($txt);
}

function plethoraplugins_pro_tabs_generate_faq_schema($questions, $content) {
    if(empty($questions)) return '';
    
    // Extract answers from content - this is a simplified extraction
    $answers = plethoraplugins_pro_tabs_extract_answers_from_content($content, count($questions));
    
    $faq_items = array();
    foreach($questions as $index => $question) {
        if(!empty($question)) {
            $answer = isset($answers[$index]) ? $answers[$index] : '';
            // Add even if answer is empty, with a default message
            $faq_items[] = array(
                '@type' => 'Question',
                'name' => wp_strip_all_tags($question),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => !empty($answer) ? wp_strip_all_tags($answer) : 'Content for ' . wp_strip_all_tags($question)
                )
            );
        }
    }
    
    if(empty($faq_items)) return '';
    
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faq_items
    );
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function plethoraplugins_pro_tabs_extract_answers_from_content($content, $question_count) {
    $answers = array();
    
    // Try to extract content from tab panels (which contain the actual content)
    if(preg_match_all('/<div[^>]*class="[^"]*js-plethoraplugins-tab-panel[^"]*"[^>]*>(.*?)<\/div>/s', $content, $matches)) {
        foreach($matches[1] as $index => $match) {
            // Clean up the content and get text
            $clean_content = trim(strip_tags($match));
            if(!empty($clean_content)) {
                $answers[$index] = $clean_content;
            }
        }
    } else if(preg_match_all('/<div class="pds-accordion__content">(.*?)<\/div>/s', $content, $matches)) {
        // Fallback for accordion content
        foreach($matches[1] as $index => $match) {
            $clean_content = trim(strip_tags($match));
            if(!empty($clean_content)) {
                $answers[$index] = $clean_content;
            }
        }
    } else {
        // Last fallback: split by paragraphs
        $content_blocks = preg_split('/<\/?(p|div)[^>]*>/i', $content);
        $clean_blocks = array_filter(array_map('trim', array_map('strip_tags', $content_blocks)));
        $answers = array_slice(array_values($clean_blocks), 0, $question_count);
    }
    
    return $answers;
}

function plethoraplugins_pro_sprint_icon_atts( $block_attributes, $attPref='data-pds-tabs--' ) {
	$iconData = 'true';
	$iconType = '';
	$iconSize = '';
	$iconType = isset($block_attributes['accordionIconType']) ? $block_attributes['accordionIconType'] : '';
	$iconSize = isset($block_attributes['accordionIconSize']) ? $block_attributes['accordionIconSize'] : '';
	if($iconType === 'two_state') {
		$iconData = array('closed'=>$block_attributes['accordionIconTwoStateClosed'],'open'=>$block_attributes['accordionIconTwoStateOpen']);
		$iconData = json_encode($iconData);
	}
	elseif($iconType === 'single_state') {
		$iconData = $block_attributes['accordionIconSingleState'];
	}
	elseif($iconType === 'none') {
		$iconData = 'false';
	}
	return ' ' . $attPref . 'icon-type="' . esc_attr($iconType) . '" ' . $attPref . 'icon="' . esc_attr($iconData) . '"  ' . $attPref . 'icon-size="' . esc_attr($iconSize) . '" ';
}
function plethoraplugins_pro_tab_render_callback( $block_attributes, $content ) {
		$accordionHeadingLevelOverride = null;
        $accordionHeadingLevelOverride = (isset($block_attributes['accordionHeadingLevel']) && $block_attributes['accordionHeadingLevel']) ? $block_attributes['accordionHeadingLevel'] : '';
        $block_attributes = plethoraplugins_pro_tab_apply_defaults($block_attributes);
        //$layout = $block_attributes['layout'];
        //$theme = $block_attributes['theme'];
        $parentLayout = $block_attributes['parentLayout'];
        $blockClassNames = '';
		$initialActive = 'false';
		$initialActive = isset($block_attributes['initialActive']) ? ($block_attributes['initialActive'] === TRUE ? 'true' : 'false') : 'false';
        if($parentLayout == 'accordion') {
			$accordionAutoClose = 'true';
			$accordionHeadingLevel = 'h3';
            $accordionAutoClose = isset($block_attributes['accordionAutoClose']) ? $block_attributes['accordionAutoClose'] : 'true';
            $parentAccordionHeadingLevel = (isset($block_attributes['parentAccordionHeadingLevel']) && $block_attributes['parentAccordionHeadingLevel']) ? $block_attributes['parentAccordionHeadingLevel'] : 'h3';
            $accordionHeadingLevel = $accordionHeadingLevelOverride ? $accordionHeadingLevelOverride : $parentAccordionHeadingLevel;
			$accordionHeadingLevelInteger = intval(ltrim($accordionHeadingLevel, 'h'));
            $label = (isset($block_attributes['label']) && $block_attributes['label']) ? $block_attributes['label'] : __('Tab');
            $anchor = isset($block_attributes['anchor']) ? $block_attributes['anchor'] : null;
            $finalAnchor = $anchor ? $anchor : plethoraplugins_pro_tabs_generate_anchor($label);
            return '<div id="' . $finalAnchor . '" class="pds-accordion__item pds-js-accordion-item pds-no-js" data-pds-tabs--initially-open="' . $initialActive . '" data-pds-tabs--click-to-close="true" data-pds-tabs--auto-close="' . $accordionAutoClose . '" data-pds-tabs--scroll="false" data-pds-tabs--scroll-offset="0" ' . plethoraplugins_pro_sprint_icon_atts( $block_attributes ) . '>
                    <div id="at-' . $finalAnchor . '" class="pds-accordion__title pds-js-accordion-controller" ><span class="pds-accordion__heading" role="heading" aria-level="' . $accordionHeadingLevelInteger . '">' . $label . '</span><span class="pds-accordion__icon" role="presentation" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 7.4099998" width=".75rem" height=".75rem"><path d="M12 1.41 10.59 0 6 4.58 1.41 0 0 1.41l6 6z" fill="currentColor"/></svg></span></div>
                    <div id="ac-' . $finalAnchor . '" class="pds-accordion__content">
                        ' . $content . '
                    </div>
                </div>';
        }
        return '<div class="' . $blockClassNames . '"  data-pds-tabs--accordion-initially-open="' . $initialActive . '" >' . $content . '</div>';
}
function plethoraplugins_pro_tabs_render_callback( $block_attributes, $content ) {
    //this renders the plugin server side so that we can update the block HTML without having to constantly chase deprecated markup

    $block_attributes = plethoraplugins_pro_tabs_apply_defaults($block_attributes);
	
	$accordionHeadingLevel = 'h3';
	$accordionHeadingLevel = (isset($block_attributes['accordionHeadingLevel']) && $block_attributes['accordionHeadingLevel']) ? $block_attributes['accordionHeadingLevel'] : 'h3';
	
    $layout = isset($block_attributes['layout']) ? $block_attributes['layout'] : 'basic';
    $theme = isset($block_attributes['theme']) ? $block_attributes['theme'] : 'horizontal';
    $tabLabels = (isset($block_attributes['tabLabels']) && is_array($block_attributes['tabLabels'])) ? $block_attributes['tabLabels'] : [];
    $tabIds = (isset($block_attributes['tabIds']) && is_array($block_attributes['tabIds'])) ? $block_attributes['tabIds'] : [];
	$accordionAutoClose = 'true';
    $accordionAutoClose = isset($block_attributes['accordionAutoClose']) ? $block_attributes['accordionAutoClose'] : 'true';
	$initialActiveTab = 0;
    $initialActiveTab = isset($block_attributes['initialActiveTab']) ? $block_attributes['initialActiveTab'] : 0;
    $hTabResponsive = isset($block_attributes['hTabResponsive']) ? $block_attributes['hTabResponsive'] : '';
    $hResponsiveAccordionsCollapsedInitially = isset($block_attributes['hResponsiveAccordionsCollapsedInitially']) ? $block_attributes['hResponsiveAccordionsCollapsedInitially'] : '';
    $mobileBreakpoint = isset($block_attributes['mobileBreakpoint']) ? $block_attributes['mobileBreakpoint'] : '';
    $accordionHeadingLevel = isset($block_attributes['accordionHeadingLevel']) ? $block_attributes['accordionHeadingLevel'] : '';
    $vTabListWidth = isset($block_attributes['vTabListWidth']) ? $block_attributes['vTabListWidth'] : '25%';
    $vTabContentWidth = isset($block_attributes['vTabContentWidth']) ? $block_attributes['vTabContentWidth'] : '75%';
    $className = isset($block_attributes['className']) ? $block_attributes['className'] : '';
    $globalFaqSchema = plethoraplugins_pro_tabs_get_defaults('enableFaqSchema');
    
    // Check for FAQ schema via className
    $blockFaqSchema = false;
    if (isset($block_attributes['className']) && strpos($block_attributes['className'], 'has-faq-schema') !== false) {
        $blockFaqSchema = true;
    }
    
    // Use global setting if enabled, otherwise check per-block setting
    $enableFaqSchema = $globalFaqSchema ? $globalFaqSchema : $blockFaqSchema;
    
    
    foreach($tabLabels as $k=>$v){
        if(!$v) $tabLabels[$k] = __('Tab') . ' ' . ($k + 1);
    }
    foreach($tabIds as $k=>$v){
        if(!$v) $tabIds[$k] = plethoraplugins_pro_tabs_generate_anchor($tabLabels[$k]);
    }
    $tabListClass = '';
    $tabsContainerClass = $className;
    $contentClass = '';
    $contentStyle = '';
    $tabListStyle = '';
    $cssNS = 'plethoraplugins';
    $themeClass = '';
    if ($theme != 'minimal' && $theme != 'none') $themeClass .= $cssNS . '-theme__minimal ';
    $themeClass .= $cssNS . '-theme__' . $theme . ' ';
    
    // Add FAQ Schema if enabled
    $faqSchemaScript = '';
    if($enableFaqSchema && !empty($tabLabels)) {
        // Add schema for accordion layout or horizontal tabs with accordion responsive behavior
        if($layout == 'accordion' || ($layout == 'horizontal' && $hTabResponsive == 'accordion')) {
            $faqSchemaScript = plethoraplugins_pro_tabs_generate_faq_schema($tabLabels, $content);
        }
    }
    
    if($layout == 'accordion') {
        $tabsContainerClass .= ' ' . $cssNS . '-accordion ' . $themeClass;
        return '<div >
                    <div class="' . esc_attr($tabsContainerClass) . '"   >
                        <div>
                            ' . $content . '
                        </div>
                    </div>
                    ' . $faqSchemaScript . '
            </div>';
    };
    $tabLabelClassName = '';
    $tabLabelClassNameActive = '';
    $responsiveBehavior = '';
    $blockAtts = [];
    switch($layout){
        case 'horizontal':
            $tabsContainerClass .= ' '  . $cssNS . '-tabs-container ' . $cssNS . '-tabs-container--horizontal' . ' ' . $themeClass;
            $contentClass = $cssNS . '-tabs--content';
            $tabListClass = $cssNS . '-tabs';
            $tabLabelClassNameActive = 'active';
            $responsiveBehavior = $hTabResponsive;
            break;
        case 'vertical':
            $tabsContainerClass .= ' '  . $cssNS . '-tabs-container ' . $cssNS . '-tabs-container--vertical' . ' ' . $themeClass;
            $contentClass = $cssNS . '-tabs--content ' . $cssNS . '-sidenavjump-content';
            $tabListClass = $cssNS . '-sidenavjump';
            $tabListStyle = 'flex-basis: ' . esc_attr($vTabListWidth);
            $contentStyle = 'flex-basis: ' . esc_attr($vTabContentWidth);
            $tabLabelClassNameActive = 'active';
            break;
    }

    $o =  
    '<div >
        <div class="' . esc_attr($tabsContainerClass) . '" 
			data-pds-tabs--layout="' . esc_attr($layout) . '"  
			data-pds-tabs--theme="' . esc_attr($theme) . '"  
			data-pds-tabs--mobile-breakpoint-forced="' . esc_attr($mobileBreakpoint) . '"
			' . ($responsiveBehavior ? 
					' data-pds-tabs--responsive="' . esc_attr($responsiveBehavior) . '" 
					 data-pds-tabs--responsive-accordion-collapsed-initially="' . esc_attr($hResponsiveAccordionsCollapsedInitially) . '"'
				 : '') 
			 . plethoraplugins_pro_sprint_icon_atts( $block_attributes, 'data-pds-tabs--accordion-' )
			. ' data-pds-tabs--accordion-heading-level="' . esc_attr($accordionHeadingLevel) . '"' 
			. ' data-pds-tabs--accordion-auto-close="' . esc_attr($accordionAutoClose) . '"' 
			.  ' >
            <div class="' . $tabListClass . '" ' . ($tabListStyle ? 'style="' . $tabListStyle . '"' : '') . ' >
              <ul>';
    foreach($tabLabels as $index=>$label){
                  $o .= '<li>
                        <a 
                                href="#' . $tabIds[$index] . '"
                                class="' . $tabLabelClassName . ((intval($initialActiveTab) == $index) ? ' '  . $tabLabelClassNameActive : '') . '" 
                            >
                            <span>' . $label . '</span>
                        </a>
                    </li>';
    }
              $o .= '</ul>
            </div>
            <div class="' . $contentClass . '" ' . ($contentStyle ? 'style="' . $contentStyle . '"' : '') . '>
                ' . $content . '
            </div>
        </div>
        ' . $faqSchemaScript . '
    </div>';
    return $o;
}
  
add_action( 'template_redirect', function(){
	if(!is_admin()){
		$jsloadingbehavior = plethoraplugins_pro_tabs_get_defaults('jsloadingbehavior');
		$mustLoad = FALSE;
		switch($jsloadingbehavior){
			case 'all':
				$mustLoad = TRUE;
				break;
			case 'none':
				$mustLoad = FALSE;
				break;
			case '': //conditional, the default: only load on pages that have one of our block types
			default:
				$postid = get_queried_object_id();
				$mustLoad = has_block('plethoraplugins/tab',$postid) || has_block('plethoraplugins/tabs',$postid);
				break;
		}
		if($mustLoad){
			add_action( 'wp_enqueue_scripts', function () {
				wp_register_script('plethoraplugins_pro_tabs_js', plugins_url('js/tabs.jquery-plugin.js', __FILE__), array('jquery'),'1.1.3', TRUE);
				wp_enqueue_script('plethoraplugins_pro_tabs_js');
				wp_register_script('plethoraplugins_pro_accordion_js', plugins_url('js/accordion.jquery-plugin.js', __FILE__), array('jquery'),'1.1.3', TRUE);
				wp_enqueue_script('plethoraplugins_pro_accordion_js');
			} ); 
		}
	}
});


add_action( 'admin_enqueue_scripts', function(){
    $script = 'window.plethoraplugins_pro_tabs_settings = ' . json_encode(plethoraplugins_pro_tabs_get_settings()) . ';';
    wp_add_inline_script('plethoraplugins-tabs-editor-script', $script, 'before');
    
    // Enqueue FAQ schema block editor enhancement
    $current_screen = get_current_screen();
    if ($current_screen && $current_screen->is_block_editor) {
        wp_enqueue_script(
            'plethoraplugins-tabs-faq-schema',
            plugins_url('js/block-editor-faq-schema.js', __FILE__),
            array('wp-blocks', 'wp-dom-ready', 'wp-hooks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-compose', 'wp-data'),
            '1.1.3',
            true
        );
    }
    
	if(plethoraplugins_pro_tabs_is_settings_page()){
		wp_register_script( 'micromodal_js', 'https://unpkg.com/micromodal/dist/micromodal.min.js', null, null, true );
		wp_enqueue_script('micromodal_js');
		wp_register_style( 'plethoraplugins_pro_micromodal_css', plugins_url( '/css/micromodal.css', __FILE__ ), false, '1.1.3', 'all' );
		wp_enqueue_style( 'plethoraplugins_pro_micromodal_css' );

		wp_register_script('plethoraplugins_pro_plugin_settings_js', plugins_url('js/plugin-settings.js', __FILE__), array('jquery'),'1.1.3', true);
		wp_enqueue_script('plethoraplugins_pro_plugin_settings_js');
		
		wp_register_style( 'plethoraplugins_pro_plugin_settings_css', plugins_url( '/css/plugin-settings.css', __FILE__ ), false, '1.1.3', 'all' );
		wp_enqueue_style( 'plethoraplugins_pro_plugin_settings_css' );
	}
	
	// Add notice dismissal script
	wp_add_inline_script('jquery', '
		jQuery(document).ready(function($) {
			$(document).on("click", ".notice[data-notice] .notice-dismiss", function() {
				var notice = $(this).parent().data("notice");
				if(notice) {
					$.post(ajaxurl, {
						action: "dismiss_plethoraplugins_pro_tabs_notice",
						notice: notice,
						nonce: "' . wp_create_nonce('dismiss_notice') . '"
					});
				}
			});
		});
	');
} );

// Handle notice dismissal
add_action('wp_ajax_dismiss_plethoraplugins_pro_tabs_notice', function() {
	if(!wp_verify_nonce($_POST['nonce'], 'dismiss_notice')) {
		wp_die('Security check failed');
	}
	
	$notice = sanitize_text_field($_POST['notice']);
	if($notice == 'plethoraplugins_pro_tabs_faq_notice') {
		update_option('plethoraplugins_pro_tabs_faq_notice_dismissed', true);
	}
	
	wp_die();
});



function plethoraplugins_pro_tabs_render_settings_page_print_sections($sections){
	if(!is_array($sections)) return;
	 foreach($sections as $section=>$childSections) { ?>
		<section class="plethoraplugins_pro_settings-section plethoraplugins_pro_settings-section-<?php print esc_attr_e($section) ?>">
		<?php  do_settings_sections( 'plethoraplugins_pro_tabs_' . $section ); 
			
			if($childSections) { 
				//recursion to display child sections...
				plethoraplugins_pro_tabs_render_settings_page_print_sections($childSections);
			}
		?> </section><?php 
	} 
}
function plethoraplugins_pro_tabs_render_settings_page(){
    ?>
	<div>
    <h1>Plethora Tabs + Accordions</h1>
    <h2><?php print __('by') ?> <a href="https://plethoradesign.com" target="_blank">Plethora Design</a></h2>
	<p><a href="https://www.plethoradesign.com/tabs-accordions-documentation/" target="_blank"><?php print __('Documentation') ?></a></p>
    <form class="plethoraplugins_pro_settings-form" action="options.php" method="post">
        <?php settings_fields( 'plethoraplugins_tabs_options' ); ?>
		<div class="plethoraplugins_pro_settings-sections" >
			 <?php plethoraplugins_pro_tabs_render_settings_page_print_sections(plethoraplugins_pro__tabs_sections_layout); ?>
		</div>
		<div class="plethoraplugins_pro_settings-form-actions">
			<input name="reset" class="plethoraplugins_pro_settings-form-reset-button plethoraplugins_pro_settings-form-action-button" type="button" value="<?php esc_attr_e( __('Reset to defaults') ); ?>" />
			<input name="submit" class="plethoraplugins_pro_settings-form-save-button plethoraplugins_pro_settings-form-action-button" type="submit" value="<?php esc_attr_e( __('Save') ); ?>" />
		</div>
    </form>
	</div>
    <?php

}

add_action( 'admin_menu', function () {
if(!defined( 'plethoraplugins__tabs' )) add_options_page( 'Plethora Tabs + Accordions', 'Tabs + Accordions', 'manage_options', 'plethoraplugins-tabs-settings', 'plethoraplugins_pro_tabs_render_settings_page' );
} );

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), function ( $links ) {
   $plugin_links[] = '<a href="' . esc_url( plethoraplugins_pro_tabs_get_settings_url() ) . '" >' . esc_html__( 'Settings' ) . '</a>';
   $plugin_links[] = '<a href="' . esc_url( 'https://www.plethoradesign.com/tabs-accordions-documentation/' ) . '" target="_blank">' . esc_html__( 'Documentation' ) . '</a>';
	return array_merge( $links, $plugin_links );
});


add_filter( 'plugin_row_meta', function ( $links, $file ) {
	if ( plugin_basename( __FILE__ ) !== $file ) {
		return $links;
	}

	$more = [
		'<a href="' . esc_url(plethoraplugins_pro_tabs_get_settings_url()) . '" >' . esc_html__( 'Settings' ) . '</a>',
		'<a href="' . esc_url( 'https://www.plethoradesign.com/tabs-accordions-documentation/' ) . '" target="_blank" >' . esc_html__( 'Documentation' ) . '</a>',
	];

	return array_merge( $links, $more );
}, 10, 2 );
 

add_action('admin_head',  function () {
 ?>
		 <style type="text/css">
			#adminmenu a[href*="plethoraplugins-tabs-settings"] {
				white-space: nowrap;
			}
			#adminmenu a[href*="plethoraplugins-tabs-settings"]::before {
			content: "";
			background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 124.78678 124.78678'%3E%3Cpath fill='%2372aee6' d='M100.94380209 53.20215898H67.97269467L83.87654218 6.1421274 27.36960191 73.10814514h32.9594733L44.43686181 120.1565426Z'/%3E%3C/svg%3E");
			opacity: .6;
			color: currentColor;
			display: inline-block;
			width: 1.5em;
			height: 1.5em;
			background-repeat: no-repeat;
			margin-right: .2em;
			margin-left: -1em;
			transition: all .2s;
		}
		#adminmenu a[href*="plethoraplugins-tabs-settings"].current::before,
		#adminmenu a[href*="plethoraplugins-tabs-settings"]:focus::before,
		#adminmenu a[href*="plethoraplugins-tabs-settings"]:hover::before {
			opacity: 1;
			margin: 0;
			margin-right: 0.5em;
			transform: scale(1.5);
		}
		#adminmenu a[href*="plethoraplugins-tabs-settings"].current::before {
			background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 124.78678 124.78678'%3E%3Cpath fill='%23fff' d='M100.94380209 53.20215898H67.97269467L83.87654218 6.1421274 27.36960191 73.10814514h32.9594733L44.43686181 120.1565426Z'/%3E%3C/svg%3E");
		}
	  </style>
	  <?php 
	}
);