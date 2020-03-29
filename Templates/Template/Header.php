
<!-- RevolveR :: header -->
<header class="revolver__header <?php print $auth_class; ?>">

	<h1 class="revolver__logo">

		<a title="<?php print DESCRIPTION; ?>" href="<?php print site_host ?>"><?php print $brand; ?></a>

	</h1>

	<?php if( INSTALLED ): ?>

	<div class="revolver__search-box">

		<form action="/search/" method="GET">

			<input type="search" name="query" placeholder="<?php print TRANSLATIONS[ $ipl ]['Type keywords here'] ?>" required />
			<input type="submit" name="revolver-search-submit" value="<?php print TRANSLATIONS[ $ipl ]['Search'] ?>" />

		</form>

	</div>

	<?php endif; ?>

	<?php include('Menu.php'); ?>

</header>
