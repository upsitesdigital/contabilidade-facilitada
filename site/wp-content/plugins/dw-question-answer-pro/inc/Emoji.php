<?php
if ( !defined( 'ABSPATH' ) ) exit;

class DWQA_Emoji {

	public function __construct() {
		global $dwqa_general_settings;
		if(isset($dwqa_general_settings['emoji']) && $dwqa_general_settings['emoji']){
			// add_action('dwqa_before_single_question_content', array($this, 'addFilterContent'));
			add_filter('the_content', array($this, 'filterEmoji'), 1);
			add_action('wp_enqueue_scripts', array($this, 'addScripts'));

            add_action( 'init', array( $this, 'tinymceAddbuttons' ) );

            add_filter('dwqa_sript_vars', array($this, 'addEmoticonsVar'), 10 ,1);
		}
	}

    public function tinymceAddbuttons() {
        if ( get_user_option( 'rich_editing' ) == 'true' && ! is_admin() ) {
            add_filter( 'mce_external_plugins', array( $this, 'addCustomTinymcePlugin' ) );
            add_filter( 'mce_buttons', array( $this, 'registerCustomButton' ) );
            add_filter( 'dwqa_tinymce_toolbar1', array( $this, 'addToolbar1' ), 10 ,1  );
        }
    }

    public function addToolbar1($toolbar){
        return $toolbar.'dwqaEmoji,|,';
    }
    public function registerCustomButton( $buttons ) {
        array_push( $buttons, '|', 'dwqaEmoji' );
        return $buttons;
    } 


    public function addCustomTinymcePlugin( $plugin_array ) {
        global $dwqa_options;
        if ( is_singular( 'dwqa-question' ) || ( $dwqa_options['pages']['submit-question'] && is_page( $dwqa_options['pages']['submit-question'] ) ) ) {
            $plugin_array['dwqaEmoji'] = DWQA_URI . 'assets/js/code-emoji-button.js';
        }
        return $plugin_array;
    }

	public function addScripts(){
		// if ( is_singular( 'dwqa-question' ) ) {
			wp_enqueue_style( 'dwqa-emoji-style', DWQA_URI . 'assets/css/emoticons.css', array(), true );
			// wp_enqueue_script( 'dwqa-emoji-script', DWQA_URI . 'assets/js/emoticon.js');

			// $dwqa_emoji_var = self::getEmoticonData();
			// wp_localize_script( 'dwqa-emoji-script', 'dwqa_emoji_var', $dwqa_emoji_var );
		// }
	}
	public function filterEmoji($content){
		if ( is_singular( 'dwqa-question' ) ) {
			$content = self::getEmoticon($content);
		}
		return $content;
	}

    public function addEmoticonsVar($dwqa_sript_vars){
        $dwqa_sript_vars['emoticons'] = self::getEmoticonData();
        return $dwqa_sript_vars;
    }

	static public function getEmoticon($str) {

        $emoticons = self::getEmoticonData();
        // in order to replace >:) before :)
        $emoticons = array_reverse($emoticons);        

        foreach ($emoticons as $key => $emotion) {
            $mockup = '<span title="'.$key.'" class="dwqa-content-emo2 dwqa-emo2 dwqa-emo2-'.$key.'"></span>';
            $str = str_replace($emotion, $mockup, $str);
        }

        return $str;
    }
    static public function getEmoticonData() {
        return array(
            'smile' => array(':)', ':-)', ':smile:'),
            'grin' => array(':D', ':grin:'),
            'beaming' => array('^^', ':beaming:'),
            'squinting' => array('xD', ':squinting:'),
            'star' => array(':star:'),
            'heart' => array('&lt;3', ':heart:', '<3'),
            'love' => array(':love:'),
            'kiss' => array(':-*', ':kiss:'),
            'wink' => array(';)', ':wink:'),
            'tongue' => array(':p', ':P', ':tongue:'),
            'stongue' => array('xP', ':stongue:'),
            'cool' => array('B)', 'B-)', ':cool:'),
            'hug' => array(':hug:'),
            'money' => array('$-D', '$-)', '$-P', ':money:'),
            'poop' => array(':poop:'),
            'evil' => array('&gt;:)', ':evil:', '&gt;:D', '>:D'),
            'joy' => array(':joy:', ':lmao:'),
            'rofl' => array(':rofl:'),
            'sweat' => array('^^!', ':sweat:'),
            'confused' => array(':?', ':confused:'),
            'flushed' => array(':flushed:'),
            'hmm' => array('-_-', ':hmm:'),
            'neutral' => array(':|', ':neutral:'),
            'shock' => array(':o', ':O', ':shock:'),
            'sleep' => array(':sleep:'),
            'think' => array(':think:'),
            'sexy' => array(':sexy:'),
            'whut' => array(':whut:', ':what:'),
            'unamused' => array(':unamused:'),
            'zipper' => array(':zipper:'),
            'sad' => array(':(', ':sad:'),
            'tired' => array('x-(', 'x(', ':tired:'),
            'worried' => array(':-s', ':worried:'),
            'angry' => array('&gt;:(', ':angry:'),
            'pouting' => array(':pouting:'),
            'dizzy' => array(':dizzy:'),
            'fear' => array(':fear:'),
            'fearful' => array(':fearful:'),
            'cry' => array('T_T', 'T.T', ':cry:'),
            'ill' => array(':ill:'),
            'sneezing' => array(':sneezing:'),
            'cold' => array(':cold:')
        );
    }

	public function addFilterContent(){
		// add_filter('the_content', );
	}
}
?>