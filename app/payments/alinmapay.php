<?php
use Tygh\Registry;
//session_start();
include_once ('alinmapay/alinmapay_common.inc');
//require_once 'C:/xampp/htdocs/cscart/init.php';
if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_REQUEST['payment_id'])) {
    // Retrieve payment method data
    $payment_id =isset($_REQUEST['payment_id']);
    $payment_data = fn_get_payment_method_data($payment_id);

    // Log the full payment data for debugging
   // fn_alinmapay_write_log('Payment Data: ' . json_encode($payment_data)); // Log full data

   
		$expected_cs_cart_version='4.18.3';
    // Check if the payment method is being enabled or updated
    //if (isset($payment_data['status']) && $payment_data['status'] == 'A') {
	//	if (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['A', 'D'])) {
        // Payment method is enabled, log installation details
		error_log('AlinmaPay Payment Method Enabled.');
        $cs_cart_version = Registry::get('config.version');
		$cs_cart_version = defined('PRODUCT_VERSION') ? PRODUCT_VERSION : 'Unknown version';

        $php_version = PHP_VERSION;
        $server_software = $_SERVER['SERVER_SOFTWARE'];

        // Log version and server details
        error_log('Expected CS-Cart Version: ' . $expected_cs_cart_version . ' | Found Version: ' . $cs_cart_version);
        error_log('PHP Version Detected: ' . $php_version);
        error_log('Server Software: ' . $server_software);
    //}
}


/**function fn_alinmapay_write_log($log_message)
{
    $log_file = DIR_ROOT . '/var/log/alinmapay_installation.log'; // Log file location
    $timestamp = date('[Y-m-d H:i:s]');
    
    // Format the log entry
    $log_entry = $timestamp . ' ' . $log_message . PHP_EOL;

    // Ensure the directory exists
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true); // Create the directory if it doesn't exist
    }

    // Write to the log file
    if (file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX) === false) {
        fn_alinmapay_write_log('Failed to write to log file: ' . $log_file); // Log if writing fails
    }
}**/


function getOS() { 
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
    //global $user_agent;

    $os_platform  = "Unknown OS Platform";

    $os_array     = array(
                          '/windows nt 10/i'      =>  'Windows 10',
                          '/windows nt 6.3/i'     =>  'Windows 8.1',
                          '/windows nt 6.2/i'     =>  'Windows 8',
                          '/windows nt 6.1/i'     =>  'Windows 7',
                          '/windows nt 6.0/i'     =>  'Windows Vista',
                          '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                          '/windows nt 5.1/i'     =>  'Windows XP',
                          '/windows xp/i'         =>  'Windows XP',
                          '/windows nt 5.0/i'     =>  'Windows 2000',
                          '/windows me/i'         =>  'Windows ME',
                          '/win98/i'              =>  'Windows 98',
                          '/win95/i'              =>  'Windows 95',
                          '/win16/i'              =>  'Windows 3.11',
                          '/macintosh|mac os x/i' =>  'Mac OS X',
                          '/mac_powerpc/i'        =>  'Mac OS 9',
                          '/linux/i'              =>  'Linux',
                          '/ubuntu/i'             =>  'Ubuntu',
                          '/iphone/i'             =>  'iPhone',
                          '/ipod/i'               =>  'iPod',
                          '/ipad/i'               =>  'iPad',
                          '/android/i'            =>  'Android',
                          '/blackberry/i'         =>  'BlackBerry',
                          '/webos/i'              =>  'Mobile'
                    );

    foreach ($os_array as $regex => $value)
        if (preg_match($regex, $user_agent))
            $os_platform = $value;

    return $os_platform;
}


$userAgent = $_SERVER['HTTP_USER_AGENT']; 
function getDeviceInfo($userAgent) {

	//$userAgent = $_SERVER["HTTP_USER_AGENT"];

    $deviceInfo = array(
        'device' => 'Unknown',
        'platform' => 'Unknown',
        'browser' => 'Unknown',
        'browserVersion' => 'Unknown',
        'macOSVersion' => 'Unknown',
        'macDevice' => 'Unknown'
    );

    // Check if the user agent contains 'Macintosh' and 'Safari'
    if (strpos($userAgent, 'Macintosh') !== false && strpos($userAgent, 'Safari') !== false) {
        $deviceInfo['device'] = 'Mac';
        $deviceInfo['platform'] = 'macOS';

        // Extract the browser and version
        if (preg_match('/Safari\/([\d\.]+)/', $userAgent, $matches)) {
            $browser = 'Safari';
            $browserVersion = $matches[1];
             $deviceInfo['browser'] = $browser.' '.$browserVersion;
            //$devicePlatform = $browser.' '.$browserVersion;
            //echo "Browser Version: " . $devicePlatform . "\n";
        } 
    if (preg_match('/Mac OS X ([\d_]+)/', $userAgent, $matches)) {
        $macOSVersion = str_replace('_', '.', $matches[1]);
        $deviceInfo['macOSVersion'] =$macOSVersion;
    }
    if (preg_match('/\(Macintosh; (.*?)\)/', $userAgent, $matches)) {
            $macDevice = $matches[1];
            $deviceInfo['macDevice'] =$macDevice;
        }  
    }

    return $deviceInfo;
}


function getDeviceType($userAgent) {
    if (strpos($userAgent, 'iPad') !== false) {
        return 'iPad';
    } elseif (strpos($userAgent, 'iPhone') !== false) {
        return 'iPhone';
    } elseif (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
        return 'Mobile';
    } elseif (strpos($userAgent, 'Macintosh') !== false && strpos($userAgent, 'Safari') !== false) {
 		return 'Mac';
    }
    else {
        return 'Desktop';
    }
}

//$deviceType = getDeviceType($userAgent);



function getBrowser() 
{ 
    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version= "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    }
    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    }
    elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Internet Explorer'; 
        $ub = "MSIE"; 
    } 
    elseif(preg_match('/Firefox/i',$u_agent)) 
    { 
        $bname = 'Mozilla Firefox'; 
        $ub = "Firefox"; 
    } 
    elseif(preg_match('/Chrome/i',$u_agent)) 
    { 
        $bname = 'Google Chrome'; 
        $ub = "Chrome"; 
    } 
    elseif(preg_match('/Safari/i',$u_agent)) 
    { 
        $bname = 'Apple Safari'; 
        $ub = "Safari"; 
    } 
    elseif(preg_match('/Opera/i',$u_agent)) 
    { 
        $bname = 'Opera'; 
        $ub = "Opera"; 
    } 
    elseif(preg_match('/Netscape/i',$u_agent)) 
    { 
        $bname = 'Netscape'; 
        $ub = "Netscape"; 
    } 
    
    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
            $version= $matches['version'][0];
        }
        else {
            $version= $matches['version'][1];
        }
    }
    else {
        $version= $matches['version'][0];
    }
    
    // check if we have a number
    if ($version==null || $version=="") {$version="?";}
    
    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}
    
function getIPhoneModelName() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    if (strpos($userAgent, 'iPhone') !== false) {
        $iphoneModels = array(
            '/iPhone\s2G/i' => 'iPhone 2G',
            '/iPhone\s3G/i' => 'iPhone 3G',
            '/iPhone\s3GS/i' => 'iPhone 3GS',
            '/iPhone\s4/i' => 'iPhone 4',
            '/iPhone\s4S/i' => 'iPhone 4S',
            '/iPhone\s5/i' => 'iPhone 5',
            '/iPhone\s5C/i' => 'iPhone 5C',
            '/iPhone\s5S/i' => 'iPhone 5S',
            '/iPhone\s6/i' => 'iPhone 6',
            '/iPhone\s6Plus/i' => 'iPhone 6 Plus',
            '/iPhone\s6S/i' => 'iPhone 6S',
            '/iPhone\s6SPlus/i' => 'iPhone 6S Plus',
            '/iPhone\sSE/i' => 'iPhone SE',
            '/iPhone\s7/i' => 'iPhone 7',
            '/iPhone\s7Plus/i' => 'iPhone 7 Plus',
            '/iPhone\s8/i' => 'iPhone 8',
            '/iPhone\s8Plus/i' => 'iPhone 8 Plus',
            '/iPhone\sX/i' => 'iPhone X',
            '/iPhone\sXS/i' => 'iPhone XS',
            '/iPhone\sXSMax/i' => 'iPhone XS Max',
            '/iPhone\sXR/i' => 'iPhone XR',
            '/iPhone\s11/i' => 'iPhone 11',
            '/iPhone\s11Pro/i' => 'iPhone 11 Pro',
            '/iPhone\s11ProMax/i' => 'iPhone 11 Pro Max',
            '/iPhone\sSE2/i' => 'iPhone SE (2nd generation)',
            '/iPhone\s12Mini/i' => 'iPhone 12 Mini',
            '/iPhone\s12/i' => 'iPhone 12',
            '/iPhone\s12Pro/i' => 'iPhone 12 Pro',
            '/iPhone\s12ProMax/i' => 'iPhone 12 Pro Max',
            '/iPhone\s13Mini/i' => 'iPhone 13 Mini',
            '/iPhone\s13/i' => 'iPhone 13',
            '/iPhone\s13Pro/i' => 'iPhone 13 Pro',
            '/iPhone\s13ProMax/i' => 'iPhone 13 Pro Max',
            '/iPhone/i' => 'iPhone'
        );
        
        foreach ($iphoneModels as $regex => $model) {
            if (preg_match($regex, $userAgent)) {
                return $model;
            }
        }
    }
    
    return 'Not an iPhone';
}



function decryptData($encryptedResponse, $merKey) {
    // Convert the hexadecimal key to binary
    $binaryKey = hex2bin($merKey);

    // Decode the base64 encoded encrypted response
    $decodedData = base64_decode($encryptedResponse);

    // Decrypt the data using AES-256-ECB cipher
    $decryptedData = openssl_decrypt($decodedData, 'AES-256-ECB', $binaryKey, OPENSSL_RAW_DATA);

    // Check if decryption failed
    if ($decryptedData === false) {
        return "Decryption failed";
    }

    return $decryptedData;
}
function isCartEmpty() {
    $cart = &Tygh::$app['session']['cart'];

    // Check if the cart array is empty or not set
    if (empty($cart) || !is_array($cart)) {
        return true; // Cart is empty
    }

    // Check if the cart array contains any items
    foreach ($cart as $item) {
        if (!empty($item)) {
            return false; // Cart is not empty
        }
    }

    return true; // Cart is empty
}





if ( !defined('AREA') ) { die('Access denied'); }

// Return from payment
if (defined('PAYMENT_NOTIFICATION')) {

if ($mode == 'return') {

	
	//echo "in notify ";
	$jsonData = file_get_contents("php://input");

	//echo "Received JSON data: " . $jsonData;die();
	parse_str($jsonData, $parsedData);

	// Remove the 'termId' parameter from the parsed data
	unset($parsedData['termId']);
	
	$dataValue = $parsedData['data'];

	// Decode the extracted data
	$decodedData = urldecode($dataValue);
	$decodedData = str_replace(' ', '+', $decodedData);
	//	print_r($decodedData );die();
	$paymentMethodName = 'alinmapay'; 
	// Step 2: Fetch the payment method ID based on the payment method name
	$paymentMethodId = db_get_field("SELECT processor_id FROM ?:payment_processors WHERE processor = ?s", $paymentMethodName);
	//echo $paymentMethodId ;die();

	// Step 3: Check if the payment method ID is valid

	$processorParams = db_get_field("SELECT processor_params FROM ?:payments WHERE processor_id = ?i", $paymentMethodId);
	//print_r($processorParams);die();
	
	if (!empty($processorParams)) {
    	
    	$settings = unserialize($processorParams);

   		if ($settings !== false) {
    		// Check if the merchant_key exists in the settings array
    		if (isset($settings['merchant_key'])) {
        		$merchantKey = $settings['merchant_key'];
        		//echo "Merchant Key: " . $merchantKey;
    		} else {
        		echo "Merchant Key not found in settings";
    		}
		} else {
   				 echo "Error unserializing data";
		}
} else {
    echo "Processor Params not found for payment method ID: " . $paymentMethodId;die();
}

$encryptedResponse = $decodedData;

$merKey = $merchantKey;
//echo $encryptedResponse;die();
	try {

	$decryptedData = decryptData($encryptedResponse, $merKey);
	error_log('Decrypted Response Body: ' . $decryptedData);
	
	//echo "Decrypted and decoded data: " . $decryptedData;
		} catch (Exception $e) {
    echo "Error: " . $e->getMessage();die();
}

$data = json_decode($decryptedData, true);

// Access the "transactionId" field
$transactionId = $data['transactionId'];
$respamount = $data['amountDetails']['amount'];
$orderid =$data['orderDetails']['orderId'];
$responsehash=$data['signature'];
$responsecode=$data['responseCode'];
//echo $responsehash;die();
$result = $data['result'];

 if (isset($view) === false)
        {
            $view = Registry::get('view');
        }
		$merchant_order_id=$orderid;
     	$alinmapay_payment_id = $transactionId;
	 
	 	$order_info = fn_get_order_info($orderid);
		$ordstatus=db_get_array('select * from ?:orders where order_id="'.$orderid.'"');
		foreach($ordstatus as $st){
			$status=$st['status'];
		}
	
	
	$country=$order_info['s_country'];
	//echo  $country;die();
	if($status=='N'){
		
		if(!empty($merchant_order_id) and !empty($alinmapay_payment_id)){
		
	  
            if (fn_check_payment_script('alinmapay.php', $merchant_order_id, $processor_data)) {
				
        		$currency=$order_info['secondary_currency'];
        		$order_id=$order_info['order_id'];
        		$amount=$order_info['total'];
        		$request_url = $processor_data['processor_params']['request_url'];
    			$host= gethostname();
    			$ip = gethostbyname($host);
				
                $pp_response = array();
                $success = false;
                $error = "";
				try
				{
				
					if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
    					$link = "https"; 
					else
   						 $link = "http"; 

						$link .= "://"; 
						$link .= $_SERVER['HTTP_HOST']; 
						$link .= $_SERVER['REQUEST_URI']; 
 						$input = preg_split( "/(\?|!)/", $link ); 
					
					if($result =='SUCCESS')
					{
						
					   $success = true;
            
	                   $product1=db_get_array('select * from ?:order_details where order_id="'.$orderid.'"');

						foreach($product1 as $p){
							$procode=$p['amount'];
											
	                        $products=db_get_array('select * from ?:products where product_code="'.$p['product_code'].'"');

						foreach($products as $pr){
							$proid=$pr['amount'];
						}
						if($proid>0){
						    $total=$proid-$procode;
				            db_query('update ?:products set amount="'.$total.'" where product_code="'.$p['product_code'].'"');
					}
				}
	
						
				$product=db_get_array('select product_code from ?:order_details where order_id="'.$orderid.'"');

				foreach($product as $p){
				    $procode=$p['product_code'];
				}
										
				$products1=db_get_array('select * from ?:products where product_code="'.$p['product_code'].'"');
						
				foreach($products1 as $pr){
					$proid=$pr['amount'];
				}
					

				db_query('update ?:orders set status="C" where order_id="'.$orderid.'"');

					
				fn_finish_payment($order_id, $pp_response, false);

                if($order_info['user_id']!=0){

                    fn_login_user($order_info['user_id']);

                }
                fn_clear_cart(Tygh::$app['session']['cart']);
                fn_order_placement_routines('route', $order_id);

				$url=$input[0]."?dispatch=checkout.complete&Order_id=".$orderid;
                    
						
				echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
                   

				}
					
					else
					{
						
						if($result =='FAILURE'){
						
						if($responsecode =="624" )
						{
							db_query('update ?:orders set status="I" where order_id="'.$orderid.'"');
						}
						else{
							db_query('update ?:orders set status="F" where order_id="'.$orderid.'"');
						}
		
						//include("css.php");
						$url=$input[0];
 						if($order_info['user_id']!=0){

                            fn_login_user($order_info['user_id']);

                        }

   						$url = $input[0] ."?dispatch=checkout.complete&status=failure&failureMessage=Transaction failed. Please try again.";
					 
						echo "<script type='text/javascript'>document.location.href='{$url}';</script>";
						//fn_alinmapay_write_log('Payment Failure: Order ID: ' . $orderid . ' | Response Code: ' . $responsecode . ' | Failure Message: Transaction failed.');

						$success = false;
					}else
                    {
                	echo "<div style='background-color:red; margin-top: 20%;'><h1>Thank you for shopping with us. This is fraud Transaction. Data is tempered. Please contact with administrator.</h1></div>";die;
                }
				}
				
				}
				catch(Exception $e)
				{
					$success = false;
                    $error ="CSCART_ERROR:Request to alinmapay Failed";
					
				}
				
				
			}
		}
}else
{
	echo "<div style='background-color:red; margin-top: 20%;'><h1>Thank you for shopping with us. This is fraud Transaction. Data is tempered. Please contact with administrator.</h1></div>";die;
}
}
		
		
}
else {
	
	include("responsecode.php");
	$request_url = $processor_data['processor_params']['request_url'];
	$country=$order_info['s_country'];
	$terminal_id = $processor_data['processor_params']['terminal_id'];
	$password = $processor_data['processor_params']['password'];
	$merchant_key = $processor_data['processor_params']['merchant_key'];
	$currency=$order_info['secondary_currency'];
	$host= gethostname();
	$ip = gethostbyname($host);
	$transaction_type = $processor_data['processor_params']['transaction_type']; 
	$userData= $processor_data['processor_params']['metadata'];
	
	$txn_details= "".$order_info['order_id']."|".$terminal_id."|".$password."|".$merchant_key."|".$order_info['total']."|".$currency."";
		$hash=hash('sha256', $txn_details);
	if($request_url=="" && $merchant_key=="" && $terminal_id=="" && $password=="")
{
	echo "<b>Contact administrator for issue related to Configuration !!!!</b>";die;
}
if($request_url=="")
{
	echo "<b>Contact administrator for issue related to request url !!!!</b>";die;
}
	
	$pluginName = 'CScart_Hosted';
	$pluginVersion = '3.0.2';
	// $pluginPlatform = 'Desktop';
	// $deviceModeltest = $_SERVER['HTTP_USER_AGENT'];
	// $deviceModel = substr($deviceModeltest,13,-75);
	// $devicePlatform = getBrowser();
	// $deviceOSVersion = getOS();

		$ua=getBrowser();
		$str=$ua['userAgent'];
	//echo($str);
	$pos1 = strpos($str, '(')+1;
	
	$pos2 = strpos($str, ')')-$pos1;
	$part = substr($str, $pos1, $pos2);
	$parts = explode(" ", $part);
	$devicePlatform= $ua['name'] . " " . $ua['version'];

	

	$deviceType = getDeviceType($userAgent);
	if ($deviceType === 'iPad') {
	    	$version = preg_replace("/(.+)(iPhone|iPad|iPod)(.+)OS[\s|\_](\d+)\_?(\d+)?[\_]?(\d+)?.+/i", "$4.$5", $str);

		//echo($version);
		$deviceOSVersion = $version;
		$deviceModel= "ipad" ;
	} else if ($deviceType === 'iPhone') {
	    $version = preg_match("/OS ((\d+_?){2,3})/i", $str, $matches);
		$iosversion=str_replace("_",".",$matches[1]);
		//print_r($iosversion);
		$deviceOSVersion = $iosversion;
		$deviceModel= $parts[2].' '.$parts[4];
		$iphoneModel =getIPhoneModelName();
		//echo "iPhone Model: " . $iphoneModel;die;

	} else if ($deviceType === 'Mobile') {
	    $deviceOSVersion = $parts[1].' '.$parts[2];
		$deviceModel= $parts[3].' '.$parts[4];

	}elseif($deviceType === 'Mac'){
		$deviceInfo =  getDeviceInfo($userAgent);
		$deviceType === 'Mac';
		$devicePlatform = $deviceInfo['browser'];
		$deviceOSVersion = $deviceInfo['macOSVersion'];
		$deviceModel = $deviceInfo['macDevice'];

	}else{
	 	$deviceType  = 'Desktop';
		$deviceModeltest = $_SERVER['HTTP_USER_AGENT'];
		$deviceModel = substr($deviceModeltest,13,-75);

		$deviceOSVersion = getOS();
	}
	 $cart = Tygh::$app['session']['cart'];

	$_SESSION['custom_payment_data'] = $cart;
	
	//print_r($cart);die();
	error_log('Request URL: ' . $request_url);
	$fields = array(
           
			
			'terminalId' => $terminal_id,
            'password'=> $password,
            'signature' => $hash,
			'paymentType' => $transaction_type,
            'amount' =>$order_info['total'],
            'currency' => $currency,
            'order' => array(
                            'orderId' =>  $order_info['order_id'],
                            'description' => "", 
                            
                            
                        ),
			'customer' => array(
                            'customerEmail'=> $order_info['email'],
                            'billingAddressStreet'=>  "",
                            'billingAddressCity'=>"",
                            'billingAddressState'=>"",
                            'billingAddressPostalCode'=> "",
                            'billingAddressCountry'=> $country
                            
                            
                        ),
			'additionalDetails' => array(
				'userData' => $userData
							
		), "deviceInfo" => json_encode([
              'pluginName' => $pluginName,
              'pluginVersion' => $pluginVersion, 
              'deviceType' => $deviceType,
              'deviceModel' => $deviceModel,
              'deviceOSVersion' => $deviceOSVersion,  
              'clientPlatform' => $devicePlatform,
            ])
		
            );
			
			
			$fields_string = json_encode($fields,JSON_UNESCAPED_SLASHES);

            //print_r($fields_string);die;
		    //fn_alinmapay_write_log('Request Body: ' . $fields_string);
			error_log('Request Body: ' . $fields_string);
		 $ch = curl_init($request_url);
	
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
							'Content-Type: application/json',
							'Content-Length: ' . strlen($fields_string))
						);
							curl_setopt($ch, CURLOPT_TIMEOUT, 5);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

							//execute post
							$result = curl_exec($ch);
							
							error_log('Response Body: ' . $result);
							//print_r($result);die;
							//close connection
							curl_close($ch);
									
									$urldecode=(json_decode($result,true));
									//print_r($urldecode); die;	
									
								
									$responsecode=$urldecode['responseCode'];
									

									if($responsecode=="225")
									{
										echo "<b>Contact administrator for issue related to terminal password !!!!</b>";die;
									}
									else{
								//if($responsecode=='')
										
								if($responsecode=="001")		
									{
                                    						$linkUrl = $urldecode['paymentLink']['linkUrl'];
										
									// $url=$urldecode['targetUrl']."?paymentid=".$urldecode['payid'];
									
									if($urldecode['transactionId'] != NULL)
									{
										$url=$linkUrl.$urldecode['transactionId'];
									//echo $url;die();
										echo '
									<html>
									<form name="myform" method="POST" action="'.$url.'">
									<h1>Transaction is processing......</h1>
									</form>
									<script type="text/javascript">document.myform.submit();
									</script>
									</html>';die;}
									}
									else{
										echo "<b>Something went wrong!!!!</b>  ".$arr[$responsecode]; die;
									}
									}
		
}

?>