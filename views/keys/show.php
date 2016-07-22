<?php 
    // File: show.php
    // Location: views/keys/show.php
    
    $userid = FALSE; 
    if (isset($this->session->userdata['id']))
        $userid = $this->session->userdata['id'];
    $prusers = array();
    $prmanagers = array();
    foreach ($users as $user) {
        $prusers[] = $user->user_id;
        if ($user->role == 'Manager')
            $prmanagers[] = $user->user_id;
    }
    
    $qstr = ($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';

    require_once 'views/header.php';
?>

<div class="container">
    <div class="row">
        
        <div class="col-md-12">

            <div id="breadcrumbs">
            <?php if (isset($key->breadcrumbs) && $key->breadcrumbs): ?>
                <ol class="breadcrumb">
                <?php foreach ($key->breadcrumbs as $crumb): ?>
                    <li><?=anchor(site_url() . 'keys/show/' . $crumb->key_id . $qstr, $crumb->key_name)?></li>
                <?php endforeach; ?>
                <li class="active"><?=$key->key_name?></li>
                </ol>
            <?php endif; ?>
            </div>
        </div> <!-- /.col -->

        <div class="col-md-12">    
            <div id="keypage_header" class="clearfix">
                <?php if($key->project): ?>
                <div id="projecticon" class="pull-left">
                    <?php 
                        $default = base_url() . 'images/projecticons/project_icon_default.png'; 
                        $src = ($key->project->project_icon) ? $key->project->project_icon : '/images/projecticons/project_icon_default.png';
                    ?>
                    <img src="<?=$src?>" alt="" title="<?=$src?>"/>
                </div>
                <?php endif; ?>
                <div id="title">
                    <h2>
                        <?php
                            if ($key->project) {
                                echo '<span class="project">';
                                echo anchor(site_url() . 'projects/show/' . $key->project->project_id . $qstr, $key->project->project_name) . ':';
                                echo '</span>';
                            }
                        ?>
                        <?=$key->key_name?> <span id="keyid"><?=$key->key_id?></span>
                    </h2>

                    <?php if ($key->source->citation): ?>
                    <div class="citation"><?=$key->source->citation?></div>
                    <?php endif; ?>
                </div>
            </div>

        </div> <!-- /.col -->

        <div class="col-md-12">
            <div id="key_tabs">
                <ul class="nav nav-tabs navbar-right" role="tablist">
                    <li role="presentation" class="active"><a href="#player" aria-controls="player" role="tab" data-toggle="tab">Interactive</a></li>
                    <li role="presentation"><a href="#bracketed" aria-controls="bracketed" role="tab" data-toggle="tab">Bracketed</a></li>
                    <li role="presentation"><a href="#indented" aria-controls="indented" role="tab" data-toggle="tab">Indented</a></li>
                    <li role="presentation"><a href="#about" aria-controls="users" role="tab" data-toggle="tab">About</a></li>
                </ul>

                <div class="tab-content clearfix">
                    <div id="player" class="tab-pane active" rol="tabpanel">
                        <div id="keybase-player" class="clearfix"></div>
                    </div>

                    <div id="bracketed" class="tab-pane" rol="tabpanel">
                        <div id="keybase-bracketed" class="clearfix"><i class="fa fa-spinner fa-spin fa-2x keybase-spinner"></i></div>
                    </div>

                    <div id="indented" class="tab-pane" rol="tabpanel">
                        <div id="keybase-indented" class="clearfix"><i class="fa fa-spinner fa-spin fa-2x keybase-spinner"></i></div>
                    </div>

                    <div id="about" class="tab-pane" rol="tabpanel">
                        <?php require_once 'about.php'; ?>
                    </div>

                </div> <!-- /.tab-content -->
            </div> <!-- /#key_tabs -->
        </div> <!-- /.col -->
        <div class="col-md-12">&nbsp;</div>

    </div> <!-- /.row -->
</div> <!-- /.container -->

<?php require_once 'views/footer.php'; ?>
