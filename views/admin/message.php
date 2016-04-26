<?php require_once('views/header.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2><?=$message; ?></h2>
                <p><?=anchor(base_url() . 'index.php/admin/', 'Log in'); ?></p>
            </div>
        </div>
    </div>
		
<?php require_once('views/footer.php'); ?>