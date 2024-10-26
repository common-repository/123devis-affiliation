(function() {
	tinymce.PluginManager.add('smtinymceplugin', function( editor, url ) {
		this.sm_url_path = url;
		var any = 0;
		var menu = [];
		for (var i in sm_embedeable_names){
			console.log(typeof sm_embedeable_names[i]);
			console.log(typeof sm_embedeable_names[i].length);
			if (typeof sm_embedeable_names[i].length == 'number'){
				any += sm_embedeable_names[i].length;
			}
		}
		console.log(sm_categories);
		console.log(any);
		if (any == 0){
			return;
		}
		console.log(sm_embedeable_names);

		console.log(menu);

		//show the saved ones
		for (var type_key in sm_embedeable_names){
			if (sm_embedeable_names[type_key].length > 0){
				var smenu = [];
				for (var name_key in sm_embedeable_names[type_key]){
					var e_name = sm_embedeable_names[type_key][name_key];
					var short_code = '[sm action="named_' + type_key + '_form" form_name="'+e_name+'"]';
					smenu.push({text : e_name,  onclick : function(scode) {
						return function (){
							editor.insertContent(scode);
						}
					}(short_code)});//need closure for specific variable
				}
				menu.push({text : sm_translate['Saved' + type_key + 'Forms'], menu : smenu});
			}
		}
		// API WordPress: création auto des pages génériques - add short codes "all activities" - "Catégory of activities"
		if (sm_shortcodes['activities']) {
			var short_code = sm_shortcodes['activities'];
			menu.push({text : sm_translate['ListCategories'],   onclick : function(scode) {
				return function (){
					editor.insertContent(scode);
				}
			}(short_code)});
		}


		if (sm_shortcodes['categories']) {
			var cat_sc = sm_shortcodes['categories'];
			var cat_re = /default_cat_id="\d+"/;

			var smenu = [];
			var fmenu = [];
			for (var i in sm_categories) {
				var e_label = sm_categories[i]['label'];
				var id_category = sm_categories[i]['id'];
				var short_code = cat_sc.replace(cat_re, 'default_cat_id="' + id_category + '"');
				//'[sm action="category_list" default_cat_id="' + id_category + '" target="/interview/"]';
				smenu.push({text : e_label,  onclick : function(scode) {
							return function (){
								editor.insertContent(scode);
							}
						}(short_code)});//need closure for specific variable
				// menu categorie interview
				var int_str = sm_shortcodes['interview'];
				var int_re = /default_sr_id="\d+"/;
				var smenu_interview=[];
				for (var j in sm_interviews[id_category]) {
					var e_labelInterview = sm_translate['Item']+' '+ sm_interviews[id_category][j]['label'];
					var short_code = int_str.replace(int_re, 'default_sr_id="' + sm_interviews[id_category][j]['id'] + '"');
						//str.replace(re, 'default_sr_id="' + sm_interviews[id_category][j]['id'] + '"');
					smenu_interview.push({text : e_labelInterview,  onclick : function(scode) {
						return function (){
							editor.insertContent(scode);
						}
					}(short_code)});//need closure for specific variable
				}
				fmenu.push({text : e_label, menu : smenu_interview});
			}
			menu.push({text : sm_translate['ListActivities'], menu : smenu});
			console.log(fmenu);
			var Mmenu = menu.concat(fmenu);
		} else {
			var Mmenu = menu;
		}


		console.log(menu);

		editor.addButton( 'smtinymceplugin', {
			text: '',
			icon: 'sm_tiny_mce_sc_icon',
			image : this.sm_url_path.replace(/js$/, "") + 'img/sm_tinymce_plugin.png',
			type: 'menubutton',

			menu: Mmenu
		});
	});
})();

(function() {
	return;
	tinymce.create('tinymce.plugins.smtinymceplugin', {
		init : function(ed, url) {
			this.sm_url_path = url;
		},

		createControl: function(n, cm) {
			switch (n) {
				case 'smtinymceplugin':
					var c = cm.createSplitButton('mysplitbutton', {
						title : 'ServiceMagic Codes',
						image : this.sm_url_path.replace(/js$/, "") + 'img/sm_tinymce_plugin.png',
						onclick : function() {}//prevents odd type error
					});

					c.onRenderMenu.add(function(c, m) {
						//m.add({title : 'Lists', 'class' : 'mceMenuItemTitle'}).setDisabled(1);

						//m.add({title : 'Root List', onclick : function() {
						//	tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[sm action="home_list"]');
						//}});

						//m.add({title : 'Activity List', onclick : function() {
						//	var cat_id = prompt("Enter a category id");
						//	if (!isNaN(parseFloat(cat_id)) && isFinite(cat_id))
						//		tinyMCE.activeEditor.execCommand('mceInsertContent',false,'[sm action="category_list" category_id="'+cat_id+'"]');
						//}});

						//console.log(sm_embedeable_names)

						//determine if there are any saved items, if not, show none available
						var any = 0;
						for (var i in sm_embedeable_names){
							any += sm_embedeable_names[i].count;
						}

						if (any > 0){
							m.add({title : 'None available'}).setDisabled(1);
						}

						//show the saved ones
						for (var type_key in sm_embedeable_names){
							if (sm_embedeable_names[type_key].length > 0){
								m.add({title : 'Saved ' + type_key + ' Forms', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
								for (var name_key in sm_embedeable_names[type_key]){
									var e_name = sm_embedeable_names[type_key][name_key];
									var short_code = '[sm action="named_' + type_key + '_form" form_name="'+e_name+'"]';
									m.add({title : e_name,  onclick : function(scode) {
										return function (){
											tinyMCE.activeEditor.execCommand('mceInsertContent', false, scode);
									}}(short_code)});//need closure for specific variable
								}
							}
						}

					});

				// Return the new splitbutton instance
				return c;
			}

			return null;
		},

		getInfo : function() {
			return {
				longname : "ServiceMagic EU Short Codes",
				author : 'DRE',
				authorurl : 'http://www.servicemagic.eu',
				infourl : 'http://www.servicemagic.eu',
				version : "0.1"
			};
		}

	});
tinymce.PluginManager.add('smtinymceplugin', tinymce.plugins.smtinymceplugin);
})();