<section id='order_form' class="row bg-white" style="margin-top: 5%">
    <div class="container"><br><br>
        <h1 class="section_head">
            Oh, The Unfortunate Thing Has Happened
        </h1>
    </div>

    <div class="container">

         <div class="col-md-12" style="min-height: 500px">
            <?php /*print_r($messages);*/ foreach($messages as $message){
                if (is_array($message)){
                    foreach($message as $key => $value){
                        if(!is_integer($key)){
                            echo '</br><h3>->for <strong>'.$key.'</strong> make sure that '.$value.'</br>';
                        }else{
                            echo '</br><h3>->'.$value.'</h3>';
                        }
                    }
                }else{
                    echo '</br><h3>'.$message.'</h3>';
                }
            } ?>
        </div>


</section>