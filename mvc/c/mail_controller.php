<?php

namespace restapimail;

class mail_controller extends \fab\fab_controller
{
  public $name = 'mail';
  public $models_name = array();

  public function rest_save()
  {
    return array(
      "code" => "error",
      "message" => "not implement!",
    );
  }

  public function rest_read()
  {
    if (current_user_can('edit_pages') == 1) {
      $to = get_option('mailrestapi_email', '');
      $subject = get_option('mailrestapi_subject', '');
      $message = get_option('mailrestapi_message', '');
      $message_from = get_option('mailrestapi_message_from', '');

      $postdata = file_get_contents("php://input");
      $_POST = json_decode($postdata, true);

      if (isset($_POST['email'])) {
        $subject = str_replace('[email]', $_POST['email'], $subject);
        $message = str_replace('[email]', $_POST['email'], $message);
        $message_from = str_replace('[email]', $_POST['email'], $message_from);
      }
      if (isset($_POST['subject'])) {
        $subject = str_replace('[subject]', $_POST['subject'], $subject);
        $message = str_replace('[subject]', $_POST['subject'], $message);
        $message_from = str_replace('[subject]', $_POST['subject'], $message_from);
      }
      if (isset($_POST['message'])) {
        $subject = str_replace('[message]', $_POST['message'], $subject);
        $message = str_replace('[message]', $_POST['message'], $message);
        $message_from = str_replace('[message]', $_POST['message'], $message_from);
      }
      if (isset($_POST['id_user'])) {
        $id_user = intval($_POST['id_user']);
      } else {
        $id_user = 0;
      }
      $emails = preg_split('/[;]/', $to);
      foreach ($emails as $to) {
        wp_mail(trim($to), $subject, $message);
      }
      // manda la mail anche alla persona che ha inviato il messaggio

      $sent = \wp_mail(trim($_POST['email']), $subject, $message_from);
      $args_action = array(
        'to' => $_POST['email'],
        'subject' => $subject,
        'message' => $message,
        'id_user' => $id_user,
      );
      if ($sent) {
        do_action('restapimail_sent_message_ok', $args_action);
      } else {
        do_action('restapimail_sent_message_fail', $args_action);
      }
      return array(
        "code" => "ok",
        "message" => "Messaggio inviato con successo",
        "data" => $_POST,
      );
    } else {
      return array(
        "code" => "error",
        "message" => "Non sei amministratore!",
      );
    }
  }
}
