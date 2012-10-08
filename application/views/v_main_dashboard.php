<?php require_once('header.php');?>

      <h2>Dashboard</h2> <br/>
      <div class="row-fluid">

        <?php 
        //show_sess();
        shout();
          $i = 0;
          $span = 6;
          echo '<div class="row-fluid tile">';
        	foreach($dashboard as $feature){
            if($i%$span == 0) echo '</div><div class="row-fluid tile">';
        	  if($feature['id']==0){
              echo '<div class="span2"><a href="'.$feature['site_url'].'" class="'.$feature['status'].'"><img src="'.base_url().'/assets/img/big_icon/'.$feature['icon'].'"/><p><b>'.$feature['title'].'</b><br/> '.$feature['description'].'</p></a></div>';
            }
            else echo '<div class="span2"><a href="'.site_url($feature['site_url']).'" class="'.$feature['status'].'"><img src="'.base_url().'/assets/img/big_icon/'.$feature['icon'].'"/><p><b>'.$feature['title'].'</b><br/> '.$feature['description'].'</p></a></div>';

            
            $i++;
        	}

        ?>
      </div><!--/row-->
      <style>
        .tile{margin-bottom: 25px;}
      	.tile a, .tile a:visited{
      		display:block; 
      		float:left;
      		padding: 5px;
      	}
      	.tile img{
      		float:left;
      	}
      	.tile p{
      		float:left;
      		width: 108px;
      		height: 50px;
      		position: relative;
          margin-left: 6px;
      	}
      	.tile span{
      		background: #444;
      		color: #fff;
      		font-size: 0.8em;
      		padding: 0px 3px;
      		border-radius: 4px;
      		float:right;
      		display:block;
      	}
        a.indev, a.indev:visited{
          background: url(<?php echo base_url()?>assets/img/off.jpg);
          color: #fff;
        }
        a.indev:hover{
          background: #444;
        }
        a.on, a.on:visited{
        }
        a.on:hover{
          background: #fff;
          box-shadow: 0px 5px 2px #ddd;
          margin-bottom: 5px;
          margin-top: -5px;
        }
      	a.off, a.off:visited{
      		background: url(<?php echo base_url()?>assets/img/off.gif);
          color: #fff;
      	}
        a.off:hover{
          background: #444;
        }
      </style>

<?php require_once('footer.php')?>
