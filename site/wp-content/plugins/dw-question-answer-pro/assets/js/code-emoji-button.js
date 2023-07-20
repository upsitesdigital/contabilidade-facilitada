(function($) {
    tinymce.create('tinymce.plugins.dwqaEmoji', {
        init : function(ed, url) {
            // console.log(ed);
            // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
            ed.addCommand('dwqaEmojiCommand', function(elm) {
                // console.log(elm);
                var code = $(elm).attr('data-code') ;
                tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, code + ' ' );
            });
            ed.addCommand('dwqaEmojiInitCommand', function() {
                $('.dwqa-emoticon-js__board').closest('.mce-panel').addClass('dwqaCustomTinymcePanel');
            });

            // Register example button
            ed.addButton('dwqaEmoji', {
                title : 'Emoji',
                cmd : 'dwqaEmojiInitCommand',
                image : false,
                icon: 'dwqa_emotions',
                type: 'panelbutton',
                panel: {
                    role: 'application',
                    autohide: !0,
                    html: renderBoard()
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
        getInfo : function() {
            return {
                    longname : 'Emoji',
                    author : 'DesignWall',
                    authorurl : 'http://designwall.com',
                    infourl : 'http://designwall.com',
                    version : "1.0"
            };
        }
    });
    // Register plugin
    tinymce.PluginManager.add('dwqaEmoji', tinymce.plugins.dwqaEmoji);

    function renderBoard() {
        var emoticons = dwqa.emoticons;
        var html = '<ul class="dwqa-emoticon__board dwqa-emoticon-js__board">';
        for (let key in emoticons) {
            var emo = emoticons[key];
            html += '\
        <li>\
            <span\
                title="' + key + '"\
                onclick="tinyMCE.execCommand(\'dwqaEmojiCommand\',this);return false;"\
                class="dwqa-emo2 dwqa-emo2-' + key + '"\
                data-code="' + emo[0] + '" >\
            </span>\
        </li>\
        ';
        }

        html += '</ul>';
        return html;
    }
})(jQuery);