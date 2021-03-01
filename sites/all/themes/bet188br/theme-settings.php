<?php
function bet188br_form_system_theme_settings_alter(&$form,&$form_state){
  $form['theme_settings']['front_page_bg']=array(
    '#type'=>'managed_file',
    '#title'=>'Front page background image',
    '#upload_location'=> file_default_scheme() . '://188bet/images/',
    '#default_value'=>theme_get_setting('front_page_bg'),
    '#upload_validators'=>array(
      'file_validate_extensions'=>array('gif png jpg jpeg svg')
    )

  );
}
?>