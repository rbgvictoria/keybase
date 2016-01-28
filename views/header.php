<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>KeyBase</title>
    <meta charset="UTF-8" />
    <!--base href="<?=base_url()?>" /-->
    <link rel="shortcut icon" href="<?=base_url()?>favicon.ico">
    <!--link rel="stylesheet" href="http://openlayers.org/en/v3.3.0/css/ol.css" type="text/css"-->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/jqueryui.autocomplete.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/colorbox/colorbox.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/keybase.player.css" />
    <?php if (isset($css)): ?>
        <?php foreach ($css as $link): ?>
    <link rel="stylesheet" type="text/css" href="<?=$link?>" />
        <?php endforeach; ?>
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/keybase.css?v=1.0" />
    <script type="text/javascript" src="<?=base_url()?>js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/jquery.keybase.key.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/jspath.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/jquery.keybase.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/colorbox/jquery.colorbox.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/colorbox/jquery.keybase.colorbox.js?v=1.0"></script>
    <?php if (isset($js)): ?>
        <?php foreach ($js as $file): ?>
    <script type="text/javascript" src="<?=$file?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php
        if (isset($iehack) && $iehack) require_once('iehack.php');
    ?>
    <?php if (isset($script) && $script): ?>
    <script type="text/javascript">
    <?=$script; ?>
    </script>
    <?php endif; ?>
</head>

<body class="keybase <?=$this->uri->segment(1)?> <?=$this->uri->segment(2)?>">
    <nav class="navbar navbar-default" id="keybase-navigation">
      <div class="container">
          <div class="row">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
              
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="home-link"><a href="<?=site_url()?>"><span class="glyphicon glyphicon-home"></span></a></li>
            <li><a href="<?=site_url()?>projects">Projects</a></li>
            <li><a href="<?=site_url()?>filters">Filters</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Help <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="<?=site_url()?>key/st/about">About KeyBase</a></li>
                <li><a href="<?=site_url()?>key/st/terms-of-use">Terms of use</a></li>
                <li><a href="<?=site_url()?>key/st/citation">Citing KeyBase</a></li>
                <li><a href="<?=site_url()?>key/st/help">Help</a></li>
              </ul>
              <li><a href="<?=site_url()?>key/st/news">News</a></li>
            </li>
          </ul>
          <?=form_open('keys/search', array('class' => 'navbar-form navbar-right')); ?>
            <div class="form-group">
              <?=form_input(array('name' => 'searchbox', 'id' => 'searchbox', 'value' => '', 'class' => 'form-control', 'placeholder' => 'Enter taxon name...')); ?>
            </div>
            <button type="submit" class="btn btn-default" value="Find"><i class="fa fa-search"></i></button>
          <?=form_close(); ?>
        </div><!--/.navbar-collapse -->
          </div><!--/.row -->
      </div><!--/.container -->
    </nav>
    
    <div class="page-header">
        <div class="container">
            <div id="login" class="login">
                <?php if (isset($this->session->userdata['name'])): ?>
                    <?=$this->session->userdata['name']?> |
                    <?=anchor(base_url() . 'key/st/manage-account', 'Manage account')?> |
                    <?=anchor(base_url() . 'admin/logout', 'Log out')?>
                <?php else: ?>
                    <?=anchor(base_url() . 'admin/login', 'Log in')?> |
                    <?=anchor(base_url() . 'admin/register', 'Register'); ?>
                <?php endif; ?>
            </div>
            <div id="logo">
                <a href="<?=base_url()?>"><img src="<?=base_url()?>css/media/keybase-logo-80.png" alt=""/></a>
            </div>
            <div id="name-and-subtitle">
                <div id="site-name">
                    <a href="<?=site_url()?>">KeyBase</a>
                </div>
                <div id="subtitle">Teaching old keys new tricks...</div>
            </div>
        </div> <!-- /.container -->
    </div> 
    
