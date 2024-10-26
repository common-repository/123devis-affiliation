<?php
	
	function sm_widgets_init(){
		register_widget( 'sm_widget' );
	}
	
	class sm_widget extends WP_Widget {

		function __construct() {
			/* Widget settings. */
			$widget_ops = array( 'description' => 'Embed Servicemagic forms or lists into the sidebar.' );//'classname' => 'example', 

			/* Widget control settings. */
			//$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'example-widget' );

			/* Create the widget. */
			parent::__construct( 'sm-widget', 'Servicemagic', $widget_ops );
		}
		
		function widget($args, $instance) {
			extract( $args );
			
			//get site sitewide defaults
			$sm_display_defaults = get_option("sm_display_defaults");
			
			new sm_wp_log("Showing category list for category_id \"{$instance['category_id']}\" widget");
			
			//load the api
			$api = sm_api_factory();
			
			//get the list
			try {
				$category_list = $api->sr->category->activities->get(array('category'=>$instance['category_id']));
			} catch(Exception $e){
				return "";
			}
			$category_list->set_parameter("sm_display_defaults", $sm_display_defaults);	
		
			echo $before_widget;
			echo $before_title;
			echo $instance['sm_title'];
			echo $after_title;
			
			echo $category_list->render();
			echo $after_widget;
		}
		
		function form( $instance ) {
			/* Set up some default widget settings. */
			$defaults = array( 'category_id' => "" , 'sm_title' => "What are you building?");
			$instance = wp_parse_args( (array) $instance, $defaults ); 
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'sm_title' ); ?>">Txt Above</label>
				<input id="<?php echo $this->get_field_id( 'sm_title' ); ?>" name="<?php echo $this->get_field_name( 'sm_title' ); ?>" value="<?php echo $instance['sm_title']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'category_id' ); ?>">Category Id</label>
				<input id="<?php echo $this->get_field_id( 'category_id' ); ?>" name="<?php echo $this->get_field_name( 'category_id' ); ?>" value="<?php echo $instance['category_id']; ?>" />
			</p>
			<?php
		}
	}