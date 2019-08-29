<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>KeyBase</title>
    <meta charset="UTF-8" />
    <!--base href="<?=base_url()?>" /-->
    <link rel="shortcut icon" href="<?=base_url()?>favicon.ico">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="<?=base_url()?>css/dynatree/skin/ui.dynatree.css" />
    <link rel="stylesheet" href="<?=base_url()?>js/contextMenu/jquery.contextMenu.min.css" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?><?=autoVersion('css/jqueryui.autocomplete.css');?>" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?><?=autoVersion('css/colorbox/colorbox.css');?>" />
    <link rel="stylesheet" type="text/css" href="<?=base_url()?><?=autoVersion('css/keybase.player.css');?>" />
    <?php if (isset($css)): ?>
        <?php foreach ($css as $link): ?>
    <link rel="stylesheet" type="text/css" href="<?=$link?>" />
        <?php endforeach; ?>
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?><?=autoVersion('css/keybase.css'); ?>" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/jspath.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/dynatree/jquery.dynatree.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/contextMenu/jquery.contextMenu.min.js"></script>
    <script type="text/javascript" src="https://data.rbg.vic.gov.au/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/colorbox/jquery.colorbox.js'); ?>"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/colorbox/jquery.keybase.colorbox.js'); ?>"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/jquery.keybase.key.js'); ?>"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/jquery.keybase.project.js'); ?>"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoVersion('js/jquery.keybase.js'); ?>"></script>
    <script type="text/javascript" src="<?=base_url()?><?=autoversion('js/ckeditor_customconfig.js')?>"></script>
    <?php if (isset($js)): ?>
        <?php foreach ($js as $file): ?>
    <script type="text/javascript" src="<?=$file?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
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
            <?php if ($this->session->userdata('id')):?>
            <li><a href="<?=site_url()?>filters">Filters</a></li>
            <?php endif; ?>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Help <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="<?=site_url()?>keybase/st/about">About KeyBase</a></li>
                <li><a href="<?=site_url()?>keybase/st/terms-of-use">Terms of use</a></li>
                <li><a href="<?=site_url()?>keybase/st/citation">Citing KeyBase</a></li>
                <li><a href="<?=site_url()?>keybase/st/help">Help</a></li>
                <li><a href="<?=site_url()?>keybase/st/news">News</a></li>
              </ul>
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
            <!-- Place for alert -->
            <div id="login" class="login clearfix">
                <?php if ($this->session->userdata('name')): ?>
                    <?=form_hidden('keybase-user-id', $this->session->userdata('id')); ?>
                    <?=$this->session->userdata['name']?> |
                    <?=anchor(base_url() . 'keybase/st/manage-account', 'Manage account')?> |
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
    
