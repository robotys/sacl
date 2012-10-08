<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title><?php echo $this->config->item('sacl_app_title');?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->
    <link href="<?php echo base_url()?>assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
        background: url(<?php echo base_url()?>assets/img/white_wave.png);
      }
      .sidebar-nav {
        padding: 9px 0;
      }
      td input{
        margin: 0px;
      }

      td{
        background: #fff;
      }

      th{background: #444; color: #fff; border: #444 }

      #datatable td{text-align: right; vertical-align: middle;}
      #datatable td:nth-child(1){text-align: left;} 
      #datatable span.is{background: #ccc; border-radius: 5px; padding: 2px 4px;}
      #datatable span.slp{font-size: 0.9em; font-style: italic}
      #datatable span.state{font-size: 0.8em;}
      #datatable span.cust_name{font-weight: bold;}
      span.flag {font-size: 0.8em; } 
      span.flag span{margin-left: 5px; padding: 0px 3px; color: #fff; float:right; border-radius: 2px;}
      span.reprint{background: #008aff}
      span.low{background: #F05}
      span.slow{background: #FFAE00}
      #datatable .red{background: #F05; color: #fff; border-color: #A00}
    </style>
    <link href="<?php echo base_url()?>assets/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo site_url('main/dashboard')?>"><?php echo $this->config->item('sacl_app_title')?></a>
          <div class="nav-collapse">
            <ul class="nav">
              <li><a href="<?php echo site_url('main/dashboard')?>">Dashboard</a></li>
              
            </ul>

            <?php if($this->m_sacl->hadir()){?>
              <p class="navbar-text pull-right">Logged in as <a><?php echo ucwords($this->session->userdata('username'))?></a> 
                <?php
                  if($this->session->userdata('spoof')){
                    echo ' | <a href="'.site_url('sacl/unspoof/'.$this->session->userdata('spoof')).'">Logout spoof &raquo;</a>';
                  }else{
                    echo '| <a href="'.site_url('sacl/logout').'">Logout &raquo;</a>';
                  }
                ?>

                </p>
            <?php }else{ ?>
              <p class="navbar-text pull-right"><a href="<?php echo site_url('login')?>">Login &raquo;</a></p>
            <?php }?>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <?php //show_sess()?>
