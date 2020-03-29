
<!-- RevolveR :: footer -->
<footer class="revolver__footer <?php print $auth_class; ?>">

	<p><?php print $title; ?> | <span><?php print date('Y'); ?></span> | RevolveR CMF</p>

	<?php 

	if( INSTALLED ):

		include('Menu.php'); 

	endif; ?>

</footer>
