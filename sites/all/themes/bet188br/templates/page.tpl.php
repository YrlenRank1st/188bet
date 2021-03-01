<?php
//$FB_API_KEY='583220985346334'; //Michael's old key
$FB_API_KEY='326360647842957'; //188Bet's API key
?><script type="text/javascript">document.body.setAttribute("class",document.body.getAttribute("class")+" js");</script>
<header class="main-header"><div>
  <div class="live-header">
  <?php echo render($page['live_header']); ?>
  </div><div class="menu-bar">
  <?php if($logo){ ?>
    <div class="logo">
      <a href="/"><img src="<?php echo $logo; ?>"></a>
    </div>
  <?php } ?>
  <div class="dyna-menu">
    <?php echo render($page['header']); ?>
    <div class="login-zone">
      <?php echo render($page['login']); ?>
    </div>
  </div>
  <?php if(user_is_logged_in()){ ?>
  <?php echo render($page['menus']); ?>
  <?php } ?>
  </div>
</div></header>
<main>
<?php if($tabs){ ?>
  <div class="tabs"><?php echo render($tabs); ?></div>
<?php } ?>
<?php echo render($page['content']); ?>
</main>
<footer class="main-footer">
  <div class="blocks">
  <?php echo render($page['footer']); ?>
  </div>
  <div class="bottom-bar">
  <?php echo render($page['bottom']); ?>
  </div>
</footer>
<!-- Facebook JS -->
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
  FB.init({
    appId            : <?php echo $FB_API_KEY; ?>,
    xfbml            : true,
    cookie           : true,
    version          : 'v2.11'
  });
    
};
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

</script>
<!-- End Facebook JS  -->