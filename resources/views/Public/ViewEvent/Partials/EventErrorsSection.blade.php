<section id='order_form' class="row bg-white" style="margin-top: 5%">
    <div class="container"><br><br>
        <h2 class="section_head"><b>
            Oh, The Unfortunate Thing Has Happened
        </b></h2>
    </div>

    <div class="container">

         <div class="col-md-12" style="min-height: 500px">
            <?php if(is_array($messages)){
                foreach($messages as $message){
                    if (is_array($message)){
                        foreach($message as $key => $value){
                            if(!is_integer($key)){
                                echo '</br><h4>->for <strong>'.$key.'</strong> make sure that '.$value.'</h4></br>';
                            }else{
                                echo '</br><h4>->'.$value.'</h4>';
                            }
                        }
                    }else{
                        echo '</br><h4>'.$message.'</h4>';
                    }
                }
            }else{
                echo $messages;
            } 
                ?>
        </div>


</section>