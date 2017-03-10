<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KeyBase API</title>
    <link rel="shortcut icon" href="http://keybase.rbg.vic.gov.au/favicon.ico">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
    <!--link href="<?=base_url()?>bundles/apiplatform/swagger-ui/css/typography.css" media="screen" rel="stylesheet"-->
    <!--link href="<?=base_url()?>bundles/apiplatform/swagger-ui/css/reset.css" media="screen" rel="stylesheet"-->
    <link href="<?=base_url()?>bundles/apiplatform/swagger-ui/css/screen.css" media="screen" rel="stylesheet">
    <!--link href="<?=base_url()?>bundles/apiplatform/swagger-ui/css/reset.css" media="print" rel="stylesheet">
    <link href="<?=base_url()?>bundles/apiplatform/swagger-ui/css/print.css" media="print" rel="stylesheet"-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?><?=autoVersion('css/keybase.css'); ?>" />
    <style type="text/css">
        #swagger-ui-container {
            min-width: 0;
            max-width: none;
        }
        #swagger-ui-container ul {
            padding-left: 0;
        }
        div#resources_container {
            padding: 0;
        }
        div.footer {
            position: static;
            background-color: white;
        }
    </style>

    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/object-assign-pollyfill.js"></script>
    <!--script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/jquery-1.8.0.min.js"></script-->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/jquery.slideto.min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/jquery.wiggle.min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/jquery.ba-bbq.min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/handlebars-4.0.5.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/lodash.min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/backbone-min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/swagger-ui.min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/highlight.9.1.0.pack.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/jsoneditor.min.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/marked.js"></script>
    <script src="<?=base_url()?>bundles/apiplatform/swagger-ui/lib/swagger-oauth.js"></script>

    <script>
        $(function () {
            window.swaggerUi = new SwaggerUi({
                url: '<?=base_url()?>keybase-api-1.0.yaml',
                dom_id: 'swagger-ui-container',
                supportedSubmitMethods: ['get', 'post', 'put', 'delete'],
                onComplete: function() {
                    $('pre code').each(function(i, e) {
                        hljs.highlightBlock(e)
                    });

                                    },
                onFailure: function() {
                    log('Unable to Load SwaggerUI');
                },
                docExpansion: 'list',
                jsonEditor: false,
                defaultModelRendering: 'schema',
                showRequestHeaders: true
            });

            window.swaggerUi.load();

            function log() {
                if ('console' in window) {
                    console.log.apply(console, arguments);
                }
            }
        });
    </script>
</head>

<body class="swagger-section keybase">
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
            <div id="login" class="login">
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
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
                <div id="swagger-ui-container" class="swagger-ui-wrap"></div>
            </div>
        </div>
    </div>

</body>
</html>