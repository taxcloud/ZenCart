<?php
/**
 * TaxCloud Exemption Certificate Module v1.5
 *
 * Displays the customer's existing exemption certificates and allows them to create new certificates
 *
 * @version $Id: tpl_taxcloud_exemptions_default.php 1.5
 * @license https://taxcloud.net/ftpsl.pdf
 */
?>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="https://taxcloud.net/imgs/cert.min.js"></script>

<script type="text/javascript">
    var ajaxLoad = false; 
    
    var tcsURL = "taxcloud.net";
    var tcsProtocol = (("https:" == document.location.protocol) ? "https:" : "http:");
//    var saveCertUrl = '';
//    var reloadWithSave = true;
</script>
<script type="text/javascript">
 $(document).ready(function() {
 
        var tsCss = document.createElement('link');
        tsCss.type = 'text/css'; tsCss.rel = 'stylesheet';
        tsCss.href = tcsProtocol + '//'+tcsURL+'/imgs/jquery-ui-1.8.7.taxcloud.css';
        var tccsss = document.getElementsByTagName('script')[0];
        tccsss.parentNode.insertBefore(tsCss, tccsss);
 
 	xmpt = buildExemptCert();
 	$('.certForm').replaceWith(xmpt); 

	//select all the a tag with name equal to modal
	$('a[name=modal]').click(function(e) { 
	
		//Cancel the link behavior
		e.preventDefault();
		
		//Get the A tag
		var id = $(this).attr('href');
	
		//Get the screen height and width
		var maskHeight = $(document).height();
		var maskWidth = $(window).width();
	
		//Set heigth and width to mask to fill up the whole screen
		$('#mask').css({'width':maskWidth,'height':maskHeight});
		
		//transition effect		
		$('#mask').fadeIn(1000);	
		$('#mask').fadeTo("slow",0.8);	
	
		//Get the window height and width
		var winH = $(window).height();
		var winW = $(window).width();
              
		//Set the popup window to center
		$(id).css('top',  winH/2-$(id).height()/2);
		$(id).css('left', winW/2-$(id).width()/2);
	
		//transition effect
		$(id).fadeIn(1000); 
		
	});
	
	//if close button is clicked
	$('.window .close').click(function (e) {
		//Cancel the link behavior
		e.preventDefault();
		
		$('#mask').hide();
		$('.window').hide();
	});		
	
	//if mask is clicked
	$('#mask').click(function () {
		$(this).hide();
		$('.window').hide();
	});			

});
</script>

<style>

#mask {
	position:absolute;left:0;top:0;z-index:9000;background-color:#000;display:none;
}
  
#boxes .window {
  	position:absolute;left:0;top:0;width:750px;height:635px;display:none;z-index:9999;padding:5px;background-color:#ffffff;text-align:center;font-family: Garamond, Times New Roman;font-size:14pt;cursor:default;border:0px solid red;
}

#boxes .text {
	color:#000000;position:absolute;z-index:100;text-align:center;font-family: Garamond, Times New Roman;font-size:14pt;cursor:default;border:0px solid red;
}

#boxes .certNumber {
	position:absolute;top: 84px;left: 333px;width: 316px;font-size:10pt;font-family: Verdana, Arial;color:red;text-align:right;
}
#boxes .purchaserName {
	position:absolute;top: 249px;left: 406px;width: 216px;
}
#boxes .purchaserAddress {
	position:absolute;top: 272px;left: 189px;width: 437px;
}
#boxes .exemptionState {
	position:absolute;top: 341px;left: 214px;width: 242px;
}
#boxes .exemptionReason {
	position:absolute;top: 364px;left: 177px;width: 483px;height: 48px;
}
#boxes .exemptionCertDate {
	position:absolute;top: 422px;left: 448px;width: 257px;
}
#boxes .idType {
	position:absolute;top: 452px;left: 448px;width: 257px;
}
#boxes .taxidNumber {
	position:absolute;top: 481px;left: 448px;width: 256px;
}
#boxes .businesstype {
	position:absolute;top: 510px;left: 447px;width: 269px;
}
#boxes .seller {
	position:absolute;top: 543px;left: 447px;width: 269px;
}
#boxes .watermark {
	position:absolute;z-index:50;top:0px;width:750px;height:600px;background:transparent url('') no-repeat;
}
#boxes .fields {
	color:#000000;position:absolute;z-index:100;text-align:center;font-family: Garamond, Times New Roman;font-size:14pt;cursor:default;border:0px solid red; 
}
#boxes .cert {
 	width: auto; min-height: 0px; height: 587.033px; background:url('https://taxcloud.net/imgs/cert/exemption_certificate750x600.png') no-repeat 0 0
}

</style>


<div class="centerColumn" id="checkoutPayment">


<h1 id="exemptionHeading"><?php echo EXEMPTION_HEADING_TITLE; ?></h1>


<?php $customerID = $_SESSION['customer_id']; ?>
      
<?php
	//Load up existing exemption certificates
	$exemptionCerts = func_taxcloud_get_exemption_certificates($customerID);
	
	//Filter out single purchase certificates
	$filteredCerts = Array();
	$index = 0;
	foreach ($exemptionCerts as $cert) {
		if ( $cert->Detail->SinglePurchase == true ) {
			continue;
		}
		$filteredCerts[$index] = $cert;
		$index++;
	}
	$exemptionCerts = $filteredCerts;
?>

   
<fieldset>
<legend><?php echo EXEMPTION_TABLE_HEADING_COMMENTS; ?></legend>
<span id="xmptlink"></span>
<table>
<?php 
	if ( empty($exemptionCerts) || sizeof($exemptionCerts) == 0) {
		echo("None");
	} 
	foreach ($exemptionCerts as $cert) {


?>  

	<!-- Create div for pop-up dialog -->
	<div id="boxes">
	

	  <div id="dialog" class="window ui-dialog ui-widget ui-widget-content ui-corner-all ui-draggable" title="Exemption Certificate" role="dialog" aria-labelledby="ui-dialog-title-jqxmptCert"> 
	
	    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
		<span id="ui-dialog-title-jpxmptCert" class="ui-dialog-title">Exemption Certificate</span>
		<a class="close ui-dialog-titlebar-close ui-corner-all" href="#" role="button">
		    <span class="ui-icon ui-icon-closethick">close</span>
		</a>
	    </div>
	    <div class="cert">
	        <!--<div id="stateWatermark" style="display: inline; position: absolute; z-index: 0; left: 0pt; top: 0px; width: 730px; height: 580px; opacity: 0.6; background: url("http://taxcloud.net/imgs/states/WA.gif") no-repeat scroll center center transparent;"> </div> -->
	        <div class="certNumber"><?php echo $cert->CertificateID; ?></div>
	        <div class="purchaserName fields"><?php echo $cert->Detail->PurchaserFirstName.' '.$cert->Detail->PurchaserLastName; ?></div>
	        <div class="purchaserAddress fields"><?php echo $cert->Detail->PurchaserAddress1.', '.$cert->Detail->PurchaserCity.', '.$cert->Detail->PurchaserState.' '.$cert->Detail->PurchaserZip; ?></div>
	        <div class="exemptionState fields"><?php echo $cert->Detail->ExemptStates->ExemptState->StateAbbr; ?></div>
	        <div class="exemptionReason fields"><?php echo $cert->Detail->PurchaserExemptionReason; ?></div>
	        <div class="exemptionCertDate fields"><?php $aDate = date($cert->Detail->CreatedDate); $timestamp = strtotime($aDate); echo date("F j, Y",$timestamp); ?></div>
	        <div class="idType fields"><?php echo $cert->Detail->PurchaserTaxID->TaxType; ?></div>
	        <div class="taxidNumber fields"><?php echo $cert->Detail->PurchaserTaxID->IDNumber; ?></div>
	        <div class="businesstype fields"><?php echo $cert->Detail->PurchaserBusinessType; ?></div>
	        <div class="seller fields"><?php echo STORE_NAME; ?></div>
	    </div>
	   </div> <!-- end dialog -->
	  <div id="mask" class="ui-widget-overlay"></div>
	</div> <!-- end boxes -->
<tr>
	<td>
		<a href="#dialog" name="modal">
		<img src="//taxcloud.net/imgs/cert/exemption_certificate150x120.png">
		</a>
	</td>
	<td>
	<table>
		<tr>
		<td>Issued to: 
			<?php echo $cert->Detail->PurchaserFirstName.' '.$cert->Detail->PurchaserLastName; ?>
		</td>
		</tr>
		<tr>
		<td>Exempt State(s):
			<?php echo $cert->Detail->ExemptStates->ExemptState->StateAbbr; ?>
		</td>
		</tr>
		<tr>
		<td>Date: 
			<?php 
			$aDate = date($cert->Detail->CreatedDate);
			$timestamp = strtotime($aDate); 
			echo date("F j, Y",$timestamp); ?>
		</td>
		</tr>
		<tr>
		<td>Purpose: 
			<?php echo $cert->Detail->PurchaserExemptionReason; ?>
		</td>
		</tr>
		<tr>
		<td>  
		<form method="submit" id="dialog" action="#dialog">
			<a href="#dialog" name="modal"><input type="submit" class="ui-state-default ui-priority-primary ui-corner-all" name="View" value="View" /></a>			
		</form>
		<form method="post" action="<?php 
			$params='delete='.$cert->CertificateID;
			echo zen_href_link('taxcloud_exemption_delete', $params, 'NONSSL', true); ?>">
			<input type="submit" class="ui-state-default ui-priority-primary ui-corner-all" name="Delete" value="Delete" />
			
		</form>
		<form method="post" action="<?php 
			$params='select='.$cert->CertificateID;
			echo zen_href_link('taxcloud_exemption_select', $params, 'NONSSL', true); ?>">
			<input type="submit" class="ui-state-default ui-priority-primary ui-corner-all" name="Use" value="Use this certificate" />
			
		</form>


		</td>
		</tr>
	</table>
</tr>
<?php 
} //end foreach		

?>
</table>
</fieldset>          

<form method="post" action="<?php 
	$params='';
	echo zen_href_link('taxcloud_exemption_save', $params, 'NONSSL', true); ?>">
<fieldset>
<legend><?php echo EXEMPTION_TABLE_HEADING_NEW; ?></legend>

<input type="hidden" name="customer_id" value="<?php echo $_SESSION['customer_id']; ?>">

<h1>Purchaser Certificate of Exemption</h1>

<div class="certForm" id="jqxmpt"></div>


</fieldset>
</form>

<?php echo zen_draw_form('checkout_payment', zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'), 'post', ($flagOnSubmit ? 'onsubmit="return check_form();"' : '')); ?>

<div class="buttonRow forward"><?php echo zen_image_submit(BUTTON_IMAGE_CONTINUE_CHECKOUT, BUTTON_CONTINUE_ALT, 'onclick="submit"'); ?></div>
<div class="buttonRow back"><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '<br />' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></div>

</form>
</div>
