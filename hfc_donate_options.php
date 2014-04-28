<?php
// create custom plugin settings menu
add_action('admin_menu', 'hfc_create_menu');

function hfc_create_menu() {

	//create new top-level menu
	add_menu_page('HFC Donation Settings', 'Donations', 'administrator', __FILE__, 'hfc_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'hfc-settings-group', 'donate_paypal_email' );
	register_setting( 'hfc-settings-group', 'organization_name' );
	register_setting( 'hfc-settings-group', 'paypal_url' );
    register_setting( 'hfc-settings-group', 'goal' );
}

function load_donation_history(){
   global $wpdb;
   $results = 
   $wpdb->get_results("SELECT TransactionID, FirstName, LastName, PayerEmail, PaymentAmount, AddressStreet, AddressCity, AddressState, AddressZip, PaymentStatus from wp_payment_notifications", ARRAY_A);
   echo "<table border=1>";
      foreach($results as $id=>$row){
         if($id == 0){
            echo "<tr>";
            foreach($row as $key => $value){
               echo "<td>".$key."</td>";
            }
               echo "</tr>";
         }
         echo "<tr>";
            foreach($row as $key => $value){
               echo "<td>".$value."</td>";
            }
            echo "</tr>";
      }
      echo "</table>";

}
function hfc_settings_page() {
?>
<div class="wrap">
<h2>HFC Donations</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'hfc-settings-group' ); ?>
    <?php do_settings_sections( 'hfc-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Paypal User Email</th>
        <td><input type="text" name="donate_paypal_email" value="<?php echo get_option('donate_paypal_email'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Organization Name</th>
        <td><input type="text" name="organization_name" value="<?php echo get_option('organization_name'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">PayPal API URL</th>
        <td><input type="text" name="paypal_url" value="<?php echo get_option('paypal_url'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Fundraising Goal</th>
        <td><input type="text" name="goal" value="<?php echo get_option('goal'); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
    <?php load_donation_history(); ?>

</form>
</div>
<?php } ?>
