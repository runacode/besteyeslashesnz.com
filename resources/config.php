<?php

KformConfig::setConfig(array(
    "isWordpress" => false,
    "apiLoginId" => "os_api",
    "apiPassword" => 'p@$$w0rd123123',
 "authString"=>"d91d51871b48dfa2b831f82184c8b583",
	"autoUpdate_allowedIps"=>array("80.248.30.132"),
	"campaignId"=>10,
	"resourceDir"=>"resources/"));




/* 
!---------------------------------IMPORTANT-----------------------------------!

Documentation:
	
	-Full documentation on landing pages can be found at 

Auto-Update Feature:

	-The auto-update feature will automatically update settings on your landing page
	when you make changes to your campaign within the konnektive CRM. Use this feature
	to keep your landing page up-to-date concerning new coupons / shipping options
	and product changes.

	-To use the campaign auto-update feature, the apache or ngix user 
	(depending on your httpd software) must have write access to this file
	
	-If you are not using the auto-update feature, you will need to manually 
	replace this file after making changes to the campaign	
	
!---------------------------------IMPORTANT-----------------------------------!
*/

class KFormConfig
{
	
	public $isWordpress = false;
	public $apiLoginId = '';
	public $apiPassword = '';
	public $resourceDir;
	public $baseDir;
	
	
	public $mobileRedirectUrl;
	public $desktopRedirectUrl;
	
	
	public $continents;
	public $countries;
	public $coupons;
	public $currencySymbol;
	public $insureShipPrice;
	public $landerType;
	public $offers;
	public $upsells;
	public $products;
	public $shipProfiles;
	public $states;
	public $taxes;
	public $termsOfService;
	public $webPages;
	
	static $instance = NULL;
	static $options;
	static $campaignData;
	// class constructor to set the variable values	
	
	static function setConfig($options)
	{
		self::$options = $options;	
	}
	
	public function __construct()
	{
		if(!empty(self::$instance))
			throw new Exception("cannot recreated KFormConfig");
		
		foreach((array) self::$options as $k=>$v)
			$this->$k = $v;
			
		if($this->isWordpress)
		{
			$options = get_option('konnek_options');
			foreach((array)$options as $k=>$v)
				$this->$k = $v;
		
			$data = json_decode(get_option('konnek_campaign_data'));
			foreach($data as $k=>$v)
				$this->$k = $v;
		}
		elseif(!empty(self::$campaignData))
		{
			if(json_decode(self::$campaignData) === NULL)
			{
				echo 'JSON in config.php is broken!';
				die;
			}
			else
				$data = (array)json_decode(self::$campaignData);


			foreach($data as $k=>$v)
				$this->$k = $v;
		}

		self::$instance = $this;
		
	
	}
}

/* 
!---------------------------------IMPORTANT-----------------------------------!

	ABSOLUTELY DO NOT EDIT BELOW THIS LINE
	
!---------------------------------IMPORTANT-----------------------------------!
*/
$requestUri = $_SERVER['REQUEST_URI'];
$baseFile = basename(__FILE__);

if($_SERVER['REQUEST_METHOD']=='POST' && strstr($requestUri,$baseFile))
{
	
	$authString = filter_input(INPUT_POST,'authString',FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
	if(empty($authString))
		die(); //exit silently, don't want people to know that this file processes api requests if they are just sending random posts at it
	
	
	$remoteIp = $_SERVER['REMOTE_ADDR'];
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
		  $remoteIp =  $_SERVER["HTTP_CF_CONNECTING_IP"];
	
	$allowedIps = KFormConfig::$options['autoUpdate_allowedIps'];
	if(!in_array($remoteIp,$allowedIps))
		die("ERROR: Invalid IP Address. Please confirm that the Konnektive IP Address is in the allowedIps array.");
	if($authString != KFormConfig::$options['authString'])
		die("ERROR: Could not authenticate authString. Please re-download code package and replace config file on your server.");

	$data = filter_input(INPUT_POST,'data');
	$data = trim($data);
	$data = utf8_encode($data);
	$decoded = json_decode($data);
	if($decoded != NULL)
	{
		$file = fopen(__FILE__,'r');
		if(empty($file))
			die("ERROR: File not writable");

		$new_file = '';

		while($line = fgets($file))
		{
			$new_file .= $line;

			if(strpos($line,"/*[DYNAMIC-DATA-TOKEN]") === 0)
				break;
		}
		fclose($file);

		$new_file .= "KFormConfig::\$campaignData = '$data';".PHP_EOL;
		$ret = file_put_contents(__FILE__,$new_file);


		if(is_int($ret))
			die("SUCCESS");
		else
			die("ERROR: File not writable");
	}
	else
	{
		die("ERROR: what data");
	}
}

/*[DYNAMIC-DATA-TOKEN] do not remove */

KFormConfig::$campaignData = '{
    "countries": {
        "NZ": "New Zealand"
    },
    "states": {
        "NZ": {
            "AUK": "Auckland",
            "BOP": "Bay of Plenty",
            "CAN": "Canterbury",
            "CIT": "Chatham Islands Territory",
            "GIS": "Gisborne District",
            "HKB": "Hawkes\u0027s Bay",
            "MWT": "Manawatu-Wanganui",
            "MBH": "Marlborough District",
            "NSN": "Nelson City",
            "N": "North Island",
            "NTL": "Northland",
            "OTA": "Otago",
            "S": "South Island",
            "STL": "Southland",
            "TKI": "Taranaki",
            "TAS": "Tasman District",
            "WKO": "Waikato",
            "WGN": "Wellington",
            "WTC": "West Coast"
        }
    },
    "currencySymbol": "NZ$",
    "shipOptions": [],
    "coupons": [],
    "products": [],
    "webPages": {
        "catalogPage": {
            "disableBack": 0,
            "url": "https:\/\/www.besteyeslashesnz.com\/"
        },
        "checkoutPage": {
            "disableBack": 0,
            "url": "https:\/\/www.besteyeslashesnz.com\/checkout.php",
            "autoImportLead": 1,
            "productId": null,
            "requireSig": 0,
            "sigType": 0,
            "cardinalAuth": 0,
            "paayApiKey": null
        },
        "thankyouPage": {
            "disableBack": 0,
            "url": "https:\/\/www.besteyeslashesnz.com\/thankyou.php",
            "createAccountDialog": 0,
            "reorderUrl": null,
            "allowReorder": 0
        },
        "upsellPage1": {
            "disableBack": 1,
            "url": "https:\/\/www.besteyeslashesnz.com\/upsell1.php",
            "createAccountDialog": 0,
            "requirePayInfo": 0,
            "productId": 37,
            "replaceProductId": null
        },
        "upsellPage2": {
            "disableBack": 1,
            "url": "https:\/\/www.besteyeslashesnz.com\/upsell2.php",
            "createAccountDialog": 0,
            "requirePayInfo": 0,
            "productId": 36,
            "replaceProductId": null
        },
        "upsellPage3": {
            "disableBack": 1,
            "url": "https:\/\/www.besteyeslashesnz.com\/upsell3.php",
            "createAccountDialog": 0,
            "requirePayInfo": 0,
            "productId": 38,
            "replaceProductId": null
        },
        "productDetails": {
            "url": "product-details.php"
        }
    },
    "landerType": "CART",
    "googleTrackingId": "UA-156425769-2",
    "enableFraudPlugin": 0,
    "autoTax": 0,
    "taxServiceId": null,
    "companyName": "optin_solutions_llc",
    "offers": {
        "34": {
            "productId": 34,
            "name": "Feg Serum - Eyelash Enhancer",
            "description": "*No description available",
            "imagePath": "https:\/\/www.besteyeslashesnz.com\/resources\/images\/smain-small.jpg",
            "imageId": 1,
            "price": "18.97",
            "shipPrice": "0.00",
            "category": "FEG"
        },
        "35": {
            "productId": 35,
            "name": "Feg Serum - Eyelash Enhancer - Free",
            "description": "*No description available",
            "imagePath": "https:\/\/www.besteyeslashesnz.com\/resources\/images\/smain-small.jpg",
            "imageId": 1,
            "price": "0.00",
            "shipPrice": "0.00",
            "category": "FEG"
        }
    },
    "upsells": {
        "36": {
            "productId": 36,
            "name": "Feg Serum - Eyelash Enhancer - Free Gift",
            "description": "*No description available",
            "imagePath": "https:\/\/www.besteyeslashesnz.com\/resources\/images\/upsell1.jpg",
            "imageId": 1,
            "price": "7.95",
            "shipPrice": "0.00",
            "category": "FEG"
        },
        "37": {
            "productId": 37,
            "name": "FEG - EyeBrown (2pcs - 2 months of treatment)",
            "description": "*No description available",
            "imagePath": "https:\/\/www.besteyeslashesnz.com\/resources\/images\/upsell2.jpg",
            "imageId": 2,
            "price": "14.95",
            "shipPrice": "0.00",
            "category": "FEG"
        },
        "38": {
            "productId": 38,
            "name": "Silicone Make-Up Sponge",
            "description": "*No description available",
            "imagePath": "https:\/\/www.besteyeslashesnz.com\/resources\/images\/upsell3.jpg",
            "imageId": 3,
            "price": "7.95",
            "shipPrice": "0.00",
            "category": "FEG"
        }
    },
    "shipProfiles": [],
    "continents": {
        "NZ": "AU"
    },
    "paypal": {
        "paypalBillerId": 6
    }
}';