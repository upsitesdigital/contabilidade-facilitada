(function() {
	var youtube_body = [
						{
							type: 'textbox',
							name: 'dw_embed_youtube_link',
							label: 'Youtube Link'
						},
					];
	var vimeo_body = [
					{
						type: 'textbox',
						name: 'dw_embed_vimeo_link',
						label: 'Vimeo Link'
					},
				];
	var sc_body = [
				{
					type: 'textbox',
					name: 'dw_embed_sc_link',
					label: 'SoundCloud Link'
				},
			];

	tinymce.PluginManager.add('dw_embed_admin_button', function( editor, url ) {
		editor.addButton( 'dw_embed_youtube_button', {
			tooltip: 'DW Youtube shortcode',
			image: dw_embed_home_url + 'assets/images/youtube-black.png',
			onclick: function() {
				var test = editor.windowManager.open( {
					title: 'DW Youtube ShortCode',
					body: youtube_body,
					onsubmit: function( e ) {
						var content = '[dw_youtube]';
						if( e.data.dw_embed_youtube_link ) {
							content += e.data.dw_embed_youtube_link;
						}

						content += '[/dw_youtube]';
						editor.insertContent( content );
					}
				});
			}
		});

		editor.addButton( 'dw_embed_vimeo_button', {
			tooltip: 'DW Vimeo shortcode',
			image: dw_embed_home_url + 'assets/images/vimeo-black.png',
			onclick: function() {
				var test = editor.windowManager.open( {
					title: 'DW Vimeo ShortCode',
					body: vimeo_body,
					onsubmit: function( e ) {
						var content = '[dw_vimeo]';
						if( e.data.dw_embed_vimeo_link ) {
							content += e.data.dw_embed_vimeo_link;
						}

						content += '[/dw_vimeo]';
						editor.insertContent( content );
					}
				});
			}
		});

		editor.addButton( 'dw_embed_soundcloud_button', {
			tooltip: 'DW SoundCloud shortcode',
			image: dw_embed_home_url + 'assets/images/soundcloud-black.png',
			onclick: function() {
				var test = editor.windowManager.open( {
					title: 'DW SoundCloud ShortCode',
					body: sc_body,
					onsubmit: function( e ) {
						var content = '[dw_soundcloud]';
						if( e.data.dw_embed_sc_link ) {
							content += e.data.dw_embed_sc_link;
						}
						content += '[/dw_soundcloud]';
						editor.insertContent( content );
					}
				});
			}
		});
	});
})();