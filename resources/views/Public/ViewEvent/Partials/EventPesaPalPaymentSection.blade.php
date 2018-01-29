<?php include (app_path().'\Pesapal\OAuth.php');
//loading a class that enables the use of environment variables
$dotenv = new Dotenv\Dotenv(base_path());
$dotenv->load();
?>
<section><?php
//pesapal params
$token = $params = NULL;
$consumer_key = env('PESAPAL_CONSUMER_KEY');
$consumer_secret = env('PESAPAL_SECRET_KEY');
$iframelink = env('PESAPAL_IFRAME_URL');
$callback_url = URL::route('showEventPesament',$event_id);

$signature_method = new OAuthSignatureMethod_HMAC_SHA1();

//get form details
$amount = $order_total;
//$amount = number_format($amount, 5);//format amount to 2 decimal places

$desc = 'this is a test description';
$type = 'MERCHANT'; //default value = MERCHANT
$reference = $event_id.$order_started.$expires;//unique order id of the transaction, generated by merchant
$first_name = $order_first_name;
$last_name = $order_last_name;
$email = $order_email;
$currency = 'USD';
$phonenumber = '';//ONE of email or phonenumber is required


$post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\" PhoneNumber=\"".$phonenumber."\" Currency=\"".$currency."\"  xmlns=\"http://www.pesapal.com\" />";

$post_xml = htmlentities($post_xml);

$consumer = new OAuthConsumer($consumer_key, $consumer_secret);

//post transaction to pesapal
$iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
$iframe_src->set_parameter("oauth_callback", $callback_url);
$iframe_src->set_parameter("pesapal_request_data", $post_xml);
$iframe_src->sign_request($signature_method, $consumer, $token);

//display pesapal - iframe and pass iframe_src
?>

<iframe src="<?php echo $iframe_src;?>" width="100%" height="700px"  scrolling="no" frameBorder="0">
	<p>Browser unable to load iFrame</p>
</iframe>
</section>
