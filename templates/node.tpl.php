<?php 
  // echo '<pre>', var_dump(empty($user_picture)), '</pre>';
  // echo '<pre>', var_dump($page), '</pre>';
?>
<<?php if ( !$page ) { 
?>article<?php } else { 
?>div<?php } ?> id="<?php print $node->type; ?>-<?php print $node->nid; ?>" class="<?php print $classes; ?>">

	<div class="inner">
    <?php if ( !$page ) {
      hide($content['field_image']);
    ?>
      <?php if ( $node->field_image ) { ?>
      <a href="<?php print $node_url; ?>" class="post-thumbnail"><img src="<?php print image_style_url('thumbnail', $node->field_image['und'][0]['uri']) ?>" /></a>
      <?php } ?>
      <h2<?php print $title_attributes; ?> class="title"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
    <?php } else { ?>
      <aside class="sidebar sidebar-meta meta wrapper container entry-meta">
        <ul class="inner clearfix">
        <?php if ( $node->field_image ) { ?>
          <li class="post-thumbnail"><img src="<?php print image_style_url('thumbnail', $node->field_image['und'][0]['uri']) ?>"  /></li>
        <?php } ?>
          <li class="author">
          <?php if ( !empty($user_picture) ): ?>
            <span class="avatar user-avatar user-picture"><?php print $user_picture; ?></span>
          <?php endif; ?>
            <?php print $name; ?>
          </li>
        <?php if ($display_submitted): ?>
          <li class="date"><?php print $date; ?></li>
        <?php endif; ?>
        </ul>
      </aside>
    <?php } ?>

  	<div class="content clearfix">
  	  <?php
  	    // We hide the comments and links now so that we can render them later.
        hide($content['comments']);
        hide($content['links']);
        hide($content);
       ?>
  	</div>

    <?php if (!empty($content['links']['terms'])): ?>
      <div class="terms"><?php print render($content['links']['terms']); ?></div>
    <?php endif;?>

    <?php if (!empty($content['links'])): ?>
	    <div class="links"><?php print render($content['links']); ?></div>
	  <?php endif; ?>

	</div> <!-- /node-inner -->
</<?php if ( !$page ) { 
?>article<?php } else { 
?>div<?php } ?>> <!-- /node-->

<?php print render($content['comments']); ?>
