TaxCloud for Zen Cart v1.5+

TaxCloud® is a free, easy-to-use sales tax management service for retailers. Our free add-on module integrates with Zen Cart version 1.5 and above. This module overrides Zen Cart’s built-in tax calculation and replaces it with a real-time tax rate lookup.

1	How TaxCloud works
After a customer has entered a shipping address during checkout, Zen Cart sends TaxCloud a request to calculate the sales tax due. TaxCloud returns that information to Zen Cart, and sales tax is added to the customer’s total. Once the order is completed, another request is sent to TaxCloud to capture the transaction. All the captured transactions are included in the report that TaxCloud provides at the end of each month. 

2	Preparation
Here’s what you’ll need:
<li>
-	A TaxCloud account. Register at taxcloud.com. Log in to your account and enter your office address, website URL, and the states where you want to collect sales tax. 
-	TaxCloud API ID and API Key. These can be found in your TaxCloud account on the “Websites” page. If the website you want to use TaxCloud with isn’t listed, just click “Add website” and enter the information when prompted. The API ID and Key for that website will be assigned automatically. Please do not share them with anyone or use them for multiple URLs.
-	PHP version 5.0 or above. Most servers have a phpInfo file that displays this information.
-	SOAP and cUrl enabled. Again, the phpInfo file displays this information.
-	A USPS Web Tools Username. This allows TaxCloud to verify the customer’s address and obtain the 9-digit zip code. To get a USPS Web Tools Username, fill out the form at https://secure.shippingapis.com/registration/—your USPS Web Tools Username will be emailed to you. 
</li>

3	Installation
Click "Download ZIP" to get the TaxCloud module. Be sure to back up your Zen Cart installation before you start making changes. Disable and remove any other sales tax modules before installing TaxCloud.

Unzip TaxCloud files and upload them into the corresponding folders under the Zen Cart directory on your server. Note:  as a part of the Zen Cart installation you are prompted to rename the "admin" folder. Since we cannot predict how you will rename the admin folder you must copy the files from the TaxCloud "admin" folder over to your renamed folder.

4	Configuration
Once the module is installed, log in to Zen Cart and navigate to Locations/Taxes > TaxCloud Tax Calculation. Select this menu item to go to the TaxCloud administration page.

Click “Update” to configure your TaxCloud settings as follows:
-	API ID: Enter the API ID for your website (see Section 2, Preparation).
-	API Key: Enter the API Key for your website (see Section 2, Preparation).
-	USPS ID: Enter your USPS Web Tools Username (see Section 2, Preparation).
-	Store Street Address: Enter ONLY the first line of your business’s street address—for example, “100 Front Street.”
-	Store Zip Code: Enter your business’s 5-digit zip code. 
-	TaxCloud enabled: Check this box to enable TaxCloud tax lookups. If you later need to disable TaxCloud for any reason, simply uncheck this box.

Once you’ve entered this information, you should see this message “Server is configured to reach TaxCloud”.

5	Assigning Tax Classes
Each item in your store needs to be assigned a taxability information code or TIC, so TaxCloud can determine whether or not that item is taxed in your customer’s state. These are stored in ZenCart using the “Tax Classes” section. You should set up at least the General Goods and Services tax class, TIC 00000, which is used to designate items that are taxable in every state. For the complete list of TICs, log in to TaxCloud and go to the “Taxability Codes” section.

To add a new tax class/TIC:
1.	Go to Locations/Taxes > Tax Classes.
2.	Click the “New Tax Class” button.
3.	Enter the TIC number in the “Tax Class Title” field and a description in the “Description” field.
4.	Click the “insert” button. 

Once the TIC is created, go to your catalog and assign it to your products:
1.	Select a product by going to Catalog > Categories/Products.
2.	Browse to the product you would like to edit.
3.	Select the appropriate tax class from the “Tax Class” drop-down.
4.	Save your changes.

6	Testing
Once you have completed these steps, try some test transactions to make sure everything is working correctly. Make sure to complete at least one test order. The test order must be purchased; it’s not enough to just add an item to your cart.

To review your test transactions, log in to the TaxCloud website and click on the “Transactions” tab. Click on any transaction to see more details about that order. 

7	Going Live
TaxCloud is in test mode until you set your account to live status. When you’re ready to go live, log in to the TaxCloud website, go to the “Websites” page, and click the “Go Live!” button. If you do not see a “Go Live!” button, you’ll see a message telling you what you need to do in order to go live.

8	Exemption Certificates
We also provide support for Exemption Certificates. If the customer qualifies for a tax exemption and they fill out the included form the exemption certificate will be created and stored on TaxCloud. The customer can then apply the exemption to their shopping cart which will remove the taxes from the total. Multiple exemption certificates can be stored per customer and retrieved each time the customer logs in. This functionality is available as an order total module. If you would like to use this module it will need to be enabled in your Zen Cart admin console. Go to Modules > Order Total, select Exemption Certificates, and click the “Install” button. This will add a link to the checkout page that says “Are you exempt?” See sample, which links to the exemption certificate form here: http://taxcloud.net/imgs/cert_sample.html

9	Coupons
Zen Cart includes a built-in module for managing coupons. Unfortunately this module does not interact with TaxCloud correctly. So instead, we have provided a custom Discount Coupon module that replaces the standard one. If you are planning to use discount coupons on your site, you need to disable the standard Discount Coupon module in the Zen Cart admin console by going to Modules > Order Total. Then, select the TaxCloud version of Discount Coupon, and click the “Install” button.
 
--------------------------------------------------
Provided by The Federal Tax Authority (FedTax.net)

This code is released under the GNU GENERAL PUBLIC LICENSE (see license.txt)

Copyright (c) 2009-2013 The Federal Tax Authority, LLC (FedTax). Information subject to change without notice.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful, on an "AS IS" basis, WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, AND FEDTAX  HEREBY DISCLAIMS ALL SUCH WARRANTIES, INCLUDING WITHOUT LIMITATION, ANY WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, QUIET ENJOYMENT OR NON-INFRINGEMENT.	 See the GNU GENERAL PUBLIC LICENSE for more details.

Please see the GNU GENERAL PUBLIC LICENSE  for the specific language governing rights and limitations under the License.
