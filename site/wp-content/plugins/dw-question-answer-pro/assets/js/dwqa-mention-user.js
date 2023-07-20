//add lib 
//https://github.com/component/textarea-caret-position
/* jshint browser: true */

(function () {

// We'll copy the properties below into the mirror div.
// Note that some browsers, such as Firefox, do not concatenate properties
// into their shorthand (e.g. padding-top, padding-bottom etc. -> padding),
// so we have to list every single property explicitly.
var properties = [
  'direction',  // RTL support
  'boxSizing',
  'width',  // on Chrome and IE, exclude the scrollbar, so the mirror div wraps exactly as the textarea does
  'height',
  'overflowX',
  'overflowY',  // copy the scrollbar for IE

  'borderTopWidth',
  'borderRightWidth',
  'borderBottomWidth',
  'borderLeftWidth',
  'borderStyle',

  'paddingTop',
  'paddingRight',
  'paddingBottom',
  'paddingLeft',

  // https://developer.mozilla.org/en-US/docs/Web/CSS/font
  'fontStyle',
  'fontVariant',
  'fontWeight',
  'fontStretch',
  'fontSize',
  'fontSizeAdjust',
  'lineHeight',
  'fontFamily',

  'textAlign',
  'textTransform',
  'textIndent',
  'textDecoration',  // might not make a difference, but better be safe

  'letterSpacing',
  'wordSpacing',

  'tabSize',
  'MozTabSize'

];

var isBrowser = (typeof window !== 'undefined');
var isFirefox = (isBrowser && window.mozInnerScreenX != null);

function getCaretCoordinates(element, position, options) {
  if (!isBrowser) {
    throw new Error('textarea-caret-position#getCaretCoordinates should only be called in a browser');
  }

  var debug = options && options.debug || false;
  if (debug) {
    var el = document.querySelector('#input-textarea-caret-position-mirror-div');
    if (el) el.parentNode.removeChild(el);
  }

  // The mirror div will replicate the textarea's style
  var div = document.createElement('div');
  div.id = 'input-textarea-caret-position-mirror-div';
  document.body.appendChild(div);

  var style = div.style;
  var computed = window.getComputedStyle ? window.getComputedStyle(element) : element.currentStyle;  // currentStyle for IE < 9
  var isInput = element.nodeName === 'INPUT';

  // Default textarea styles
  style.whiteSpace = 'pre-wrap';
  if (!isInput)
    style.wordWrap = 'break-word';  // only for textarea-s

  // Position off-screen
  style.position = 'absolute';  // required to return coordinates properly
  if (!debug)
    style.visibility = 'hidden';  // not 'display: none' because we want rendering

  // Transfer the element's properties to the div
  properties.forEach(function (prop) {
    if (isInput && prop === 'lineHeight') {
      // Special case for <input>s because text is rendered centered and line height may be != height
      style.lineHeight = computed.height;
    } else {
      style[prop] = computed[prop];
    }
  });

  if (isFirefox) {
    // Firefox lies about the overflow property for textareas: https://bugzilla.mozilla.org/show_bug.cgi?id=984275
    if (element.scrollHeight > parseInt(computed.height))
      style.overflowY = 'scroll';
  } else {
    style.overflow = 'hidden';  // for Chrome to not render a scrollbar; IE keeps overflowY = 'scroll'
  }

  div.textContent = element.value.substring(0, position);
  // The second special handling for input type="text" vs textarea:
  // spaces need to be replaced with non-breaking spaces - http://stackoverflow.com/a/13402035/1269037
  if (isInput)
    div.textContent = div.textContent.replace(/\s/g, '\u00a0');

  var span = document.createElement('span');
  // Wrapping must be replicated *exactly*, including when a long word gets
  // onto the next line, with whitespace at the end of the line before (#7).
  // The  *only* reliable way to do that is to copy the *entire* rest of the
  // textarea's content into the <span> created at the caret position.
  // For inputs, just '.' would be enough, but no need to bother.
  span.textContent = element.value.substring(position) || '.';  // || because a completely empty faux span doesn't render at all
  div.appendChild(span);

  var coordinates = {
    top: span.offsetTop + parseInt(computed['borderTopWidth']),
    left: span.offsetLeft + parseInt(computed['borderLeftWidth']),
    height: parseInt(computed['lineHeight'])
  };

  if (debug) {
    span.style.backgroundColor = '#aaa';
  } else {
    document.body.removeChild(div);
  }

  return coordinates;
}

if (typeof module != 'undefined' && typeof module.exports != 'undefined') {
  module.exports = getCaretCoordinates;
} else if(isBrowser) {
  window.getCaretCoordinates = getCaretCoordinates;
}

}());


jQuery(document).ready(function($){
	$.widget("ui.triggeredAutocomplete", $.extend({}, $.ui.autocomplete.prototype, {

		options:{
			trigger: "@",
			dwMinlength: 1,
		},

		_create:function() {

			var self = this;
			this.id_map = new Object();
			this.stopIndex = -1;
			this.stopLength = -1;
			this.contents = '';
			this.cursorPos = 0;

            // Check for the id_map as an attribute.  This is for editing.

            var id_map_string = this.element.attr('id_map');
            if(id_map_string) this.id_map = jQuery.parseJSON(id_map_string);

            this.ac = $.ui.autocomplete.prototype;
            this.ac._create.apply(this, arguments);

            this.updateHidden();

            // Select function defined via options.
            this.options.select = function(event, ui) {
            	var contents = self.contents;
            	var cursorPos = self.cursorPos;

                // Save everything following the cursor (in case they went back to add a mention)
                // Separate everything before the cursor
                // Remove the trigger and search
                // Rebuild: start + result + end

                var end = contents.substring(cursorPos, contents.length);
                var start = contents.substring(0, cursorPos);
                start = start.substring(0, start.lastIndexOf(self.options.trigger));


                this.value = start + self.options.trigger+ui.item.label +' ' + end;

                // Create an id map so we can create a hidden version of this string with id's instead of labels.

                self.id_map[ui.item.label] = ui.item.value;
                self.updateHidden();

                return false;
            };

            // Don't change the input as you browse the results.
            this.options.focus = function() { return false; }
            this.menu.options.blur = function(event, ui) { return false; }

            this.options.position = { my: "left top", at: "left bottom" };

            // Any changes made need to update the hidden field.
            this.element.focus(function() { self.updateHidden(); });
            this.element.change(function() { self.updateHidden(); });
        },

        search: function(value, event) {

        	var contents = this.element.val();
        	var cursorPos = this.getCursor();
        	this.contents = contents;
        	this.cursorPos = cursorPos;
          //http://jsfiddle.net/vsJRm/1002/


        	if (contents.indexOf(this.options.trigger) >= 0 && contents.length > this.options.dwMinlength) {

            var start = contents.substring(0, cursorPos);
            start = start.substring(0, start.lastIndexOf(this.options.trigger));
            var coordinates = getCaretCoordinates(this.element[0], start.length);

            this.options.position = { my: "left+"+coordinates.left+" top+"+(coordinates.top+30), at: "left top", of: this.element[0] };
            // Get the characters following the trigger and before the cursor position.
            // Get the contents up to the cursortPos first then get the lastIndexOf the trigger to find the search term.

            contents = contents.substring(0, cursorPos);
            var term = contents.substring(contents.lastIndexOf(this.options.trigger) + 1, contents.length);

            // Only query the server if we have a term and we haven't received a null response.
            // First check the current query to see if it already returned a null response.

            if(this.stopIndex == contents.lastIndexOf(this.options.trigger) && term.length > this.stopLength) { term = ''; }

            if(term.length > 0) {
            	return this._search(term);
            }else{
             this.close();
            }
          }    
        },

        // Slightly altered the default ajax call to stop querying after the search produced no results.
        // This is to prevent unnecessary querying.
        //dwcustom

        _initSource: function() {
        	var self = this, array, url;
        	if(this.options.source==='dwSource'){
        		this.source = function( request, response ) {
        			if ( self.xhr ) {
        				self.xhr.abort();
        			}
        			self.xhr = $.ajax({
        				url: this.options.dwDataAjax.url,
        				data: {
        					action: this.options.dwDataAjax.action,
        					nonce: this.options.dwDataAjax.nonce,
        					name: request.term
        				},
        				type: this.options.dwDataAjax.type,
        				dataType: this.options.dwDataAjax.dataType,
        				success: function(data) {
        					if(typeof data !== "undefined" && data.success && data.result != null && data.result.length>0) {
        						
        						response($.map(data.result, function(item) {
        							return {
        								label: item.name,
        								value: item.id
        							}
        						}));
        						self.stopLength = -1;
        						self.stopIndex = -1;
        					}
        					else {
                                // No results, record length of string and stop querying unless the length decreases
                                self.stopLength = request.term.length;
                                self.stopIndex = self.contents.lastIndexOf(self.options.trigger);
                                self.close();
                            }
                        }
                    });
        		};
        	}else{
        		if ( $.isArray(this.options.source) ) {
	        		array = this.options.source;
	        		this.source = function( request, response ) {
	        			response( $.ui.autocomplete.filter(array, request.term) );
	        		};
	        	} else if ( typeof this.options.source === "string" ) {
	        		url = this.options.source;
	        		this.source = function( request, response ) {
	        			if ( self.xhr ) {
	        				self.xhr.abort();
	        			}
	        			self.xhr = $.ajax({
	        				url: url,
	        				data: request,
	        				dataType: 'json',
	        				success: function(data) {
	        					if(data != null) {
	        						response($.map(data, function(item) {
	        							return {
	        								label: item.label,
	        								value: item.value
	        							}
	        						}));
	        						self.stopLength = -1;
	        						self.stopIndex = -1;
	        					}
	        					else {
	                                // No results, record length of string and stop querying unless the length decreases
	                                self.stopLength = request.term.length;
	                                self.stopIndex = self.contents.lastIndexOf(self.options.trigger);
	                                self.close();
	                            }
	                        }
	                    });
	        		};
	        	} else {
	        		this.source = this.options.source;
	        	}
        	}
        },

        destroy: function() {
        	$.Widget.prototype.destroy.call(this);
        },

        // Gets the position of the cursor in the input box.

        getCursor: function() {
        	var i = this.element[0];
        	if(i.selectionStart) {
        		return i.selectionStart;
        	}
        	else if(i.ownerDocument.selection) {
        		var range = i.ownerDocument.selection.createRange();
        		if(!range) return 0;
        		var textrange = i.createTextRange();
        		var textrange2 = textrange.duplicate();

        		textrange.moveToBookmark(range.getBookmark());
        		textrange2.setEndPoint('EndToStart', textrange);
        		return textrange2.text.length;
        	}
        },

        // Populates the hidden field with the contents of the entry box but with 
        // ID's instead of usernames.  Better for storage.

        updateHidden: function() {
        	var trigger = this.options.trigger;
            var contents = this.element.val();
        	for(var key in this.id_map) {
        		var find = trigger+key;
        		find = find.replace(/[^a-zA-Z 0-9@]+/g,'\\$&');
        		regex = new RegExp(find, "g");
                contents = contents.replace(regex, trigger+'['+this.id_map[key]+']');
        	}
            $(this.options.hidden).val(contents);
        	$(this.options.hiddenHighlight).val(JSON.stringify(this.id_map));
        },

        _renderMenu: function( ul, items ) {
          var that = this;
          $.each( items, function( index, item ) {
            that._renderItemData( ul, item );
          });
          $( ul ).addClass( "dwqa-mention-ui" );
        },

    }));

  if($('.dwqa-content-ask-form textarea, .dwqa-comment-form textarea, .dwqa-answer-form textarea').length){

  	$('.dwqa-content-ask-form textarea, .dwqa-comment-form textarea, .dwqa-answer-form textarea, #dwqa-answer-content_ifr #tinymce').each(function(){
      var parent = $(this).parent();
      var element = $(this);

      element.triggeredAutocomplete({
        hidden: '.dwqa-mention-comment',
        hiddenHighlight: '.dwqa-mention-comment-highlight',
        source: 'dwSource',
        dwMinlength: 3,
        dwDataAjax: {
          action: 'dwqa_mention_user',
          nonce: dwqa_mention.dwqa_mention_nonce,
          url: dwqa_mention.dwqa_mention_ajax_url,
          type: "POST",
          dataType: "json",
        },
        trigger: "@",
        messages: {
          results: function(e){

          },
          noResults: function(e){

          },
        },
        delay: 1000,
        appendTo:parent
      });
    });
  }

});