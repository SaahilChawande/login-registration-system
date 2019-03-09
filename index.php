<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php';?>


    <div class="jumbotron">
        <h1 class="text-center"> Home Page </h1>
    </div>

<?php display_message(); ?>

<?php
$sql = "SELECT * FROM users";
$result = query($sql);
confirm($result);
$row = fetch_array($result);
?>

<?php include 'includes/footer.php';?>

