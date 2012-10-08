<?php require_once('header.php');?>

      <div class="row-fluid">
        <h2>Kemaskini Maklumat Diri</h2>
        
        <br/>
        
        <form method="post">
          <h3>Maklumat Diri</h3>

          <?php shout()?>
          
          IC:</br>
          <input type="text" name="ic" value="<?php echo set_value('ic')?>"/></br></br>
          
          Warna IC:</br>
          <input type="radio" name="ic_color" value="biru" <?php echo set_radio('ic_color', 'biru', TRUE)?>/> Biru &nbsp; 
          <input type="radio" name="ic_color" value="merah" <?php echo set_radio('ic_color', 'merah')?>/> Merah &nbsp; </br></br>

          Nombor Passport:</br><input type="text" name="passport_number" value="<?php echo set_value('passport_number')?>"/></br></br>

          Tarikh Luput Passport:</br><input type="text" name="passport_expiry_date" value="<?php echo set_value('passport_expiry_date')?>"/></br></br>

          Jantina:</br>
          <input type="radio" name="gender" value="biru" <?php echo set_radio('gender', 'lelaki', TRUE)?>/> Lelaki &nbsp; 
          <input type="radio" name="gender" value="merah" <?php echo set_radio('gender', 'perempuan')?>/> Perempuan &nbsp; </br></br>


          Alamat Sekarang:</br><textarea name="current_address"><?php echo set_value('current_address')?></textarea></br></br>

          Nombor Telefon Sekarang (alternatif):</br><input type="text" name="alternate_current_phone_number" value="<?php echo set_value('alternate_current_phone_number')?>"/></br></br>
          

          Alamat Tetap:</br><textarea name="permanent_address"><?php echo set_value('permanent_address')?></textarea></br></br>

          Nombor Telefon Tetap:</br><input type="text" name="alternate_permanent_phone_number" value="<?php echo set_value('alternate_permanent_phone_number')?>"/></br></br>

          Bangsa:</br><input type="text" name="race" value="<?php echo set_value('race')?>"/></br></br>

          Jenis Darah:</br><input type="text" name="blood_type" value="<?php echo set_value('blood_type')?>"/></br></br>

          Nama suami/isteri:</br><input type="text" name="spouse_name" value="<?php echo set_value('spouse_name')?>"/></br></br>
          IC suami/isteri:</br><input type="text" name="spouse_ic" value="<?php echo set_value('spouse_ic')?>"/></br></br>
          Nombor Cukai Pendapatan suami/isteri:</br><input type="text" name="spouse_income_tax_number" value="<?php echo set_value('spouse_income_tax_number')?>"/></br></br>
          Nombor Telefon suami/isteri:</br><input type="text" name="spouse_contact_number" value="<?php echo set_value('spouse_contact_number')?>"/></br></br>
          Pekerjaan suami/isteri:</br><input type="text" name="spouse_job" value="<?php echo set_value('spouse_job')?>"/></br></br>
          Majikan suami/isteri:</br><input type="text" name="spouse_company_name" value="<?php echo set_value('spouse_company_name')?>"/></br></br>
          Alamat Majikan suami/isteri:</br><textarea name="spouse_company_address"><?php echo set_value('spouse_company_address')?></textarea></br></br>

          
          Children: <span class="btn btn-success add_child">tambah +</span><br/>
          <table class='table table-bordered table-striped children'>
            <thead><tr><th>Nama</th><th>Umur</th><th>IC</th></tr></thead>
            <tbody>
              <?php
                if($this->input->post('children_data')){
                  $childs = unserialize($this->input->post('children_data')); 
                  if(!is_null($childs)){
                    foreach($childs['children_name'] as $i=>$name){
                      echo '<tr><td><input type="text" name="children_name[]" value="'.$name.'"/></td><td><input type="text" name="children_age[]" value="'.$childs['children_age'][$i].'"/></td><td><input type="text" name="children_ic[]" value="'.$childs['children_ic'][$i].'"/></td></tr>';
                    }
                  }
                }
              ?>
            </tbody>
          </table>

          <script src="<?php echo base_url()?>assets/js/jquery.js"></script>
          <script type="text/javascript">
            $(document).ready(function(){
              $('.add_child').click(function(){
                var row = '<tr><td><input type="text" name="children_name[]" value=""/></td><td><input type="text" name="children_age[]" value=""/></td><td><input type="text" name="children_ic[]" value=""/></td></tr>';

                $('tbody').append(row);
              });
            });
          </script>

          Nama Waris (selain suami/isteri):</br><input type="text" name="kin_name" value="<?php echo set_value('kin_name')?>"/></br></br>

          Nombor Telefon Waris:</br><input type="text" name="kin_hp" value="<?php echo set_value('kin_hp')?>"/></br></br>
          
          Hubungan Dengan Waris:</br><input type="text" name="kin_relation" value="<?php echo set_value('kin_relation')?>"/></br></br>

          <h2>Maklumat Pekerjaan</h2>

          Cawangan Cukai:</br><input type="text" name="tax_branch" value="<?php echo set_value('tax_branch')?>"/></br></br>

          Nombor Cukai Pendapatan:</br><input type="text" name="income_tax_number" value="<?php echo set_value('income_tax_number')?>"/></br></br>

          Nombor KWSP (EPF):</br><input type="text" name="epf_number" value="<?php echo set_value('epf_number')?>"/></br></br>
          Initial KWSP (EPF):</br><input type="text" name="epf_initial" value="<?php echo set_value('epf_initial')?>"/></br></br>

          Nombor SOCSO :</br><input type="text" name="socso_number" value="<?php echo set_value('socso_number')?>"/></br></br>
          Initial SOCSO :</br><input type="text" name="socso_initial" value="<?php echo set_value('socso_initial')?>"/></br></br>
          Nombor Tabung Haji:</br><input type="text" name="tabung_haji_number" value="<?php echo set_value('tabung_haji_number')?>"/></br></br>
          Nombor Cukai Pendapatan Syarikat:</br><input type="text" name="company_income_tax_number" value="<?php echo set_value('company_income_tax_number')?>"/></br></br>
          Nombor Socso Syarikat:</br><input type="text" name="company_socso_number" value="<?php echo set_value('company_socso_number')?>"/></br></br>
          Nombor Akaun Maybank:</br><input type="text" name="mbb_acc_number" value="<?php echo set_value('mbb_acc_number')?>"/></br></br>
          
          Penyakit (jika ada):</br><input type="text" name="medical_history" value="<?php echo set_value('medical_history')?>"/></br></br>
          
          <h2>Sijil dan Akademik:</h2>

          <h2>SPM:</h2>
          Sekolah:</br><input type="text" name="spm_school" value="<?php echo set_value('spm_school')?>"/></br></br>
          
          <h2>Diploma:</h2>
          Nama Kolej/Universiti:</br><input type="text" name="dip_college" value="<?php echo set_value('dip_college')?>"/></br></br>
          Kursus Pengajian:</br><input type="text" name="dip_major" value="<?php echo set_value('dip_major')?>"/></br></br>

          <h2>Ijazah Sarjana Muda (Degree):</h2>
          Nama Kolej/Universiti:</br><input type="text" name="deg_college" value="<?php echo set_value('deg_college')?>"/></br></br>
          Kursus Pengajian:</br><input type="text" name="deg_major" value="<?php echo set_value('deg_major')?>"/></br></br>

          <h2>Ijazah Sarjana (Master):</h2>
          Nama Kolej/Universiti:</br><input type="text" name="master_college" value="<?php echo set_value('master_college')?>"/></br></br>
          Kursus Pengajian:</br><input type="text" name="master_major" value="<?php echo set_value('master_major')?>"/></br></br>

          <input type="submit" class="btn btn-large btn-primary"/>
        </form>
      </div><!--/row-->

      <style>
        input[type=text], textarea {
          width: 300px;
        }
      </style>



<?php require_once('footer.php')?>
