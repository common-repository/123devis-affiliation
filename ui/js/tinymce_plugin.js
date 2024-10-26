(function() {
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