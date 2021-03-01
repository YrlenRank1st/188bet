<?php
require_once('params.php');
require_once(__DIR__ . '/../functions.php');
global $T_MSG;
$T_MSG=array(
  'err_robot'=>'Aconteceu um erro.',
  'err_name_empty'=>'O campo "Nome" está vazio.',
  'err_email_empty'=>'O campo "email" está vazio.',
  'err_email_invalid'=>'O email é inválido.',
  'err_message_empty'=>'Por favor, escreva uma mensagem.',
  'err_unknown'=>'Aconteceu um erro.',
  'success'=>'Mensagem enviada com sucesso'
);

drupal_add_js('https://www.google.com/recaptcha/api.js?hl=pt-BR',array('async'=>true,'defer'=>true));
$RECAPTCHA_APP_ID = '6LfFrD0UAAAAABDcUMxXkS5AdTFqvty7TT86K9fG';
$RECAPTCHA_APP_SECRET='6LfFrD0UAAAAAC6Y3CLpIRxQ-0iXT66yzRqlTFAE';

$error_messages=array();

if(!empty($_POST)){

if(isset($_POST['g-recaptcha-response'])){

  $post_fields=http_build_query(array(
    'secret'=>$RECAPTCHA_APP_SECRET,
    'response'=>$_POST['g-recaptcha-response'],
    'remoteip'=>get_client_ip()
  ));

  $response_code=0;
  
  $result=plain_request('https://www.google.com/recaptcha/api/siteverify',$post_fields,$response_code);
  $json=json_decode($result,true);
  do{
    if(empty($json['success'])){
      $error_messages[]=$T_MSG['err_robot'];
    }
    if(empty(trim($_POST['name']))){
      $error_messages[]=$T_MSG['err_name_empty'];
    }
    if(empty($_POST['email'])){
      $error_messages[]=$T_MSG['err_email_empty'];
    } else if(!( preg_match('/[a-z0-9._\-]+@[a-z0-9.\-]+\.[a-z]{1}[a-z]+/i',trim($_POST['email'])) ) ){
      $error_messages[]=$T_MSG['err_email_invalid'];
    }
    if(empty(trim($_POST['message']))){
      $error_messages[]=$T_MSG['err_message_empty'];
    }
    if(!empty($error_messages)){break;}

    //Send to zendesk
    $ticket_object=array(
      'ticket'=> array(
        'subject'=>'Testing the contact form',
        'comment'=>array(
          'body'=>$_POST['message']
        ),
        'requester'=>array(
          'name'=>$_POST['name'],
          'email'=>$_POST['email']
        )
      )
    );
    $ticket_json=json_encode($ticket_object);
    $result=zd_api_json($ZD_API_URL . 'tickets.json',$ticket_json);
    if(!empty($result)){
      $success=true;
      unset($_POST);
    } else {
      $error_messages[]=$T_MSG['err_unknown'];
    }
    
  } while(0);
  
} // End if recaptcha

} // End if not empty $_POST
?>
<form method="POST" class="white-box main-form">
  <div class="title">
    <h2 class="white-title">Se você possui alguma questão sobre o jogo, descreva abaixo sua dúvida.</h2>
  </div><div class="fields">
<?php if(!empty($error_messages)){ ?>
    <div class="field">
      <ul class="invalid">
<?php   foreach($error_messages as $err){   ?>
        <li><?php echo $err; ?></li>
<?php   }   ?>
      </ul>
    </div>
<?php } ?>
<?php if(isset($success)){  ?>
    <div class="field">
      <p class="success"><?php echo $T_MSG['success']; ?></p>
    </div>
<?php } ?>
    <div class="field"><label for="contact-name"
      >Nome *</label><input id="contact-name" type="text" name="name"
      value="<?php echo ( isset($_POST['name'])? htmlentities($_POST['name']):'' ); ?>"
      required></div>
    <div class="field"><label for="contact-email"
      >E-Mail *</label><input  id="contact-email" type="email" name="email"
      value="<?php echo ( isset($_POST['email'])? htmlentities($_POST['email']):'' ); ?>"
      required></div>
    <div class="field"><label for="contact-subject"
      >Assunto</label><input  id="contact-subject" type="text" name="subject"
      value="<?php echo ( isset($_POST['subject'])? htmlentities($_POST['subject']):'' ); ?>"
      ></div>
    <div class="field"><label for="contact-message"
      >Mensagem</label><textarea  id="contact-message" name="message"
      ><?php echo ( isset($_POST['message'])? htmlentities($_POST['message']):'' ); ?></textarea
      ></div>
    <div class="field">
      <div class="g-recaptcha" data-sitekey="<?php echo $RECAPTCHA_APP_ID; ?>"></div>
    </div>
    <div class="field submit"><button type="submit" class="cta">Enviar Mensagem</button></div>
  </div>
</form>
