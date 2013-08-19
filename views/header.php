<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>KeyBase</title>
    <meta charset="UTF-8" />
    <!--base href="<?=base_url()?>" /-->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/jquery-ui-1.10.2.custom.css"/>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/colorbox/colorbox.css"/>
    <?php if (isset($css)): ?>
        <?php foreach ($css as $link): ?>
    <link rel="stylesheet" type="text/css" href="<?=$link?>" />
        <?php endforeach; ?>
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/keybase.css" />
    <!--script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script-->
    <script type="text/javascript" src="<?=base_url()?>js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/jquery.window.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/jquery-ui-1.10.2.custom.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/colorbox/jquery.colorbox.js"></script>
    <script type="text/javascript" src="<?=base_url()?>js/colorbox/jquery.keybase.colorbox.js"></script>
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

<body class="<?=$this->uri->segment(1)?> <?=$this->uri->segment(2)?>">
<div id="container">
    <div id="banner">
	<div id="menu">
            <div>
                <?=anchor(base_url(), 'Home', 'class="menu-item-left"')?><?=
                anchor(base_url() . 'key/st/about', 'About KeyBase', 'class="menu-item"')?><?=
                anchor(base_url() . 'key/st/terms-of-use', 'Terms of Use', 'class="menu-item"')?><?=
                anchor(base_url() . 'key/st/citation', 'Citing KeyBase', 'class="menu-item"')?><?=
                anchor(base_url() . 'key/st/help', 'Help', 'class="menu-item-right"')?><?=
                anchor(base_url() . 'key/st/news', 'News', 'class="menu-item"')?>
            </div>
        </div>
        <div id="logo">
            <a href="<?=base_url()?>"><img src="<?=base_url()?>css/media/logo.png" alt=""/></a>
        </div>
        <div id="banner-right">
            <div id="search">
                <?=form_open('key/search'); ?>
                    <label for="searchbox">Find a key to:</label><input 
                        type="text" name="searchbox" id="searchbox" value=""/><span class="search"><input 
                        type="submit" value=""/></span>
                </form>
            </div>
            <div id="filter">
                <a href="<?=base_url()?>key/filter<?php 
                    if(isset($this->session->userdata['GlobalFilter'])) 
                        echo '/' . $this->session->userdata('GlobalFilter');?>">Filter keys</a>
            </div>
            <div id="login">
                <?php if (isset($this->session->userdata['name'])): ?>
                    <?=$this->session->userdata['name']?> |
                    <?=anchor(base_url() . 'key/st/manage-account', 'Manage account')?> |
                    <?=anchor(base_url() . 'admin/logout', 'Log out')?>
                <?php else: ?>
                    <?=anchor(base_url() . 'admin/login', 'Log in')?> |
                    <?=anchor(base_url() . 'admin/register', 'Register'); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="content">
