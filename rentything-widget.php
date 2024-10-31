<?php /* 
Plugin Name: Rentything Offers Widget
Plugin URI:     
Description: Display a user's offer from Rentything.com
Version: 1.0 
Author: Martin Wong
Author URI: http://www.martywong.com 
License: GPLv2 
*/ 

add_action( 'widgets_init', 'create_widgets' );
function create_widgets() {     register_widget( 'Rentything_Offers' ); }

class Rentything_Offers extends WP_Widget {     // Construction function     
function __construct () {
        parent::__construct( 'rentything_offers', 'Rentything Offers',          
		array( 'description' => 'Displays a user\'s Rentything offers' ) );
    } 
	

function form( $instance ) {     
	// Retrieve previous values from instance  
    // or set default values if not present     
   $render_widget = ( !empty( $instance['render_widget'] ) ? 
														$instance['render_widget'] : 'true' );
    $nb_rentything_offers = ( !empty( $instance['nb_rentything_offers'] ) ? 
														$instance['nb_rentything_offers'] : 5 );
    $rentything_username = ( !empty( $instance['rentything_username'] ) ?  
														esc_attr( $instance['rentything_username'] ) :  
														'Enter a username' );               
	?>
    <!-- Display fields to specify title and item count -->     
	<p>         
		<label for="<?php echo 
					$this->get_field_id( 'render_widget' ); ?>">         
	<?php echo 'Display Widget'; ?> 
	<select        
		id="<?php echo $this->get_field_id 
	    ( 'render_widget' ); ?>" 
		name="<?php echo $this->get_field_name 
        ( 'render_widget' ); ?>">      
		<option value="true"        
			<?php selected( $render_widget, 'true' ); ?>>        
			Yes</option>         
			<option value="false"  
				<?php selected( $render_widget, 'false' ); ?>>  
			No</option>      
	</select>  
		</label>  
	</p>    
	<p>    
		<label for="<?php echo 
			                    $this->get_field_id( 'rentything_username' ); ?>">  
					<?php echo 'Rentything Username:'; ?>                    
					<input type="text"          
						id="<?php echo $this->get_field_id 
						( 'rentything_username' );?>"           
						name="<?php echo $this->get_field_name 
						( 'rentything_username' ); ?>"  
					value="<?php echo $rentything_username; ?>" />     
			</label>     
		</p>     
		<p>         
		<label for="<?php echo       
				$this->get_field_id( 'nb_rentything_offers' ); ?>">       
			<?php echo 'Number of offers to display:'; ?>       
			<input type="text"     
				id="<?php echo $this->get_field_id 
				( 'nb_rentything_offers' ); ?>"       
				name="<?php echo $this->get_field_name 
				( 'nb_rentything_offers' ); ?>"     
				value="<?php echo $nb_rentything_offers; ?>" />          
			</label>   
			</p>
			    <?php 
		}
		
		function update( $new_instance, $old_instance ) {  
		$instance = $old_instance;        
		// Only allow numeric values 
		if ( is_numeric ( $new_instance['nb_rentything_offers'] ) )  
		$instance['nb_rentything_offers'] = intval( $new_instance['nb_rentything_offers'] );    
		else     $instance['nb_rentything_offers'] = $instance['nb_rentything_offers'];     
        $instance['rentything_username'] = strip_tags( $new_instance['rentything_username'] );
		$instance['render_widget'] =  strip_tags( $new_instance['render_widget'] );     
		return $instance; 
		}  
		function widget( $args, $instance ) {    
			 if ( $instance['render_widget'] == 'true' ) {   
				 // Extract members of args array as individual variables  
				 extract( $args );
				// Retrieve widget configuration options      
				$nb_rentything_offers =  ( !empty( $instance['nb_rentything_offers'] ) ?  $instance['nb_rentything_offers'] : 5 );
				$rentything_username = ( !empty( $instance['rentything_username'] ) ? esc_attr( $instance['rentything_username'] ) :                           'Rentything Offers' );
				// Preparation of query string to retrieve Rentything offerss     
				$url = 'https://www.rentything.com/api/wordpress/' . $rentything_username;
				$response = wp_remote_get( $url, array( 'sslverify' => false )  );
				if( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					echo "Something went wrong: $error_message";
				} else {  			
					$offers_query = 0;
						
				// Display widget title     
				echo $before_widget;     
				echo $before_title;	
				echo apply_filters( 'rentything_username', 'Renything Offers from ' . $rentything_username ); 
				echo $after_title; 
				// Check if any posts were returned by query     
					        
					
						// Display posts in unordered list layout   
						echo '<ul>';
						// Cycle through all items retrieved           		  
						$listings = json_decode($response[body],true);
						foreach ($listings as $listinger) {
						if ( $offers_query < $nb_rentything_offers) {
							foreach ($listinger as $key => $value) {    
								switch ($key) {
									case 'title':
										$title = $value;
										break;
									case 'url':
										$url = $value;
										break;
									case 'price':
										$price = $value;
										break;
									case 'freq':
										$freq = $value;
										break;
									case 'pic':
										$pic = $value;
										break;
								}												 
							}						
							echo '<li><img src="' . $pic . '" height="20px" width="20px" /> <a href="https://www.rentything.com/offer/' . $url . '">';    
							echo $title . '</a></li>';
						}
$offers_query++;						
}						
						echo '</ul>';    
					      
				}
				echo $after_widget;     
			} 
		}		
		
}


				

?>