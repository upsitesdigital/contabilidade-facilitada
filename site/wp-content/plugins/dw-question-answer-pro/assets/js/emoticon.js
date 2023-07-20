//not use this file at this time
(function(root, $) {


    function initialize() {

    }

    function showBoard(elm) {
        this.$editor = {};
        
        if ($(elm).parent().parent().data('editor') != "") {
            this.$editor = $(elm).parent().parent().data('editor')
        }
        $('.dwqa-emoticon-js__board').remove();
        var $body = $('body'),
                $board = $('.dwqa-emoticon-js__board'),
                $icon = $(elm).parents('.dwqa-icon--emoticon'),
                offset = $(elm).offset(),
                offsetTop = 0,
                emoticons = dwqa_emoji_var,
                isRTL = $('html').attr('dir') === 'rtl';

        if (!$board.length) {
            html = renderBoard(emoticons);
            $body.append(html);
            $board = $('.dwqa-emoticon-js__board');
        }

        var spacer = isRTL ? 15 : ($board.outerWidth() - 30);
        var above = {
            display: 'block',
            top: (offset.top - $board.outerHeight()) + 'px',
            left: (offset.left - spacer) + 'px',
            position: 'absolute'
        }

        var animate_above = {
            opacity: '1',
            top: (offset.top - $board.outerHeight() - 10) + 'px'
        }

        var below = {
            display: 'block',
            top: (offset.top + 20) + 'px',
            left: (offset.left - spacer) + 'px',
            position: 'absolute'
        }

        var animate_below = {
            opacity: '1',
            top: (offset.top + 24) + 'px'
        }

        offsetTop = offset.top - $(window).scrollTop();
        var pos, ani, positionClass;

        if (offsetTop > ($board.outerHeight() + 30)) {
            pos = above;
            ani = animate_above;
            positionClass = 'dwqa-board--above'
        } else {
            pos = below;
            ani = animate_below;
            positionClass = 'dwqa-board--below';
        }

        $board.is(':hidden') && setTimeout(function () {
            $('.dwqa-icon--active').removeClass('dwqa-icon--active');
            $icon.addClass('dwqa-icon--active');
            $board.css(pos);
            $board.addClass(positionClass)
            setTimeout(function () {
                $board.css(ani);
            }, 100);

            $(document).one('click', function () {
                $board.css({
                    display: 'none',
                    opacity: '0'
                });

                $board.removeClass('dwqa-board--above dwqa-board--below');
            });
        }, 100)
    }

    function renderBoard(emoticons) {
        var html = '<ul class="dwqa-emoticon__board dwqa-emoticon-js__board">';
        for (var key in emoticons) {
            var emo = emoticons[key];
            html += '\
        <li>\
            <span\
                title="' + key + '"\
                onclick="dwqa_emoji.insert(this)"\
                class="dwqa-emo2 dwqa-emo2-' + key + '"\
                data-code="' + emo[0] + '" >\
            </span>\
        </li>\
        ';
        }

        html += '</ul>';

        return html;
    }


    function insert(elm) {
        var code = $(elm).attr('data-code') ;
        this.$editor.smileyCallback(code );
    }

    // $('.dwqa-comment-text').on('focus',function(){
    //     console.log(this);
    //     showBoard(this);
    // });


    // root.dwqa_emoji = root.dwqa_emoji || {};
    // root.dwqa_emoji.showBoard = showBoard;
    // root.dwqa_emoji.insert = insert;

})(window, jQuery);

