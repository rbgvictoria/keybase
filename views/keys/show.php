<?php 
    // File: show.php
    // Location: views/keys/show.php
    
    $userid = FALSE; 
    if (isset($this->session->userdata['id']))
        $userid = $this->session->userdata['id'];
    $prusers = array();
    $prmanagers = array();
    foreach ($users as $user) {
        $prusers[] = $user['UsersID'];
        if ($user['Role'] == 'Manager')
            $prmanagers[] = $user['UsersID'];
    }
    
    $qstr = ($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';

    require_once 'views/header.php';
?>

<div class="container">
    <div class="row">
        
        <div class="col-md-12">

            <div id="breadcrumbs">
            <?php if (isset($breadcrumbs) && $breadcrumbs): ?>
                <ol class="breadcrumb">
                <?php foreach (array_reverse($breadcrumbs) as $crumb): ?>
                    <li><?=anchor(site_url() . 'keys/show/' . $crumb['KeysID'] . $qstr, $crumb['Name'])?></li>
                <?php endforeach; ?>
                <li class="active"><?=$keyname?></li>
                </ol>
            <?php endif; ?>
            </div>
        </div> <!-- /.col -->

        <!--div class="col-md-12">
            <?php //require_once 'views/includes/globalfilter.php'; ?>
        </div --> <!-- /.col -->

        <div class="col-md-12">    


            <div id="keypage_header" class="clearfix">
                <?php if($project): ?>
                <div id="projecticon" class="pull-left">
                    <img src="<?=base_url()?>images/projecticons/<?=($project['ProjectIcon']) ? $project['ProjectIcon'] : 'project_icon_default.png'?>" alt=""/>
                </div>
                <?php endif; ?>
                <div id="title">
                    <h2>
                        <?php
                            if ($project) {
                                echo '<span class="project">';
                                echo anchor(site_url() . 'projects/show/' . $project['ProjectsID'] . $qstr, $project['Name']) . ':';
                                echo '</span>';
                            }
                        ?>
                        <?=$keyname?> <span id="keyid"><?=$keyid?></span>
                    </h2>

                    <?php if ($citation): ?>
                    <div class="citation"><?=$citation?></div>
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
                                <div id="keybase-bracketed" class="clearfix"></div>
                            </div>

                            <div id="indented" class="tab-pane" rol="tabpanel">
                                <div id="keybase-indented" class="clearfix"></div>
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
