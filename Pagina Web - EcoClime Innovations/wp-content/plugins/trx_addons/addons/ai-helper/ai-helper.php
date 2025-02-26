<?php
/**
 * AI Helper - a companion to write a post title, excerpt and content, generate images and support a chat with AI technologies
 *
 * @addon ai-helper
 * @version 1.0
 *
 * @package ThemeREX Addons
 * @since v2.20.0
 */

namespace TrxAddons\AiHelper;

// Register autoloader for the addon's classes
require_once TRX_ADDONS_PLUGIN_DIR_CLASSES . 'Autoloader.php';
new \TrxAddons\Core\Autoloader( array(
	'path' => dirname( __FILE__ ),
	'namespace' => __NAMESPACE__,
	'allowed_namespaces' => array(
		'ThemeRex\\Ai',
		'ThemeRex\\OpenAi',
		'ThemeRex\\StableDiffusion',
		'ThemeRex\\ModelsLab',
		'ThemeRex\\StabilityAi',
		'ThemeRex\\FlowiseAi',
		'ThemeRex\\GoogleAi',
		'ThemeRex\\LumaLabs',
		'Rahul900day\\Gpt3Encoder',
		'Markdown\\Parser'
	),
	'folders_map' => array(
		'Markdown\\Parser' => 'vendors',
		'Rahul900day\\Gpt3Encoder' => 'vendors',
		'ThemeRex\\Ai' => 'vendors',
		'ThemeRex\\FlowiseAi' => 'vendors',
		'ThemeRex\\GoogleAi' => 'vendors',
		'ThemeRex\\ModelsLab' => 'vendors',
		'ThemeRex\\OpenAi' => 'vendors',
		'ThemeRex\\StabilityAi' => 'vendors',
		'ThemeRex\\StableDiffusion' => 'vendors',
		'ThemeRex\\LumaLabs' => 'vendors',
		'AssistantTools' => 'support',
		'EmbedChats' => 'support',
		'Gutenberg' => 'support',
		'MediaLibrary' => 'support',
		'ProcessSelection' => 'support',
		'TrxAiAssistants' => 'support',
		'Elementor' => 'support',
	),
	'classes_map' => array(
		'TrxAddons\\Core\\Singleton' => '/includes/classes/Singleton.php',	// Absolute path from the root of the plugin.
	),
) );

// Add options to the ThemeREX Addons Options
new Options();

// Load TrxAiAssistants chatbot
new TrxAiAssistants\Helper();

// Load Embed external chatbots
new EmbedChats\Helper();

// Load Assistant Tools support
new AssistantTools\Helper();

// Load ProcessSelection support
new ProcessSelection\Helper();

// Load Elementor support
if ( is_admin() && trx_addons_exists_elementor() ) {
	new Elementor\Helper();
	new Elementor\CompanyGenerator();
}

// Load a Gutenberg support
if ( ( function_exists( 'trx_addons_is_preview' ) && trx_addons_is_preview( 'gutenberg' ) )
	|| ( wp_is_json_request() && strpos( trx_addons_get_current_url(), 'ai-helper/v1' ) !== false )
) { 
	new Gutenberg\Helper();
}

// Load a MediaSelector support
if ( is_admin() || ( wp_doing_ajax() && strpos( trx_addons_get_value_gp( 'action' ), 'trx_addons_ai_helper_' ) !== false ) ) {
	new MediaLibrary\Helper();
}

// Load a shortcode "Image Generator"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/igenerator.php';

// Load a shortcode "Text Generator"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/tgenerator/tgenerator.php';

// Load a shortcode "Chat"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat/chat.php';

// Load a shortcode "Chat Topics"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_topics/chat_topics.php';

// Load a shortcode "Chat History"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/chat_history/chat_history.php';

// Load a shortcode "Music Generator"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/mgenerator/mgenerator.php';

// Load a shortcode "Audio Generator"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/agenerator/agenerator.php';

// Load a shortcode "Video Generator"
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/vgenerator/vgenerator.php';
