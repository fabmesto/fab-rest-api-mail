<?php

namespace restapimail;

class mail_controller extends \fab\fab_controller
{
  public $name = 'mail';
  public $models_name = array();

  public function rest_save()
  {
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    if ($action !== 'public_support') {
      return array(
        "code" => "error",
        "message" => "Azione non valida!",
        "data" => array("status" => 400),
      );
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return array(
        "code" => "error",
        "message" => "Metodo non consentito!",
        "data" => array("status" => 405),
      );
    }

    $postdata = file_get_contents("php://input");
    $_POST = json_decode($postdata, true);
    if (!is_array($_POST)) {
      $_POST = array();
    }

    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $subject_input = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message_input = isset($_POST['message']) ? wp_strip_all_tags((string) $_POST['message']) : '';
    $captcha_token = isset($_POST['captcha_token']) ? sanitize_text_field($_POST['captcha_token']) : '';

    if (!is_email($email) || empty($subject_input) || empty($message_input) || empty($captcha_token)) {
      return array(
        "code" => "error",
        "message" => "Dati non validi!",
        "data" => array("status" => 400),
      );
    }

    $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : 'unknown';
    $rate_key = 'restapimail_public_support_' . md5(strtolower($email) . '|' . $remote_addr);
    if (get_transient($rate_key)) {
      return array(
        "code" => "error",
        "message" => "Richiesta troppo frequente. Riprova tra un minuto.",
        "data" => array("status" => 429),
      );
    }
    set_transient($rate_key, 1, 60);

    $turnstile_secret = get_option('mailrestapi_turnstile_secret_key', '');
    if (empty($turnstile_secret)) {
      return array(
        "code" => "error",
        "message" => "Captcha non configurato lato server.",
        "data" => array("status" => 500),
      );
    }

    if (!$this->verify_turnstile_token($captcha_token, $remote_addr, $turnstile_secret)) {
      return array(
        "code" => "error",
        "message" => "Verifica captcha non valida.",
        "data" => array("status" => 400),
      );
    }

    $to = get_option('mailrestapi_email', '');
    $subject = get_option('mailrestapi_subject', '');
    $message = get_option('mailrestapi_message', '');
    $message_from = get_option('mailrestapi_message_from', '');

    $subject = str_replace('[email]', $email, $subject);
    $message = str_replace('[email]', $email, $message);
    $message_from = str_replace('[email]', $email, $message_from);

    $subject = str_replace('[subject]', $subject_input, $subject);
    $message = str_replace('[subject]', $subject_input, $message);
    $message_from = str_replace('[subject]', $subject_input, $message_from);

    $subject = str_replace('[message]', $message_input, $subject);
    $message = str_replace('[message]', $message_input, $message);
    $message_from = str_replace('[message]', $message_input, $message_from);

    $emails = preg_split('/[;]/', (string) $to);
    foreach ($emails as $to_email) {
      $to_email = trim($to_email);
      if ($to_email !== '') {
        wp_mail($to_email, $subject, $message);
      }
    }

    $sent = wp_mail($email, $subject, $message_from);
    $args_action = array(
      'to' => $email,
      'subject' => $subject,
      'message' => $message,
      'id_user' => 0,
    );
    if ($sent) {
      do_action('restapimail_sent_message_ok', $args_action);
    } else {
      do_action('restapimail_sent_message_fail', $args_action);
    }

    return array(
      "code" => "ok",
      "message" => "Messaggio inviato con successo",
      "data" => array("sent" => true),
    );
  }

  public function rest_read()
  {
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    if ($action === 'public_support_config') {
      return array(
        "code" => "ok",
        "message" => "Config supporto pubblico",
        "data" => array(
          "turnstile_site_key" => get_option('mailrestapi_turnstile_site_key', ''),
        ),
      );
    }

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

  private function verify_turnstile_token($token, $remote_addr, $secret)
  {
    $response = wp_remote_post(
      'https://challenges.cloudflare.com/turnstile/v0/siteverify',
      array(
        'timeout' => 10,
        'body' => array(
          'secret' => $secret,
          'response' => $token,
          'remoteip' => $remote_addr,
        ),
      )
    );

    if (is_wp_error($response)) {
      return false;
    }

    $status = wp_remote_retrieve_response_code($response);
    if ($status !== 200) {
      return false;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    if (!is_array($body) || !isset($body['success'])) {
      return false;
    }

    return $body['success'] === true;
  }
}
