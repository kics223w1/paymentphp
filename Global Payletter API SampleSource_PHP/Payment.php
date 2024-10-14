<?php
header("Content-type: text/html; charset=utf-8");

    //--------------------------------------------------------------------------------------------
    // Description    : Payment Request API URL
    //                - TEST URL : https://dev-api.payletter.com/api/payment/request
    //                - LIVE URL : https://api.payletter.com/api/payment/request
    //--------------------------------------------------------------------------------------------
    $strReqUrl    = 'https://dev-api.payletter.com/api/payment/request';


    //--------------------------------------------------------------------------------------------
    // 1. Set payment request parameters
    //    -When the store contract is completed, the store ID and HashKey are issued.
    //    -Integration test is possible with HashKey pre-configured in the test environment before signing up.
    //    -Please refer to the guide document for parameters to be set when requesting payment with a PG other than PLCreditCard.
    //    -StoreID : PL_Merchant
    //    -HashKey : PL_Merchant
    //
    //    ※ Only PLCreditcard (non-authenticated) and PayPalExpressCheckout are accepted for test payment.
    //--------------------------------------------------------------------------------------------


    //--Store ID                              
    //--Currency Code                         
    //--Store Order Number                    
    //--Amount                                
    //--Payer ID                              
    //--Payer Email                           
    //--URL to return after payment processing
    //--URL to receive payment success result 
    //--Payment Request PG Information        

    $strRequestContent = '{
                            "storeid"      : "PL_Merchant",
                            "currency"     : "USD",
                            "storeorderno" : "123456789",
                            "amount"       : 1,
                            "payerid"      : "tester",
                            "payeremail"   : "tester@test.com",
                            "returnurl"    : "https://merchant.test.com/Return.php",
                            "notiurl"      : "https://merchant.test.com/PaymentNoti.php",
                            "pginfo"       : "PLCreditCard",
                          }';


    //----------------------------------------------------------------------------------------------------------------------------
    // 2. Authorization
    //----------------------------------------------------------------------------------------------------------------------------
    $strAuth = "GPLKEY " . "PL_Merchant";


    //----------------------------------------------------------------------------------------------------------------------------
    // 3. Payment Request
    //----------------------------------------------------------------------------------------------------------------------------
    $arrHeaderData   = [];
    $arrHeaderData[] = 'Content-Type: application/json';
    $arrHeaderData[] = "Authorization: ". $strAuth;

    $objCurl = curl_init();
    curl_setopt($objCurl, CURLOPT_URL, $strReqUrl);
    curl_setopt($objCurl, CURLOPT_HTTPHEADER, $arrHeaderData);
    curl_setopt($objCurl, CURLOPT_POST, 1);
    curl_setopt($objCurl, CURLOPT_POSTFIELDS, iconv("euc-kr", "utf-8", $strRequestContent));
    curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);


    //----------------------------------------------------------------------------------------------------------------------------
    //4. Whether the API request was successful/failed (error code)
    //   Request processing succeeds only when HTTP StatusCode 200 OK. If not, refer to the StatusCode below.
    // - 400 : [997] The request is invalid (Request parameter error)
    // - 401 : [998] Authentication token is missing or incorrect. (Authentication error)
    // - 403 : [993] Yon do not have authorization. (Authentication error)
    // - 406 : [995] Error detail Message  (Error during business logic processing)
    // - 500 : [999] Internal server error (System internal error)
    //----------------------------------------------------------------------------------------------------------------------------


    $strResponse   = curl_exec($objCurl);

    $objJsonData = json_decode(urldecode($strResponse));

    // In case of successful request processing
    // Response Parameters (In case of successful) : token, online_url, mobile_url
    if(curl_getinfo($objCurl, CURLINFO_HTTP_CODE) == 200)
    {
        echo $objJsonData->token;           //--Payment authentication token
        echo $objJsonData->online_url;      //--Payment page call URL (PC environment)
        echo $objJsonData->mobile_url;      //--Payment page call URL (Mobile environment)
    }
    // If not success
    // Response Parameters (In case of failure) : code, message
    else
    {
        echo $strResponse;
    }

    curl_close($objCurl);
?>