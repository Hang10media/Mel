<?php

//this is a new fuction to handle the calls from the BNV Investor front end
// think of it as middleware.

function lme_bmv_inv_func_props_per_page(){
    // COMP
	// this returns the number of properties per page to be displauyed

    $propertyperpage = 10;

    Return $propertyperpage;


}

function lme_bmv_inv_func_howmany_props(){
	// COMP
    // this returns the total number of properties in the system available
    // to be displayed on the properties listings page
    
    // 7146,7572 are to cat_ids
    // 7146 = BMV - Classifiction = 7661
    // 7572 = LO - Classification = 7662

    include "db_connect.php";
    // changed during testing
    $query = "SELECT id from lme_leads where archived = 'N' and cat_id in ( 7146,7572 )";
    //$query = "SELECT id from lme_leads";
    $result = mysqli_query($thenewconnection,$query);
    $propertycount=mysqli_num_rows($result);  

    
    include "db_close.php";

    Return $propertycount;


}

function lme_bmv_inv_func_howmany_props_for_classification( $classification){
	// COMP
    // this returns the total number of properties in the system available
    // to be displayed on the properties listings page
    // for a specific Classification type
    
    //RETURN CODS
    // -1 : Classification Code not found or incorrect
    //
    
    
    
    $cattofind = -1;
    $propertycount = -1;
    
    if ( $classification == 7662 ){ $cattofind = 7572; }
    if ( $classification == 7661 ){ $cattofind = 7146; }
    
    if ( $cattofind > 0 ){
    
    include "db_connect.php";
    $query = "SELECT id from lme_leads where archived = 'N' and cat_id = ".$cattofind;
    $result = mysqli_query($thenewconnection,$query);
    $propertycount=mysqli_num_rows($result);  

    
    include "db_close.php";
    
	}
    
   

    Return $propertycount;


}

function lme_bmv_inv_func_howmany_props_for_reserved( $investorid){
	// COMP
    // this returns the total number of properties in the system available
    // to be displayed on the properties listings page
    // that the Investors has reserverd
    
    include "db_connect.php";
    $query = "SELECT id from lme_leads where archived = 'N' and cat_id = ".$cattofind;
    $result = mysqli_query($thenewconnection,$query);
    $propertycount=mysqli_num_rows($result);  

    
    include "db_close.php";


    $propertycount = 1;

    Return $propertycount;


}

function lme_bmv_inv_func_property_summary( $pagetoreturn ){
    //this returns the property summary information for a specifc page
    //based on the number of properties per page
    
    
    // based on 10 prop per page
    // page 1 = 1 - 10
    // page 2 = 11 - 20
    // page 3 = 21 - 30

    // Array contains
    // $propid
    // $title
    // $classification
    // $address
    // $cashflow
    // $rio
    // $finderfee
    // $defaultpicurl
    
        
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
	
	// create the OFFSET, based on 10 records per page being returned
	if ( $pagetoreturn > 1 ){ $theoffsetvalue = 10 * $pagetoreturn;}else{ $theoffsetvalue = 0;}
	
	
	$propertyc = array();
    
    include "db_connect.php";
    
     // changed during testing
    //$query = "SELECT id from lme_leads where archived = 'N' and cat_id in ( 7146,7572 )";
    $query = "SELECT sold_status, propvalue, cat_id, id, add1, post_code, property_type, num_bedrooms, would_accept_bmv, purchase_amount from lme_leads where archived = 'N' and cat_id in ( 7146,7572 ) order by id LIMIT 10 OFFSET ".$theoffsetvalue;


	$result = mysqli_query($thenewconnection,$query);
	$num=mysqli_num_rows($result);
	$i = 0;
	$howmany = $num; 
	
	//loop thr each one
	while ($i < $howmany )  
			{
				
				$thecatid=mysqli_result($result,$i,"cat_id");	
				$theid=mysqli_result($result,$i,"id");
				$theadd1=mysqli_result($result,$i,"add1");
				$thepcode=mysqli_result($result,$i,"post_code");
				$thebedrooms=mysqli_result($result,$i,"num_bedrooms");
				$thepropertytype=mysqli_result($result,$i,"property_type");
				$thehowmuchbmv	=mysqli_result($result,$i,"would_accept_bmv");
				$thepropvalue	=mysqli_result($result,$i,"propvalue");
				$thepurchaseamt	=mysqli_result($result,$i,"purchase_amount");
				$thetermtoshow	=mysqli_result($result,$i,"lo_term");
				$therenttoshow	=mysqli_result($result,$i,"ast_amount");
				$thesoldstatus	=mysqli_result($result,$i,"sold_status");
				
			
				If (is_null($thesoldstatus))
					{ 
						$thefulltitle = $thebedrooms.' Bed, '.$thepropertytype.', '.$thepcode;
						}
				else
				   { $thefulltitle = strtoupper($thesoldstatus) . ' - ' .$thebedrooms.' Bed, '.$thepropertytype.', '.$thepcode;}
				
				
				//defaults
				$theclassification = 'For Sale';
				
				// returns : Purchase price / potential CF PCM / Market Value / RIO
				if ( $thecatid == 7146 )
				{
					If (is_null($thesoldstatus))
						{ $theclassification = 'Below Market Value';}
					else
						{ $theclassification = $thesoldstatus.' - Below Market Value';}
						
					$txt1 = 'Purchase Price';
					$txt2 = 'Cashflow PCM';
					$txt3 = 'Return on Investment'; 
					$txt4 = 'Finders Fee';
					
					$value1 = $thepricetoshow;
					$value2 = 0; // Still to calc
					$value3 = 0; // still to calc
					$value4 = $thepurchaseamt;
				
				
				
				}
			    				
				// returns : Term / Rent PCM / CF over Term / Option Purchase Price
				if ( $thecatid == 7572 )
				{
		
					
					If (is_null($thesoldstatus))
						{ $theclassification = 'Lease Option';}
					else
						{ $theclassification = $thesoldstatus.' - Lease Option';}
					
					$txt1 = 'Term';
					$txt2 = 'Rent PCM';
					$txt3 = 'Cashflow over Term'; 
					$txt4 = 'Option Purchase Price';
					
					$value1 = $thetermtoshow;
					$value2 = 0; // Still to calc
					$value3 = 0; // still to calc
					$value4 = $thepurchaseamt;
				
					
				}


				
				if ( $thehowmuchbmv < $thepropvalue) {$thepricetoshow = $thehowmuchbmv;}else{ $thepricetoshow = $thepropvalue; }
			
				
				//Text1/Value1 = Top Left
				//Text2/Value2 = Top right
				//Text3/Value3 = Bottom Left
				//Text4/Value4 = Bottom right
			
				
				//add to array
				$property1 = array(
			       'propid' => $theid, 
			       'text1'=> $txt1, 
			       'value1'=> $value1,
			       'text2'=> $txt2, 
			       'value2'=> $value2,
			       'text3'=> $txt3, 
			       'value3'=> $value3,
			     	'text4'=> $txt4, 
			       'value4'=> $value4,
			       'title' =>  $thefulltitle,
			       'classification' => $theclassification,
			       'address' => $theadd1,    
			       'defaultpicurl' => 'https://propertyleadportal.com/-/wp-content/uploads/2023/07/IMG_3420.jpeg'
			    	);
					
				  $propertyc[] = $property1;
			    						
				$i++;	
			}
	
	
	include "db_close.php";
    
    Return $propertyc;


}

function lme_bmv_inv_func_property_detail( $propid ){
    // this returns details for a specific property
    // brochure page

    // Array contains
    // $propid
    // $title
    // $classification
    // $propertyinformation
	// $propertydescription
	// $location
	// $rental
	// $thevision
	// $costtoconsider
	// $capitalrequiredtogetinvolved
	// $nextsteps
	// $disclaimer
	// $photosemptycurrently    

    $property = array(
      'propid' => 1234,
     'title' => 'Test Property',
      'classification' => 'Residential',
     'propertyinformation' => array(
        'purchase_price' => 260000,
        'finders_fee' => 6000,
        'gross_income_month' => 2100,
        'managed' => 556,
        'self_managed' => 945
        ),
     'propertydescription' => 'Lorem ipsum dolor sit amet, consectetur ',
     'location' => '123 Main St',
      'rental' => 356,
     'thevision' => 'Our vision for this property is ',
     'costtoconsider' => array(
        'mortgage' => 813,
        'bills' => 480,
        'management' => 252
        ),
     'capitalrequiredtogetinvolved' => array(
        'price' => 260000,
        'deposit' => 65000,
        'legals' => 2600,
        'stamp' => 8300,
        'finders' => 6000
        ),
     'nextsteps' => 'If you are interested ',
     'disclaimer' => 'We try our utmost to ensure that all the property specifications we display are as accurate and dependable as possible, however, they do not form part of any contract or offer.<br /><br />This information should not be relied upon as any kind of representation or fact towards any decisions you make about the purchase of any property.<br /><br />Whatever is listed as services for the property or services on offer inside the specification have not been assessed by us. Therefore, we are unable to give any kind of guarantee as to their operating ability or even their efficiency. This is something you will have to check with your own due diligence.<br /><br />Some measurements will be supplied by estate agents or by third parties so you should only take this information as a guideline for any prospective purchase.<br /><br />We maybe in a situation where we are waiting for confirmation from the vendor to some of our questions but will release the answers as soon as we have them.<br /><br />We would like you to take this into account if you are travelling a long distance for viewing.<br /><br />As in most cases any fixtures and fittings, other than those we mention in the description, need to be agreed with the seller directly.<br /><br />Should you have any further questions then please direct them to us. We are here to help.',
	 'gallery_images' => array(
      'photosemptycurrently1' => 'https://example.com/property10.jpg',
      'photosemptycurrently2' => 'https://example.com/property10.jpg',
      'photosemptycurrently3' => 'https://example.com/property10.jpg',
      'photosemptycurrently4' => 'https://example.com/property10.jpg',
      'photosemptycurrently5' => 'https://example.com/property10.jpg',
      'photosemptycurrently6' => 'https://example.com/property10.jpg',
      'photosemptycurrently7' => 'https://example.com/property10.jpg',
      'photosemptycurrently8' => 'https://example.com/property10.jpg',
      'photosemptycurrently9' => 'https://example.com/property10.jpg',
      'photosemptycurrently10' => ''
	  )
    );

    $propertyc = array();
    $propertyc[] = $property;

    Return $propertyc;


}

function lme_bmv_inv_func_property_summary_for_classification( $pagetoreturn, $classification ){
    //this returns the property summary information for a specifc page
    //based on the number of properties per page for a specific classification type
    // based on 10 prop per page
    // page 1 = 1 - 10
    // page 2 = 11 - 20
    // page 3 = 21 - 30

    // Array contains
    // $propid
    // $title
    // $classification
    // $address
    // $cashflow
    // $rio
    // $finderfee
    // $defaultpicurl
    

    
    // create the second property
    $property2 = array(
        'propid' => 20,
        'title' => 'Property 2',
        'classification' => 'Commercial',
        'address' => '456 Oak St',
        'cashflow' => 8000,
        'rio' => 12,
        'finderfee' => 3,
        'defaultpicurl' => 'https://example.com/property2.jpg'
    );

   
    // property 4
    $property4 = array(
        'propid' => 40,
        'title' => 'Property 4',
        'classification' => 'Commercial',
        'address' => '1011 Oak St',
        'cashflow' => 12000,
        'rio' => 15,
        'finderfee' => 4,
        'defaultpicurl' => 'https://example.com/property4.jpg'
    );

   
    // property 6
    $property6 = array(
        'propid' => 60,
        'title' => 'Property 6',
        'classification' => 'Commercial',
        'address' => '1415 Chestnut St',
        'cashflow' => 9000,
        'rio' => 14,
        'finderfee' => 3,
        'defaultpicurl' => 'https://example.com/property6.jpg'
    );
 
    // add the properties to the main array
    $propertyc = array();
    $propertyc[] = $property2;
    $propertyc[] = $property4;
    $propertyc[] = $property6;

    Return $propertyc;


}

function lme_bmv_inv_func_property_summary_for_reserved( $pagetoreturn, $investorid ){
    //this returns the property summary information for a specifc page
    //based on the number of properties per page that an investor has reserved
    // based on 10 prop per page
    // page 1 = 1 - 10
    // page 2 = 11 - 20
    // page 3 = 21 - 30

    // Array contains
    // $propid
    // $title
    // $classification
    // $address
    // $cashflow
    // $rio
    // $finderfee
    // $defaultpicurl
    

    
    // create the second property
    $property2 = array(
        'propid' => 20,
        'title' => 'Property 2',
        'classification' => 'Commercial',
        'address' => '456 Oak St',
        'cashflow' => 8000,
        'rio' => 12,
        'finderfee' => 3,
        'defaultpicurl' => 'https://example.com/property2.jpg'
    );

    // add the properties to the main array
    $propertyc = array();
    $propertyc[] = $property2;

    Return $propertyc;


}


function lme_bmv_inv_func_login($uid, $pwd){
	// COMP
    // this handles whether the login is correct
    // if good need to write a cookie for 24hrs

    // Return = $bmvid // Good Login
    // Return = -1 // Bad Password
    // Return = -2 // Bad User Name
    //$return = 30058;
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
    
    include "db_connect.php";
    
	//code to just write to audit during testing
	if ( $intesting ){
    $crmnotes = $uid.'/'.$pwd;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	
	$query = "SELECT pwd, name, id  FROM aff_user WHERE uid = '" . $uid . "'";
	
	//code to just write to audit during testing
	if ( $intesting ){
    $crmnotes = $query;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	
	
	$result = mysqli_query($thenewconnection,$query);

	$num=mysqli_num_rows($result);
	if ( $num == 0 )
	
	{
		$return = -2;
		//code to just write to audit during testing
		if ( $intesting ){
	    $crmnotes = 'RC : '.$return;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		}
	}
	else
	{
	
	//code to just write to audit during testing
	if ( $intesting ){
	   $crmnotes = 'found user';
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
		}	
		
		
	$id=mysqli_result($result,0,"id");
	$storedpwd=mysqli_result($result,0,"pwd");
	
	
	if ( $intesting ){
	   $crmnotes = 'found user = '.$id.'/'.$storedpwd.'/'.$pwd;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
		}	
	
	


	if ( $storedpwd == $pwd )
	
		{
	 		$return = $id;
	 		//code to just write to audit during testing
			if ( $intesting ){
		    $crmnotes = 'RC : '.$return;
			$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
			$resulterror = mysqli_query($thenewconnection,$queryerror);
			}
			
		}
		else
		{
		
			$return = -1;	
			//code to just write to audit during testing
			if ( $intesting ){
		    $crmnotes = 'RC : '.$return;
			$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
			$resulterror = mysqli_query($thenewconnection,$queryerror);
			
			
			$crmnotes = 'storedpwd : '.$storedpwd;
			$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
			$resulterror = mysqli_query($thenewconnection,$queryerror);
			
			
			$crmnotes = 'pwd : '.$pwd;
			$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
			$resulterror = mysqli_query($thenewconnection,$queryerror);
			
			
			}
			
		}
	}

    
	//code to just write to audit during testing
	if ( $intesting ){
		$crmnotes = 'FINAL RC : '.$return;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
		include "db_close.php";
	
     Return $return;


}


function lme_bmv_inv_func_reserve($uid, $propid){
	// COMP
    // handles when a Investor Reserves a property

    // Return = 0 // reserved 
    // Return = -1 // Was already reservied, so now removed
    
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');

	include "db_connect.php";    
    
	//check its not reserverd,
	$query = "SELECT id from lme_investor_reserved where lead_id = ".$propid." and investor_id =".$uid;
	$result = mysqli_query($thenewconnection,$query);
	$propertycount=mysqli_num_rows($result);  
	
	if ( $propertycount == 0 ){

		$return = 0;
	
		// if not reserve
		$query = "INSERT INTO `lme_investor_reserved` (`lead_id` ,`investor_id`  ) VALUES ( ".$propid.",".$uid.")";
		$result = mysqli_query($thenewconnection,$query);
		
		if ( $intesting ){
			$crmnotes = 'Insert Investor Reserved : '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		}
	
		$propidenc = (( 1969*$propid)*1969);
		
		// write audit
		$auditwrite = 'Property Reserved : #'.$propidenc;
		
		$query = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` )VALUES (".$uid.", 'PIP', '$arrived_when','$arrived_whentime', '$auditwrite', '1')";
			if ( $localdebug ){echo '<br />'.$query;}
		$result = mysqli_query($thenewconnection,$query);
	
	}
	else
	{
	// if already reserved, then remove record.
	//write autdit
	
		$return = -1;
	
		$query = "DELETE from `lme_investor_reserved` where lead_id = ".$propid." and investor_id =".$uid;
		$result = mysqli_query($thenewconnection,$query);
		
		if ( $intesting ){
			$crmnotes = 'Deleted Investor Reserved : '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		}
	
		$propidenc = (( 1969*$propid)*1969);
		
		// write audit
		$auditwrite = 'Property Reserved REMOVED : #'.$propidenc;
		
		$query = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` )VALUES (".$uid.", 'PIP', '$arrived_when','$arrived_whentime', '$auditwrite', '1')";
			if ( $localdebug ){echo '<br />'.$query;}
		$result = mysqli_query($thenewconnection,$query);
	
	
	}
	
	
    
    
    include "db_close.php";
   
    
    Return $return;


}


function lme_bmv_inv_func_set_details($saveData){
	// COMP
    // updates investor details.


    // return = 0 : Good update
    // Return = -1 : Failure
    
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
	
	extract($saveData);
    
    include "db_connect.php";
    
    $query = "update lme_users set  name='".$fullname."', email='".$email."', mobile='".$phone_number."'   WHERE aff_user_id = ".$uid;
    $result = mysqli_query($thenewconnection,$query);
	
    
    if ( $intesting ){
    	$crmnotes = 'Update User detals : '.$uid.'/'.$fullname.'/'.$phone_number.'/'.$email;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	
	// need to look at array for locations and classificaitons and work out the update.
	// Stores locations	
	
	$query1 = "delete from lme_investor_region_criteria where investor_id = ".$uid;
	$result1 = mysqli_query($thenewconnection,$query1);
	
	foreach ($loactions as $loc ){
		extract($loc);
		
		$queryerror = "INSERT INTO `lme_investor_region_criteria` (`investor_id` ,`region_id` )VALUES (".$uid.",".$loc.")";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
	}
	
	
	// reset storage
	$query1 = "delete from lme_investor_classification_criteria where investor_id = ".$uid;
	$result1 = mysqli_query($thenewconnection,$query1);
	
	// stores classification type
	foreach ($criteria as $crit ){
		extract($crit);
		
		$queryerror = "INSERT INTO `lme_investor_classification_criteria` (`investor_id` ,`classification_id` )VALUES (".$uid.",".$crit.")";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
	}
		
	
	include "db_close.php";
    
    
    
    $return = 0;

    return $return;


}


function lme_bmv_inv_func_get_details($uid){
	// COMP
    // gets the investor  details 
    
    
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
    
    include "db_connect.php";
    
    $query = "SELECT name, email, mobile from lme_users WHERE aff_user_id = '" . $uid . "'";
	
	//code to just write to audit during testing
	if ( $intesting ){
    $crmnotes = $query;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	$result = mysqli_query($thenewconnection,$query);
	$therename=mysqli_result($result,0,"name");
	$thereemail=mysqli_result($result,0,"email");
	$theremobile=mysqli_result($result,0,"mobile");
	
	
	
	if ( $intesting ){
    $crmnotes = 'name : '.$therename.' / email : '.$thereemail.' / mobile : '.$theremobile;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	
	

	$query = "SELECT pwd  FROM aff_user WHERE uid = '" . $theremobile . "'";
	
	if ( $intesting ){
    $crmnotes = $query;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	$result = mysqli_query($thenewconnection,$query);
	$therepwd=mysqli_result($result,0,"pwd");
	
	if ( $intesting ){
    $crmnotes = 'pwd : '.$therepwd;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	
	include "db_close.php";
    
	
 $investor = array(
        'fullname' => $therename,
        'theiremail' => $thereemail,
        'theirphone' => $theremobile,
        'password' => $therepwd
    );

    
    $returnyc = array();
    $returnyc[] = $investor;

    Return $returnyc;
    


}


function lme_bmv_inv_func_get_criteria($uid){
	// COMP
    // gets user criteria for for Classificaitons
    //load all Classifications
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
	
	$criteriayc = array();
    
    include "db_connect.php";
    
    $query = "SELECT id, description from lme_setup where active = 'Y' and type_id = 295";
	
	//code to just write to audit during testing
	if ( $intesting ){
    $crmnotes = 'loading classifications';
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	$result = mysqli_query($thenewconnection,$query);
	$num=mysqli_num_rows($result);
	$i = 0;
	$howmany = $num; 
	
	   //loop thr each one
	while ($i < $howmany )  
			{
					$theid=mysqli_result($result,$i,"id");
					$thedescription=mysqli_result($result,$i,"description");
					
					 //check table if the Investor has this set
    				// mark TRUE/FALSE
					 $query1 = "SELECT id from lme_investor_classification_criteria where investor_id = ".$uid." and classification_id = ".$theid;
					 
					 $result1 = mysqli_query($thenewconnection,$query1);
					 $num1=mysqli_num_rows($result1);
					 
					 if ( $num1 > 0 ){$classselected = TRUE;}else{$classselected = FALSE;}
					  //add to array
					 $criteria1 = array(
					        'id' => $theid,
					        'name' => $thedescription,
					        'checked' => $classselected
   										 );
   										 
   										 
   					   $criteriayc[] = $criteria1;
					
				
			$i++;	
			}
	

    
    include "db_close.php";
  


    Return $criteriayc;
    


}


function lme_bmv_inv_func_set_criteria($uid, $criteriayc){
    // sets user criteria
    // NOT USED, HANDLED BY MAIN UPDATE FUNC FOR INVESTOR ABOVE
 

    // return = 0 : Good update
    // Return = -1 : Failure
    $return = 0;

    Return $retunr;
    


}



function lme_bmv_inv_func_get_locations($uid){
	// COMP
    // gets locations selected for criteria for investor
    
    //load all regions
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
	
	$locationsyc = array();
    
    include "db_connect.php";
    
    $query = "SELECT id, regions from lme_country_pcode_perc where regions <>'' group by regions order by regions asc";
	
	//code to just write to audit during testing
	if ( $intesting ){
    $crmnotes = 'loading locations';
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	$result = mysqli_query($thenewconnection,$query);
	$num=mysqli_num_rows($result);
	$i = 0;
	$howmany = $num; 
	
	   //loop thr each one
	while ($i < $howmany )  
			{
					$theid=mysqli_result($result,$i,"id");
					$theregion=mysqli_result($result,$i,"regions");
					
					 //check table if the Investor has this set
    				// mark TRUE/FALSE
					 $query1 = "SELECT id from lme_investor_region_criteria where investor_id = ".$uid." and region_id = ".$theid;
					 
					 $result1 = mysqli_query($thenewconnection,$query1);
					 $num1=mysqli_num_rows($result1);
					 
					 if ( $num1 > 0 ){$regionselected = TRUE;}else{$regionselected = FALSE;}
					  //add to array
					 $locations1 = array(
					        'id' => $theid,
					        'name' => $theregion,
					        'checked' => $regionselected
   										 );
   										 
   										 
   					   $locationsyc[] = $locations1;
					
				
			$i++;	
			}
	

    
    include "db_close.php";
  

    Return $locationsyc;
    


}

function lme_bmv_inv_func_set_criteria_dublicate($uid, $locationsyc){
    // sets user criteria
    // SPEAK TO DAREN IF YOU CALL THIS

 

    // return = 0 : Good update
    // Return = -1 : Failure
    $return = -1;

    Return $retunr;
    


}


function lme_bmv_inv_func_create_member($memberyc){
	// COMP
	// create new investor
	
	//Return Code:
	// -1 : User Already Exists
	// 1 : Good Return
	
	
	
	
	$intesting = FALSE;
	$arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
	
	 include "db_connect.php";

	
	if ( $intesting ){
   	 	$crmnotes = 'lme_bmv_inv_func_create_member CALLED';
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
	}
	
	$firstname = $memberyc['firstname'];
	$lastname = $memberyc['lastname'];
	$email = $memberyc['email'];
	$contactnumber = $memberyc['contactnumber'];
	$password = $memberyc['password'];
	$companyname = $memberyc['companyname'];
    
    
	//code to just write to audit during testing
	if ( $intesting ){
	
		    $crmnotes = $firstname.'/'.$lastname.'/'.$email.'/'.$contactnumber.'/'.$password.'/'.$companyname;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	
	
	}
	
	//check for duplicate
	$query = "select uid from aff_user where uid = '".$email."'";
	$result = mysqli_query($thenewconnection,$query);
	$num=mysqli_num_rows($result);
	
	//code to just write to audit during testing
	if ( $intesting ){
	 $crmnotes = '0 - '.$query;
	$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
	$resulterror = mysqli_query($thenewconnection,$queryerror);
	
	
	}
	
	
	
	
	
	
	if ($num > 0 ){
		$return = -1;
		
		//code to just write to audit during testing
		if ( $intesting ){
		
			    $crmnotes = 'TIS A DUPE';
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}
		
		
	}
	else
	{	
		$contactname = $firstname.' '.$lastname;
		
		$query = "INSERT INTO `aff_user` (`name` ,`member_since` ,`uid` ,`pwd` ,`warning` ,`statement` ,`type` ,`forward_page` ,`app_id` ,`capture` ,`linked_appid`)VALUES ( '$contactname', '0000-00-00', '$email', '$password', NULL , NULL , 4 , 'lme_investorredirect.php', '0', '', 500175)";
		$result = mysqli_query($thenewconnection,$query);
		$aff_user_id = mysqli_insert_id($thenewconnection);
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '1 - '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}
		
			
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '2 - '.$aff_user_id;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}
		
		$query = "INSERT INTO `lme_users` (`new_inv`,`uid` ,`name` ,`admin` ,`email` ,`mobile` ,`aff_user_id` ,`active`)VALUES ('Y', '$email', '$contactname', 'N', '$email', '$contactnumber', ".$aff_user_id.", 'Y')";
		$result = mysqli_query($thenewconnection,$query);
		$new_user_id = mysqli_insert_id($thenewconnection);	
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '3 - '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}
		
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '4 - '.$new_user_id;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
		
		$contact_type_id=6450;
		$query = "INSERT INTO `lme_contacts` (`autoalloconstop`,`contact_type_id` ,`company_name` ,`contact_name` ,`telephone` ,`email` ,`active`,`user_id`)VALUES ('Y',
		".$contact_type_id.", '$companyname', '$contactname', '$contactnumber',  '$email',  'Y', ".$aff_user_id.")";
		$result = mysqli_query($thenewconnection,$query);
		$newsuppid = mysqli_insert_id($thenewconnection);
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '5 - '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '6 - '.$newsuppid;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
		
		
		
		$newsuppidenc = ( $newsuppid*1969)*1969;
		$clientemail = $email;
		
					
		$auditwrite = 'Investor Signed up';  	 
		$query = "INSERT INTO `lme_audit` (`notes` ,`supplier_id` ,`audit_date`,`audit_time` )VALUES ('$auditwrite',".$newsuppid.", '$arrived_when','$arrived_whentime')";
		$result = mysqli_query($thenewconnection,$query);
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '7 - '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
		
		
		$notestext = "New Investor - Check for signing of : NDA / MNCCA";
		$query = "INSERT INTO `lme_admin_inbox` (`row_type` ,`notification_type` ,`lead_id`,`rec_date` ,`notification_details` )VALUES ( 'I',299, ".$newsuppid.", '".$arrived_when."', '".$notestext ."')";
		$result = mysqli_query($thenewconnection,$query);
		
		
		$notestext = "New Investor - Switch on their Commission";
		$query = "INSERT INTO `lme_admin_inbox` (`row_type` ,`notification_type` ,`lead_id`,`rec_date` ,`notification_details` )VALUES ( 'I',299, ".$newsuppid.", '".$arrived_when."', '".$notestext ."')";
		$result = mysqli_query($thenewconnection,$query);
		
		
		$notestext = "New Investor - Set Commission % or Fixed Amount";
		$query = "INSERT INTO `lme_admin_inbox` (`row_type` ,`notification_type` ,`lead_id`,`rec_date` ,`notification_details` )VALUES ( 'I',299, ".$newsuppid.", '".$arrived_when."', '".$notestext ."')";
		$result = mysqli_query($thenewconnection,$query);
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '8 - '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}
		
		$auditwrite = 'Marked ON STOP - Await Money Laundering check'; 	 
		$query = "INSERT INTO `lme_audit` (`notes` ,`supplier_id` ,`audit_date`,`audit_time` )VALUES ('$auditwrite',".$newsuppid.", '$arrived_when','$arrived_whentime')";
		$result = mysqli_query($thenewconnection,$query);
		
		
		$auditwrite = 'Commission % or Fixed Amount NOT SET'; 	 
		$query = "INSERT INTO `lme_audit` (`notes` ,`supplier_id` ,`audit_date`,`audit_time` )VALUES ('$auditwrite',".$newsuppid.", '$arrived_when','$arrived_whentime')";
		$result = mysqli_query($thenewconnection,$query);
		
		
		
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '9 - '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}
		
		
		$auditwrite = '*** HAS NON CIRCUMVENTION DOC BEEN SIGNED - CHECK AUDIT ***';
		$query = "INSERT INTO `lme_audit` (`notes` ,`supplier_id` ,`audit_date`,`audit_time` )VALUES ('$auditwrite',".$newsuppid.", '$arrived_when','$arrived_whentime')";
		$result = mysqli_query($thenewconnection,$query);
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		$crmnotes = '10 - '.$query;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}
		
		//get standard eEureka email footer 16 = plp
		$query = "select email_footer from lme_multi_profile where id =16";
		$result = mysqli_query($thenewconnection,$query);
		$plpfooter=mysqli_result($result,0,"email_footer");
		
		$signlink = "https://propertyleadportal.com/plp/www/admin/lme_doc_sign_process.php?passedid=".$newsuppidenc."&docid=7060";
		
		
		// Send site owner details of visitor
		$to = $clientemail;
		$subject = "Investor Registration";
		
		$messagea = "Dear Investor,<br /><br />";
	
		
		$messagea .= "Thank you for signing up.<br /><br />For you to receive these property details you are required to electronically sign the enclosed document. Once you hae signed this document all property details will be sent out to you with all new notices we receive every day for those you have requested.<br /><br />To sign just <a href='".$signlink."'>CLICK HERE</a><br /><br />";
	
		
		
		$messagea .= "Your username and password are below."."\n\n";
		
		$messagea .= "Username:	".$clientemail."\n";
		$messagea .= "Password:	".$password."\n";
		$messagea .= "Link : https://propertyinvestor.propertyleadportal.com/login/"."\n\n\n\n";
		
		$messagea .= "If we can help in anyway then don’t hesitate to get in touch. "."\n\n";
		$messagea .= $plpfooter;
		
		$yourname = 'Property Lead Portal';
		$youremailaddress = "noreply@propertyleadportal.com";
		
	
		//begin of HTML message
		$message = '<html><body>'.nl2br($messagea).'</body></html>';
		//echo '<br />'.$message;
				
		$headers  = "From: $youremailaddress\r\n";
		$headers .= "Content-type: text/html\r\n"; 
		
		$rc = mail($to, $subject, $message, $headers);
		
	
		
		//email Admin
		$query1 = "select description from lme_setup where type_id = 8";	
		$result1 =mysqli_query($thenewconnection,$query1);
		$plpadminemail =mysqli_result($result1,0,"description");
		$plpadminemail = "daren@hang10media-corporate.com";
		
		
		$vendorfrom = "noreply@propertyleadportal.com";
		
		
		$vendoroutput = 'Click to View<br /><br />https://propertyleadportal.com/plp/www/admin/lme_add_new_contactv2.php?passedid='.$newsuppid;
		$vendorfrom = "noreply@propertyleadportal.com";
		$vendorsubject ="New BMV Investor has signed Up";
		$vendormessage = '<html><body>'.nl2br($vendoroutput).'</body></html>';
		$emailheaders  = "From: $vendorfrom\r\n";
		$emailheaders .= "Content-type: text/html\r\n"; 
			
			
		$rc = mail($plpadminemail, $vendorsubject, $vendormessage, $emailheaders); 
		
		 $return = 1;

	}
    
    include "db_close.php";

  

    Return $return; 
   
}




function lme_bmv_inv_func_classificationtypes(){
	// COMP
    // gets all Classication types
    
    include "db_connect.php";
    
     $classificationyc = array();
    
    $query = "SELECT id,description  from lme_setup where active = 'Y' and type_id=295 order by description ASC";
	$result = mysqli_query($thenewconnection,$query);
	$num=mysqli_num_rows($result);
	$i = 0;
	$howmany = $num; 
	
	while ($i < $howmany )  
			{
				$description=mysqli_result($result,$i,"description");
				$theid=mysqli_result($result,$i,"id");
				
				$locations1 = array(
					        'id' => $theid,
					        'name' => $description
   										 );
   										 
   										 
   				$classificationyc[] = $locations1;
				
				
				
				
			$i++;	
			}

    
    include "db_close.php";
   

    Return $classificationyc; 
    


}



function lme_get_availabletimeslots($propid, $selecteddate){
	// COMP
    // gets available viewing slots for a specific property for a specific day, that are still available and not booked
    
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
	
	$query1 = "select id from lme_listing_openday where open_day = '$selecteddate' and lead_id = ".$propid;
	$result = mysqli_query($thenewconnection,$query1);
	$num=mysqli_num_rows($result);
	
	if ( $num > 0 ){ $openday = TRUE; }else{ $openday = FALSE; }
	
	if ( $intesting ){
		
	
		$crmnotes = 'lme_get_availabletimeslots -  Property ID : '.$propid;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	
	if ( $intesting ){
		
		$propidcoded = base64_decode($propid);
		
		$crmnotes = 'lme_get_availabletimeslots -  base64_decode : '.$propidcoded;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	
	
	
  	
	
	$query1 = "select viewing_time from lme_listing_viewing_slots where investor_id = 0  and viewing_date = '$selecteddate' and lead_id = ".$propid;
	
	if ( $intesting ){
		
		$crmnotes = 'lme_get_availabletimeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result = mysqli_query($thenewconnection,$query1);
	
	$slots = array();
	
	$num=mysqli_num_rows($result);
	
	
	if ( $intesting ){
		
		$crmnotes = 'lme_get_availabletimeslots - rows found : '.$num;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	
	
	$i = 0;
	$howmany = $num; 
	
	while ($i < $howmany )  
			{
				$viewing_time=mysqli_result($result,$i,"viewing_time");
				
				$viewings = array(
					        'slot' => $viewing_time,
					        'openday' => $openday
   										 );
   										 
   				$slots[] = $viewings;
				
			$i++;	
			}

	
    include "db_close.php";

    Return $slots; 

}


function lme_get_timeslots($propid, $selecteddate){
	//COMP
    // gets all timeslots for a property irrospective of if booked or not
    
    $intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
	
	$query1 = "select id from lme_listing_openday where open_day = '$selecteddate' and lead_id = ".$propid;
	$result = mysqli_query($thenewconnection,$query1);
	$num=mysqli_num_rows($result);
	
	if ( $num > 0 ){ $openday = TRUE; }else{ $openday = FALSE; }
	
  	
	
	$query1 = "select viewing_time from lme_listing_viewing_slots where viewing_date = '$selecteddate' and lead_id = ".$propid;
	
	if ( $intesting ){
		
		$crmnotes = 'lme_get_timeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result = mysqli_query($thenewconnection,$query1);
	
	$slots = array();
	
	$num=mysqli_num_rows($result);
	$i = 0;
	$howmany = $num; 
	
	while ($i < $howmany )  
			{
				$viewing_time=mysqli_result($result,$i,"viewing_time");
				
				$viewings = array(
					        'slot' => $viewing_time,
					        'openday' => $openday
   										 );
   										 
   				$slots[] = $viewings;
				
			$i++;	
			}

	
    include "db_close.php";

    Return $slots; 
    


}


function lme_book_timeslots($propid, $selecteddate, $slottime,$investorid){
	// allows an investor to book a specifci time slot on a specific day for a specific property 
	$intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
 
	
	 $query1 = "update lme_listing_viewing_slots set  investor_id = ".$investorid." where lead_id =  ".$propid." and viewing_date = '$selecteddate' and viewing_time = '$slottime'";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_book_timeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);

    
    include "db_close.php";
    
    Return 1; 
 

}


function lme_create_timeslots($propid, $selecteddate, $slottime){
 	// COMP
	// creates a timeslot used by vendor
 	
 	$intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
  	
	
	$query1 = "delete from lme_listing_viewing_slots where lead_id = ".$propid." and viewing_date = '".$selecteddate."' and viewing_time = '". $slottime."'";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_create_timeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);
	

	
	$query1 = "INSERT INTO `lme_listing_viewing_slots` (`lead_id` ,`viewing_date`,`viewing_time` )VALUES (".$propid.",'$selecteddate','$slottime')";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_create_timeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);

    
    include "db_close.php";
 	
 	
 	
    Return 1; 
 

}


function lme_delete_timeslots($propid, $selecteddate, $slottime){
 	// COMP
	// deleted timeslot used by vendor
 	
 	$intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
  	
	
	$query1 = "delete from lme_listing_viewing_slots where lead_id = ".$propid." and viewing_date = '".$selecteddate."' and viewing_time = '". $slottime."'";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_delete_timeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);

    
    include "db_close.php";
 	

    Return 1; 
 

}


function lme_edit_timeslots($propid, $selecteddate, $slottime){
	// COMP
 	// updates timeslot used by vendor
 	
	$intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
  	
	
	$query1 = "delete from lme_listing_viewing_slots where lead_id = ".$propid." and viewing_date = '".$selecteddate."' and viewing_time = '". $slottime."'";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_edit_timeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);
	

	
	$query1 = "INSERT INTO `lme_listing_viewing_slots` (`lead_id` ,`viewing_date`,`viewing_time` )VALUES (".$propid.",'$selecteddate','$slottime')";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_edit_timeslots - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);

    
    include "db_close.php";
 	
    Return 1; 
 

}

function lme_create_openday($propid, $selecteddate){
	//COMP
 	// creats an open for a specifc property 

 	
 	$intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
  	
	
	$query1 = "delete from lme_listing_openday where lead_id = ".$propid." and open_day = '".$selecteddate."'";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_create_openday - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);
	

	
	$query1 = "INSERT INTO `lme_listing_openday` (`lead_id` ,`viewing_date` )VALUES (".$propid.",'$selecteddate')";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_create_openday - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);

    
    include "db_close.php";
 	
 	
 	
 	
 	
    Return 1; 
 

}

function lme_delete_openday($propid, $selecteddate){
	// COMP
 // deletes an open for a specifc property
 
 	$intesting = FALSE;
    $arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
 	
	include "db_connect.php";
  	
	
	$query1 = "delete from lme_listing_openday where lead_id = ".$propid." and open_day = '".$selecteddate."'";
	
	if ( $intesting ){
		
		$crmnotes = 'lme_delete_openday - '.$query1;
		$queryerror = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` ,`sysmon` )VALUES (-666, 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1','Y')";
		$resulterror = mysqli_query($thenewconnection,$queryerror);
		
		
		}	
	
	$result1 = mysqli_query($thenewconnection,$query1);
	

    
    include "db_close.php";

    Return 1; 
 

}

function lme_get_openday_price($propid){
	// COMP
 	// returns the prices for an open day, to be used when call PayPal
	
	$theprice = 100;

    Return $theprice; 
 

}









function lme_bmv_inv_func_provide_lead($uid, $leadfirstname, $leadlastname, $leadtelco, $leademail){
    // COMP
	// used to create new lead 
    
	// Return code 
	// -2 = The Lead Already existings in System
	// 0 = Good Return
	
	
	$intesting = FALSE;
	$arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
	
	include "db_connect.php";

	//check for duplicate
	$query = "select email from lme_leads where email = '".$leademail."'";
	$result = mysqli_query($thenewconnection,$query);
	$num=mysqli_num_rows($result);
	
	
	if ($num > 0 ){
		$return = -2;
		
		//code to just write to audit during testing
		if ( $intesting ){
		
		
		//write audit for investor
		$auditwrite = 'Investor supplierd DUPLICATE  Lead : '. $leadfirstname.' '. $leadlastname.' - '.$leadtelco.' '.$leademail;
		$query = "INSERT INTO `lme_audit` (`notes` ,`supplier_id` ,`audit_date`,`audit_time` )VALUES ('$auditwrite',".$uid.", '$arrived_when','$arrived_whentime')";
		$result = mysqli_query($thenewconnection,$query);
		
		
		
		}
	}
	else
	{
		$return = 0;
		
		
		$query = "select company_name ,contact_name , telephone , email  from lme_contacts where id = ".$uid;
		$result = mysqli_query($thenewconnection,$query);
		$company_name=mysqli_result($result,0,"company_name");
		$contact_name=mysqli_result($result,0,"contact_name");
		$telephone=mysqli_result($result,0,"telephone");
		$email=mysqli_result($result,0,"email");
	
		$investordetails = $company_name.'/'.$contact_name.'/'.$telephone.'/'.$email;
		
		
		//write audit for investor
		$auditwrite = 'Investor supplierd following Lead : '. $leadfirstname.' '. $leadlastname.' - '.$leadtelco.' '.$leademail;
		$query = "INSERT INTO `lme_audit` (`notes` ,`supplier_id` ,`audit_date`,`audit_time` )VALUES ('$auditwrite',".$uid.", '$arrived_when','$arrived_whentime')";
		$result = mysqli_query($thenewconnection,$query);
		
		$orwf = 706; // default to ask what type they are
		//what contact data do we have telco/email
		if (strlen($leadtelco)  < 5 ){$leadtelco = ''; $orwf = 705; }
		if (strlen($leademail)  < 5 ){$leademail = ''; $orwf = 704;}
		
		
		//insert new lead..
		 $query = "INSERT INTO `lme_leads` (`lead_supplier_id`,`signuptype`,`override_wf`,`arrived_when`,`archived`, `tel_home`, `srce_id`,`firstname`, `lastname` ,`email` )VALUES (".$uid.",'S',".$orwf.",'$arrived_when','N','$leadtelco',241, '$leadfirstname', '$leadlastname','$leademail'  )";
	 	$result = mysqli_query($thenewconnection,$query);
		$aff_user_id = mysqli_insert_id($thenewconnection);
		
		
		// add to audit too
		$crmnotes = "This lead provided by : ".$investordetails;
		$query = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` )VALUES (".$aff_user_id.", 'SYSMON', '$arrived_when','$arrived_whentime', '$crmnotes', '1')";
		$result = mysqli_query($thenewconnection,$query);
		
		
		
	 	$aff_user_idenc = ($aff_user_id * 1969 )*1969;
		
		
	}
	
    
	include "db_close.php";
    Return $return;
    
}



//inital load of sign up
function lme_bmv_inv_func_provide_lead_full($inputs) {
   		// COMP	
	
	  extract($inputs);  
     
     // used to provide store lead details
     
     
                                                                                                                                                                                                                                           
	//$hashvalid,$uid, $fname, $lname, $email, $telephone, $add1, $add2, $add3, $add4,$pcode, $propertyvalue, $outstandingmortgage, $securedloans, 
	//$reasonforsale, $desiredoutcome,$propertytype, $numberbedrooms,$leasefreehold,$yearsremaning,$numberbaths,$numbertoilets,$numberreceptions,
	//$garage,$garden,$yearbuilt,$centralheating,$doubleglazed,$buildstucture,$conditionlounge,$conditionkitchen,$conditionbathroom,
	//$conditionwc,$conditionbedroom1,$conditionbedroom2,$conditionbedroom3,$conditionbedroom4,$conditionoutbuilding,$conditiongarden,
	//$howarrivedat,$mortgagepayment,$typemortgage,$mortgagelender,$mortgagearrears,$facingrepsession,$anydamp,$anysubsidence,
	//$anyfireDamaged,$electricalinstallationcertificate,$energyperformancecertificate,$landlordgassafeycertificate,$propertyvacant,$speaktoconsultant
	
	// INPUT = $hashvalid  				Pass '#89753'
	// INPUT = $uid						Investors ID
	// INPUT = $fname					First Name
	// INPUT = $lname					Last Name
	// INPUT = $email					email address
	// INPUT = $telephone				mobile telephone
	// INPUT = $add1					house number or name
	// INPUT = $add2					Street
	// INPUT = $add3					Town / City
	// INPUT = $add4					County
	// INPUT = $pcode					Post Code
	// INPUT = $propertyvalue			value of property in £
	// INPUT = $outstandingmortgage		total outstanding mortgage in £
	// INPUT = $securedloans			total secured loans in £
	
	// INPUT = $reasonforsale			ID for selected reason for sale, this will populate the <SELECT><OPTION>  "/home/yjcnw125gejm/public_html/darenpledger-com/www/admin/lme_options_reasons.php" and allow you to pass the ID
	
	// INPUT = $desiredoutcome			ID for selected desired outcome, this will populate the <SELECT><OPTION>   "/home/yjcnw125gejm/public_html/darenpledger-com/www/admin/lme_options_desired.php" and allow you to pass the 
	
	
	// INPUT = $propertytype				ID for selected Property type, this will populate the <SELECT><OPTION>  "/home/yjcnw125gejm/public_html/darenpledger-com/www/admin/lme_options_propeytype.php" and allow you to pass the ID

	// INPUT = $numberbedrooms				Number of bedrooms Dropdown 1,2,3,4,5,6,7,8,9,10

  	
  	// Return Code
   	// -1 = Failure to insert Lead Row
  	// -2 = Failure to insert Status Row
  	// -3 = Failure to insert Audit Row
	// -10 = Property Type Not Passed
	// -20 = number of bedrooms or "0" Not Passed
	// -30 = Buyer or Seller Type Not Passed
	// -40 = Telephone Not Pass
	// -50 = Post Code Not Passed
	// -60 = First Name Not Passed
	// -70 = Last Name Not Passed
	// -80 = Email Not Passsed
	// -90 = Address Line 1 Not Passed
	// -100 = Property Value or "0" Not Passed
	// -110 = Outstanding Mortgage or "0" Not Passed
	// -120 = Secured Load or "0" Not Passed
	// -130 = Reason For Sale or "0" Not Passed
	// -140 = Desired Outcome or "0" Not Passed
  	// -666 = Hash Failure
  	// Return new ID inserted
  	          
  	$returnpass = 0;
  	
	$localdebug = FALSE;
	
	//seller type
	$type = "S";
  
  
if ( $hashvalid <> '#89753' ){
	  $returnpass = -666;
	  
}
else
{
	
	
	 include "db_connect.php";
	

	$arrived_when=date('Y-m-d');
	$arrived_whentime=date('H:i:s');
	
	
	 $propertytypename = "";
	if ( $propertytype == "1" ){ $propertytypename = "Studio Flat";}
	if ( $propertytype == "2" ){ $propertytypename = "Apartment";}
	if ( $propertytype == "3" ){ $propertytypename = "Terraced";}
	if ( $propertytype == "4" ){ $propertytypename = "End of terrace";}
	if ( $propertytype == "5" ){ $propertytypename = "Semi-detached";}
	if ( $propertytype == "6" ){ $propertytypename = "Detached";}
	if ( $propertytype == "7" ){ $propertytypename = "Flat";}
	if ( $propertytype == "8" ){ $propertytypename = "Bungalow";}
	
	
	
	

// 6440 default eEureka Cat
// 4 = source for eEureka

// handle buyers/sellers

if ( $type == 'S' ){ $thetypecat = 6092; }
if ( $type == 'B' ){ $thetypecat = 6093; }



	 


//make sure data is clean
$bigtestfail=TRUE;
if (strlen($propertytype) == 0 ){ $returnpass = -10; $bigtestfail=FALSE;}
if (strlen($numberbedrooms) == 0 ){ $returnpass = -20; $bigtestfail=FALSE;}
if (strlen($type) == 0 ){ $returnpass = -30; $bigtestfail=FALSE;}
if (strlen($telephone) == 0 ){ $returnpass = -40; $bigtestfail=FALSE;}
if (strlen($pcode) == 0 ){ $returnpass = -50; $bigtestfail=FALSE;}
if (strlen($fname) == 0 ){ $returnpass = -60; $bigtestfail=FALSE;}
if (strlen($lname) == 0 ){ $returnpass = -70; $bigtestfail=FALSE;}
if (strlen($email) == 0 ){ $returnpass = -80; $bigtestfail=FALSE;}
if (strlen($add1) == 0 ){ $returnpass = -90; $bigtestfail=FALSE;}
if (strlen($propertyvalue) == 0 ){ $returnpass = -100; $bigtestfail=FALSE;}
if (strlen($outstandingmortgage) == 0 ){ $returnpass = -110; $bigtestfail=FALSE;}
if (strlen($securedloans) == 0 ){ $returnpass = -120; $bigtestfail=FALSE;}
if (strlen($reasonforsale) == 0 ){ $returnpass = -130; $bigtestfail=FALSE;}
if (strlen($desiredoutcome ) == 0 ){ $returnpass = -140; $bigtestfail=FALSE;}


	
	if ( $bigtestfail ){

 		$securedloansWording = 'No';
 		if ( $securedloans > 0 ){ $securedloansWording = 'Yes';}

		$query = "INSERT INTO `lme_leads` (`property_type`,`num_bedrooms`,`signuptype`,`cat_id`,`arrived_when`,`archived`, `tel_home`, `srce_id`,`post_code`,`firstname`, `lastname` ,`email`, `add1`, `add2`, `add3`, `add4`, `propvalue`, `mortgageos`, `anyloans` ,`howmuch` , `reasonforsale`, `desiredoutcome` )VALUES ( '$propertytypename','$numberbedrooms','$type',$thetypecat,'$arrived_when','N','$telephone',241,'$pcode', '$fname', '$lname','$email', '$add1', '$add2', '$add3', '$add4',$propertyvalue, $outstandingmortgage,'$securedloansWording',$securedloans,$reasonforsale, $desiredoutcome )";
		
		
		
		
		if ( $localdebug ){echo '<br />'.$query;}
		
		$result = mysqli_query($thenewconnection,$query);
		$aff_user_id = mysqli_insert_id($thenewconnection);
		
		if ( $aff_user_id == 0 ){ $returnpass = -1;}
		
		if ( $aff_user_id > 0 ){
		
			if ( $localdebug ){echo '<br />New row : '.$aff_user_id;}
			
			
			$query = "INSERT INTO  `lme_iar_lead_status` (`lead_id` ,`staging_id` ,`seq_step` ,`last_staged` ,`next_staged`)VALUES ($aff_user_id,  '0',  '0',  '0000-00-00',  '0000-00-00')";
			
			if ( $localdebug ){echo '<br />'.$query;}
			
			$result = mysqli_query($thenewconnection,$query);
			$nextrcrow = mysqli_insert_id($thenewconnection);
			if ( $nextrcrow == 0 ){ $returnpass = -2;}
			
				
			$query = "select name from lme_src where id = 4";
			if ( $localdebug ){echo '<br />'.$query;}
			
			$result = mysqli_query($thenewconnection,$query);
			$sourcename=mysqli_result($result,0,"name");
			if ( $localdebug ){echo '<br />name : '.$sourcename;}	
			
			
		$query = "SELECT description from lme_setup where id = ".$reasonforsale;
		$result = mysqli_query($thenewconnection,$query);
		$reasonforsalename=mysqli_result($result,0,"description");
		
		$query = "SELECT description from lme_setup where id = ".$desiredoutcome;
		$result = mysqli_query($thenewconnection,$query);
		$desiredoutcomename=mysqli_result($result,0,"description");
		
		
		// handle buyers/sellers
		if ( $type == 'S' ){ 
			
			$crmnotes = "Lead Received from Investor";
						
			$auditwrite = 'Lead received from Investor' ;
			
			}
		


			$query = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` )VALUES (".$aff_user_id.", 'NPB', '$arrived_when','$arrived_whentime', '$auditwrite', '1')";
			if ( $localdebug ){echo '<br />'.$query;}
		$result = mysqli_query($thenewconnection,$query);
		
					$query = "INSERT INTO `lme_lead_audit` (`lead_id` ,`user` ,`audit_date`,`audit_time`  ,`description` ,`audit_type` )VALUES (".$aff_user_id.", 'NPB', '$arrived_when','$arrived_whentime', '$crmnotes', '1')";
			if ( $localdebug ){echo '<br />'.$query;}
		$result = mysqli_query($thenewconnection,$query);
		
	
		

			
		
		}
		
	}
  
  
 		include "db_close.php";	
	
}
  	
  
 if ( $aff_user_id > 0 ){ $returnpass = $aff_user_id;}
  
  Return $returnpass;
}


function lme_bmv_inv_func_store_pics( $propertyid, $picnames){
    // used to store images for a specific property

   $returnpass = 0;

   Return $returnpass; 

}



?>