<?php
namespace restapimail;

class mail_controller extends \fab\fab_controller {
  public $name = 'mail';
  public $models_name = array();

  public function rest_save(){
    return array(
      "code" => "error",
      "message" => "not implement!",
    );
  }

  public function rest_read(){
    if(current_user_can('edit_pages')==1){
      $to = get_option( 'mailrestapi_email', '');
      $subject = get_option( 'mailrestapi_subject', '');
      $message = get_option( 'mailrestapi_message', '');

      $postdata = file_get_contents("php://input");
      $_POST = json_decode($postdata, true);
      
      if(isset($_POST['email'])){
        $subject = str_replace('[email]', $_POST['email'], $subject);
        $message = str_replace('[email]', $_POST['email'], $message);
      }
      if(isset($_POST['subject'])){
        $subject = str_replace('[subject]', $_POST['subject'], $subject);
        $message = str_replace('[subject]', $_POST['subject'], $message);
      }
      if(isset($_POST['message'])){
        $subject = str_replace('[message]', $_POST['message'], $subject);
        $message = str_replace('[message]', $_POST['message'], $message);
      }
      wp_mail( $to, $subject, $message );
      return array(
        "code" => "ok",
        "message" => "Messaggio inviato con successo",
        "data" => $_POST,
      );
    }else{
      return array(
        "code" => "error",
        "message" => "Non sei amministratore!",
      );
    }
  }
}
