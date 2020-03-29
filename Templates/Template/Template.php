<?php include('Head.php'); ?>

<?php // Scalable design class

    $main_class = 'revolver__scalable-main';
    $auth_class = $authFlag ? 'revolver__authorized' : 'revolver__not-authorized';

?>

<body>

<main id="RevolverRoot" class="<?php print $main_class; ?>">

<?php 

    include('Header.php');

    include('Main.php');

    include('Footer.php');

?>

</main>

<?="\n\n";

foreach( $scripts as $s ) {

    print $s ."\n"; 

}

?>
