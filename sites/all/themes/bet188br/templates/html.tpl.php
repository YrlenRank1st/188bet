<?php
if(!isset($_GET['page']) || $_GET['page']==0){$classes .=' first-page';}
?><!DOCTYPE html >
<html lang="<?php print $language->language; ?>" dir="<?php print $language->dir; ?>">
  <head>
    <?php print $head; ?>
    <title><?php print $head_title; ?></title>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5P57M7F');</script>
<!-- End Google Tag Manager -->
    <!-- Frosmo scripts -->
    <script type="text/javascript" charset="utf-8" src="//d2wzl9lnvjz3bh.cloudfront.net/frosmo.easy.js"></script>
    <script type="text/javascript" charset="utf-8" src="//d2wzl9lnvjz3bh.cloudfront.net/sites/bolao_188bet_net.js"></script>
    <!-- End Frosmo scripts -->
    <!-- June 2018 tracking script -->
<script type="text/javascript">
    (function () {
        window.universal_variable = window.universal_variable || {};
        window.universal_variable.dfp = window.universal_variable.dfp || {};
        window.uolads = window.uolads || [];
    })();
</script>
<script type="text/javascript" src="//tm.jsuol.com.br/uoltm.js?id=ssmy0d" async></script>
    <!-- End June 2018 tracking script -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="google-site-verification" content="E3k9z3dViBWP9Bl5Q9DftZlU8WmTrvu-Kf-zOD8QaGg" />
    <?php print $styles; ?>
  </head>
  <body class="<?php print $classes; ?>" <?php print $attributes;?>>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5P57M7F"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
      <?php print $page_top; ?>
      <?php print $page; ?>
      <?php print $page_bottom; ?>
      <?php print $scripts; ?>
   </body>
</html>
