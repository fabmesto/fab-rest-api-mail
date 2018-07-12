<div class="wrap">
  <h1>FAB Rest Api Mail</h1>

  <?php settings_errors(); ?>

  <form method="post" action="options.php">
    <?php settings_fields( 'fabrestapimail-options' );
    do_settings_sections( 'fabrestapimail-options' );
    ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Nei campi seguenti, puoi utilizzare questi mail-tag:</th>
        <td>
          <b><i>[email] [subject] [message]</i></b>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Email</th>
        <td><input type="text" name="mailrestapi_email" value="<?php echo esc_attr( get_option('mailrestapi_email', '') ); ?>" style="width:100%" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Oggetto del messaggio</th>
        <td><input type="text" name="mailrestapi_subject" value="<?php echo esc_attr( get_option('mailrestapi_subject', '') ); ?>" style="width:100%" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">
          Oggetto del messaggio
        </th>
        <td><textarea name="mailrestapi_message" style="width:100%"><?php echo esc_attr( get_option('mailrestapi_message', '') ); ?></textarea></td>
      </tr>
      <tr valign="top">
        <th scope="row">Licenza</th>
        <td><input type="text" name="<?php echo  $this->macaddress_name?>" value="<?php echo esc_attr( get_option($this->macaddress_name) ); ?>" style="width:100%" /></td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>

  <div>
    Codice da comunicare a TELNET:<br />
    <input type="text" value="<?php echo $this->internal_code()?>" style="color:#999; background-color:#ccc; width:100%"/>
  </div>
</div>
