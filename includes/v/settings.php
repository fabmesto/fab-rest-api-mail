<div class="wrap">
  <h1>FAB Rest Api Mail</h1>

  <?php settings_errors(); ?>

  <form method="post" action="options.php">
    <?php settings_fields('fabrestapimail-options');
    do_settings_sections('fabrestapimail-options');
    ?>
    <h3>REST api</h3>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Nei campi seguenti, puoi utilizzare questi mail-tag:</th>
        <td>
          <b><i>[email] [subject] [message]</i></b>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Email</th>
        <td><input type="text" name="mailrestapi_email" value="<?php echo esc_attr(get_option('mailrestapi_email', '')); ?>" style="width:100%" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Oggetto del messaggio</th>
        <td><input type="text" name="mailrestapi_subject" value="<?php echo esc_attr(get_option('mailrestapi_subject', '')); ?>" style="width:100%" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Testo del messaggio</th>
        <td><textarea name="mailrestapi_message" style="width:100%"><?php echo esc_attr(get_option('mailrestapi_message', '')); ?></textarea></td>
      </tr>
      <tr valign="top">
        <th scope="row">Testo del messaggio da inviare al mittente</th>
        <td><textarea name="mailrestapi_message_from" style="width:100%"><?php echo esc_attr(get_option('mailrestapi_message_from', '')); ?></textarea></td>
      </tr>
    </table>

    <h3>Parametri mittente email WP</h3>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Indirizzo e-mail mittente</th>
        <td><input type="text" name="mailrestapi_mittente_email" value="<?php echo esc_attr(get_option('mailrestapi_mittente_email', '')); ?>" style="width:100%" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Nome mittente</th>
        <td><input type="text" name="mailrestapi_mittente_nome" value="<?php echo esc_attr(get_option('mailrestapi_mittente_nome', '')); ?>" style="width:100%" /></td>
      </tr>
    </table>

    <h3>Email a nuovi iscritti</h3>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">Nei campi seguenti, puoi utilizzare questi mail-tag:</th>
        <td>
          {blogname} {user_login} {user_email} {user_first_name} {user_last_name}
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Oggetto email nuovi iscritti</th>
        <td><input type="text" name="mailrestapi_new_user_notification_email_subject" value="<?php echo esc_attr(get_option('mailrestapi_new_user_notification_email_subject', '')); ?>" style="width:100%" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">HTML del messaggio da inviare al mittente</th>
        <td><?php wp_editor(get_option('mailrestapi_new_user_notification_email_message', ''), 'mailrestapi_new_user_notification_email_message'); ?></td>
      </tr>
    </table>

    <?php submit_button(); ?>
  </form>

</div>