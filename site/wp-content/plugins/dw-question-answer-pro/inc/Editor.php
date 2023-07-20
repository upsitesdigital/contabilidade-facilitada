<?php  

function dwqa_init_tinymce_editor( $args = array() ) {
	global $dwqa;
	$dwqa->editor->display( $args );
}

function dwqa_paste_srtip_disable( $mceInit ){
	$mceInit['paste_strip_class_attributes'] = 'none';
	return $mceInit;
}

class DWQA_Editor {

	public function __construct() {

		add_action( 'init', array( $this, 'tinymce_addbuttons' ) );

		add_filter( 'dwqa_prepare_edit_answer_content', 'wpautop' );
		add_filter( 'dwqa_prepare_edit_question_content', 'wpautop' );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue() {
		global $dwqa_general_settings;
		if ( isset( $dwqa_general_settings['markdown-editor'] ) && $dwqa_general_settings['markdown-editor'] ) {
			wp_enqueue_script( 'dwqa_simplemde', DWQA_URI . 'assets/js/simplemde.min.js', array(), true );
			wp_enqueue_style( 'dwqa_simplemde', DWQA_URI . 'assets/css/simplemde.min.css' );
		}
	}
	
	public function tinymce_addbuttons() {
		if ( get_user_option( 'rich_editing' ) == 'true' && ! is_admin() ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_custom_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_custom_button' ) );
		}
	}

	public function register_custom_button( $buttons ) {
		array_push( $buttons, '|', 'dwqaCodeEmbed' );
		return $buttons;
	} 

	public function add_custom_tinymce_plugin( $plugin_array ) {
		global $dwqa_options;
		if ( is_singular( 'dwqa-question' ) || ( $dwqa_options['pages']['submit-question'] && is_page( $dwqa_options['pages']['submit-question'] ) ) ) {
			$plugin_array['dwqaCodeEmbed'] = DWQA_URI . 'assets/js/code-edit-button.js';
		}
		return $plugin_array;
	}
	public function display( $args ) {
		global $dwqa_general_settings;
		extract( wp_parse_args( $args, array(
				'content'       => '',
				'id'            => 'dwqa-custom-content-editor',
				'textarea_name' => 'custom-content',
				'rows'          => 5,
				'wpautop'       => false,
				'media_buttons' => false,
		) ) );

		$dwqa_tinymce_css = apply_filters( 'dwqa_editor_style', DWQA_URI . 'templates/assets/css/editor-style.css' );
		$toolbar1 = apply_filters( 'dwqa_tinymce_toolbar1', 'bold,italic,underline,|,' . 'bullist,numlist,blockquote,|,' . 'link,unlink,|,' . 'image,code,|,'. 'spellchecker,fullscreen,dwqaCodeEmbed,|,' );

		if ( isset( $dwqa_general_settings['markdown-editor'] ) && $dwqa_general_settings['markdown-editor'] ) {
			$this->editor( $content, $id, $args );
		} else {
			$array_setting_wp_editor = array(
				'wpautop'       => $wpautop,
				'media_buttons' => $media_buttons,
				'textarea_name' => $textarea_name,
				'textarea_rows' => $rows,
				'tinymce' => array(
						'toolbar1' => $toolbar1,
						'toolbar2'   => '',
						'content_css' => $dwqa_tinymce_css
				),
				'quicktags'     => true,
			);
			$array_setting_wp_editor = apply_filters('dwqa_array_setting_wp_editor', $array_setting_wp_editor);
			wp_editor( $content, $id, $array_setting_wp_editor);
		}
	}

	public function editor( $content, $id, $settings = array() ) {

		$toolbar = '[
			{
				name: "bold",
				action: SimpleMDE.toggleBold,
				className: "fa fa-bold",
				title: "'.__("Bold", 'dwqa').'"
			},
			{
				name: "italic",
				action: SimpleMDE.toggleItalic,
				className: "fa fa-italic",
				title: "'.__("Italic", 'dwqa').'"
			},
			"|",
			{
				name: "link",
				action: SimpleMDE.drawLink,
				className: "fa fa-link",
				title: "'.__("Create Link", 'dwqa').'"
			},
			{
				name: "image",
				action: SimpleMDE.drawImage,
				className: "fa fa-picture-o",
				title: "'.__("Insert Image", 'dwqa').'"
			},
			"|",
			{
				name: "preview",
				action: SimpleMDE.togglePreview,
				className: "fa fa-eye no-disable",
				title: "'.__("Toggle Preview", 'dwqa').'"
			},
			{
				name: "fullscreen",
				action: SimpleMDE.toggleFullScreen,
				className: "fa fa-arrows-alt no-disable no-mobile",
				title: "'.__("Toggle Fullscreen", 'dwqa').'"
			},
			{
				name: "guide",
				action: "//simplemde.com/markdown-guide",
				className: "fa fa-question-circle",
				default: true,
				title: "'.__("Markdown Guide", 'dwqa').'"
			},
		]';

		$default = array(
			'editor_class' => 'dwqa_editor',
			'placeholder' => '',
			'spellchecker' => false,
			'toolbar' => apply_filters( 'dwqa_markdown_editor_toolbar', $toolbar),
			'tabsize' => 4,
			'tabindex' => false,
			'textarea_rows' => 5,
			'textarea_cols' => 10,
			'autofocus' => false
		);

		$set = wp_parse_args( $settings, $default );

		$set['textarea_name'] = isset( $set['textarea_name'] ) ? $set['textarea_name'] : $id;
		echo '<textarea class="'.$set['editor_class'].'" id="'.$id.'" name="'.$set['textarea_name'].'" rows="'.$set['textarea_rows'].'" cols="'.$set['textarea_cols'].'" placeholder="'.$set['placeholder'].'" tabindex="'.$set['tabindex'].'">'.$content.'</textarea>';
		?>
		<script type="text/javascript">
			var dwqa_simplemde = new SimpleMDE({
				element: document.getElementById("<?php echo $id ?>"),
				autofocus: <?php echo $set['autofocus'] ? 'true' : 'false' ?>,
				placeholder: '<?php echo $set['placeholder'] ?>',
				spellChecker: <?php echo $set['spellchecker'] ? 'true' : 'false' ?>,
				tabSize: '<?php echo $set['tabsize'] ?>',
				toolbar: <?php echo !empty( $set['toolbar'] ) ? $set['toolbar'] : '' ?>,
				lineWrapping: true,
				promptURLs: true
			});
		</script>
		<!-- translate Text -->
		<style>
			.editor-statusbar .lines:before{
			    content: '<?php _e('lines: ', 'dwqa')?>' !important;
			}
			.editor-statusbar .words:before{
				content: '<?php _e('words: ', 'dwqa')?>' !important;
			}
		</style>
		<?php
	}
}

?>