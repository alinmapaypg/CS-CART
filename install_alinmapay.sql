REPLACE INTO cscart_payment_processors (`processor`,`processor_script`,`processor_template`,`admin_template`,`callback`,`type`) VALUES ('AlinmaPay','alinmapay.php', 'views/orders/components/payments/alinmapay.tpl','alinmapay.tpl', 'Y', 'P');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','terminal_id','Terminal Id');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','password','Password');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','merchant_key','Merchant Key');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','request_url','Request URL');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','metadata','MetaData');

REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','text_vegaah_failed_order','No response from alinmapay has been received. Please contact the store staff and tell them the order ID:');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','text_vegaah_pending','No response from alinmapay. Please check the payment using Client ID on alinmapay dashboard. ');
REPLACE INTO cscart_language_values (`lang_code`,`name`,`value`) VALUES ('EN','text_vegaah_success','Payment Sucessful. You can check the payment using Client ID on alinmapay dashboard. ');