<?php
setlocale(LC_ALL,'pt_BR');
drupal_add_js('/logic/js/lightbox.js');

function bet188br_preprocess_page(&$vars){
  $path=drupal_get_path_alias();
  /** Remove header and footer for /blogfeed/ **/
  if($path==='blogfeed'){
    $vars['theme_hook_suggestions'][]='page--blogfeed';
  }
  /** Remove header and footer for /appblog/ **/
  if($path==='appblog'){
    $vars['theme_hook_suggestions'][]='page__appblog';
  }
  
  /* Remove header and footer for /apostas/ */
  $node=$vars['node'];
  $alias=drupal_get_path_alias();
  if(isset($node) && $node->type==='blog_article'){
    $tags=$node->field_tags['und'][0];
    foreach($tags as $tag_id){
      $tag=taxonomy_term_load($tag_id);
      if(isset($tag) && $tag->name==='apostas'){
        $vars['theme_hook_suggestions'][]='page__apostas';
        break;
      }
    }
  } else if($alias==='apostas'){
    $vars['theme_hook_suggestions']=array();
  }
  /* Background image setting */
  $bg_image=theme_get_setting('front_page_bg');
  if(!empty($bg_image)){
    $bg_file=file_load($bg_image);
    if($bg_file->status!=1){
      $bg_file->status=1;
      file_save($bg_file);
      file_usage_add($bg_file,'bet188br','theme',1);
    }
    if(!empty($bg_file)){
      $image_url=file_create_url($bg_file->uri);
      drupal_add_css(
        'section.home-top{background-image:url(\''
        . $image_url . '\');}',
        array('type'=>'inline')
      );
    }
  }
}

function bet188br_html_head_alter(&$head_elements){
  /**
   * This function expects the metatag module to set all
   * of the other OG and Twitter values.
   */
  $bg_image=theme_get_setting('front_page_bg');
  if(!empty($bg_image) && drupal_is_front_page()){
    $bg_file=file_load($bg_image);
    if(!empty($bg_file)){
      $image_url=file_create_url($bg_file->uri);
      if(isset($head_elements['metatag_og:image_0'])){
        $head_elements['metatag_og:image_0']['#value']=$image_url;
      }
      
      if(isset($head_elements['metatag_twitter:image_0'])){
        $head_elements['metatag_twitter:image_0']['#value']=$image_url;
      }
    } // End if image file
  } // End if image value

}

?>