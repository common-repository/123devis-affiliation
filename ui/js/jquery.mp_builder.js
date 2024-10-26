(function( $ ){

   var methods = {
      init : function (options){
         var cntnr = this;

         if (!options.form) options.form = {};
         if (!options.form.steps) options.form.steps = [];
         if (!options.form.q_in_col) options.form.q_in_col = {};
         if (options.form.steps.length == 0) options.form.steps.push("Title : click to change");

         //default col where its not set
         for (var i in options.questions){
            var q = options.questions[i];
            q.col = (options.form.q_in_col.hasOwnProperty(q.name) ? options.form.q_in_col[q.name] : 0);
         }

         var message = function (msg){
            alert(msg);
         }

         var setup_base_dom = function (){
            cntnr.addClass("sm_multiplude_builder");
            //cntnr.append('<input type="hidden" name="form_config">');
            cntnr.append('<div class="menu"><button id="add_step_btn" class="button" type="button">Add Step </button></div>');
            cntnr.append('<div class="steps"></div>');
            cntnr.append('<div class="clr"></div>');
         }

         var add_step = function (name){
            var step = $('<div class="step"><div class="rm_step"></div><h4 class="title">' + name + '</h4><div class="qlist"></div></div>');
            $(".steps", cntnr).append(step);
            $(".qlist", step).sortable({connectWith:".qlist",cursor: "move", handle: ".hdl"})
            $(".rm_step", step).click(function(){rm_step.call(step);});
            $(".title", step).edit_in_place({});
            return step;
         }

         var rm_step = function(){
           if ($(".qlist", this).children().length > 0) alert("Can't remove a step with forms. Please remove the forms.");
           else $(this).remove();
         }

         var load_data = function (){
            //load steps
            var load_steps = [];

            if (options.form.steps.length){
               for (var i in options.form.steps){
                  load_steps.push(add_step(options.form.steps[i]));
               }
            }

            //load questions
            for (var col in options.form.q_in_col) {
               for (var fld_i in options.form.q_in_col[col]){
                  var q_name = options.form.q_in_col[col][fld_i];
                  for (var q_i in options.questions){
                     var question = options.questions[q_i];
                     if (question.name == q_name){
                        question.found = true;
                        var label = (options.form.q_labels[q_name] ? options.form.q_labels[q_name] : question.label);
                        $(".qlist", load_steps[col]).append('<div class="question ' + question.type + '" id="' + question.name + '"><div class="hdl"></div><span class="lbl label" data-original-label="' + question.label + '">' + label + '</span></div>');
                     }
                  }
               }
            }

            //handle case where new element is added since last save - put new element at end of last step
            //also used for new forms where no elements get found above in load questions and thus default here
            for (var q_i in options.questions){
               var question = options.questions[q_i];
               if (!question.found){
                  var label = question.label;
                  $(".step .qlist", cntnr).last().append('<div class="question ' + question.type + '" id="' + question.name + '"><div class="hdl"></div><span class="lbl label" data-original-label="' + question.label + '">' + label + '</span></div>');
               }
            }
         }

         var validate_and_save = function(){
            var cntnr = this;
            var saveable = 1;
            var saveme = {
               steps : [],
               q_in_col : {},
               q_labels : {}
            };
            $(".step", cntnr).each(function(step_cnt) {
               var title = $("h4.title", this).html();
               if (title == '') {
                  message("A title for each step is required");
                  saveable = 0;
                  return false;
               } else {
                  saveme.steps.push(title);
                }
               var $questions = $(".qlist", this).children();
               if ($questions.length == 0) {
                  message("Each step must have questions");
                  saveable = 0;
                  return false;
               }
               saveme.q_in_col[step_cnt] = [];
               $questions.each(function() {
                  $this = $(this);
                  saveme.q_in_col[step_cnt].push($this.attr("id"));
                  var $label = $(".lbl", $this);
                  if ($label.html() != $label.data("originalLabel")){
                     saveme.q_labels[$this.attr("id")] = $label.html();
                  }
               });
            });

            if (!saveable) return false;
            $("input[name=form_config]").val(JSON.stringify(saveme));

            return true;
         }

         var setup_events = function (){
            $(".qlist", cntnr).sortable({connectWith:".qlist"});
            $("#add_step_btn", cntnr).click(function(){add_step("Title : click to change")});
            $(".steps", cntnr).sortable()
            $(".label", cntnr).edit_in_place({});
            cntnr.closest("form").submit(function(){ return validate_and_save.call(cntnr);});
         }

         return this.each(function() {
            var $this = $(this);
            setup_base_dom($this);
            load_data($this);
            setup_events($this);
         });

      },
	  destroy : function(){
		return this.each(function() {
		   $(this).empty();
		});
	  }
   }

   $.fn.sm_mp_builder = function( mthd ) {
       // Method calling logic
       if ( methods[mthd] ) {
         return methods[ mthd ].apply( this, Array.prototype.slice.call( arguments, 1 ));
       } else if ( typeof mthd === 'object' || ! mthd ) {
         return methods.init.apply( this, arguments );
       } else {
         $.error( 'Method ' +  mthd + ' does not exist on jQuery.tooltip' );
         return false;
       }
   };

})( jQuery );
