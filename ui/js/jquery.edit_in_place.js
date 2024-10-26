(function($) {
    $.fn.extend({
        edit_in_place: function(opts, callback) {
            var self = this;
            var defaults = {
                'input_type': 'text'
            }
            var options = $.extend({}, defaults, opts);

            return this.each(function() {
                var $this = $(this);
                var $input;
                var original_value = $this.html().replace(/<br.*?>/g, '\n');
                var original_display = $this.css('display');

                $this.bind('click', function() {
                    var starting_value = $this.html().replace(/<br.*?>/g, '\n');

                    if (options['input_type'] == 'text') {
                        $input = $.make('input', { type: 'text', name: 'eip_input', value: starting_value });
                    } else if (options['input_type'] == 'textarea') {
                        $input = $.make('textarea', { name: 'eip_input' }, starting_value);
                    }


                    var $form = $.make('div', { className: 'eip-container' }, [
                        $input,
                        $.make('button', { class: 'eip-submit' }, 'OK'),
                        $.make('button', { class: 'eip-cancel' }, 'Cancel')
                    ]);

                    $this.css({'display': 'none'});
                    $this.after($form);
                    $input.focus();
                    if (original_value == starting_value) {
                        $input.select();
                    }

                    var restore_input = function(input) {
                        return function($this, $form) {
                            $this.css({'display': original_display});
                            $form.empty().remove();
                            if (input) {
                                $this.html(input.replace(/[\n\r]+/g, "<br /><br />"));
                                $.isFunction(callback) && callback.call(self, input);
                            }
                        }($this, $form);
                    };

                    setTimeout(function() {
                        $(document).one('click.edit_in_place', function() {
                            restore_input($input.val());
                        });
                        $form.click(function(e) {
                            if (e.target.className == 'eip-cancel') {
                                restore_input();
                                $(document).unbind('click.edit_in_place');
                            } else if (e.target.className == 'eip-submit') {
                                restore_input($input.val());
                                $(document).unbind('click.edit_in_place');
                            }
                            e.stopPropagation;
                            return false;
                        });
                    }, 10);
                });

            });
        }
    });

    $.extend({

        make: function(){
            var $elem,text,children,type,name,props;
            var args = arguments;
            var tagname = args[0];
            if(args[1]){
                if (typeof args[1]=='string'){
                    text = args[1];
                }else if(typeof args[1]=='object' && args[1].push){
                  children = args[1];
                }else{
                    props = args[1];
                }
            }
            if(args[2]){
                if(typeof args[2]=='string'){
                    text = args[2];
                }else if(typeof args[1]=='object' && args[2].push){
                  children = args[2];
                }
            }
            if(tagname == 'text' && text){
                return document.createTextNode(text);
            }else{
                $elem = $(document.createElement(tagname));
                if(props){
                    for(var propname in props){
                      if (props.hasOwnProperty(propname)) {
                            if($elem.is(':input') && propname == 'value'){
                                $elem.val(props[propname]);
                            } else {
                                $elem.attr(propname, props[propname]);
                            }
                        }
                    }
                }
                if(children){
                    for(var i=0; i < children.length; i++){
                        if(children[i]){
                            $elem.append(children[i]);
                        }
                    }
                }
                if(text){
                    $elem.html(text);
                }
                return $elem;
            }
        }

    });
})(jQuery);