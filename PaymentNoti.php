<?php
header("Content-type: text/plain; charset=utf-8");


    //-----------------------------------------------------------------------------------------------------------------
    // Description    : Get payment results returned by Notify URL
    //                - If the payment is successful, the payment result is returned to the Notify URL.
    //                - It receives the result value through the received Notify URL and performs logic
    //                - such as charging and purchasing suitable for the affiliated store.
    //-----------------------------------------------------------------------------------------------------------------
    if(empty($_POST))
    {
        echo "<RESULT>FAIL</RESULT>";
        return;
    }


    //-----------------------------------------------------------------------------------------------------------------------
    // Description    : Setting payment result information
    //                - The customer can save the following payment information in the DB.
    //                - leave a file log of the following information for debugging.
    //                - To prevent forgery/modification of the parameters passed to Return URL / Notify URL,
    //                - create a SHA256 hash value and then perform comparison verification with the passed hash.
    //                - If successful after processing is completed in the Notify URL, please print <RESULT>OK</RESULT>.
    //-----------------------------------------------------------------------------------------------------------------------
    $strStoreID      = $_POST['storeid'];           //--Store ID
    $strCountryCode  = $_POST['countrycode'];       //--Country Code
    $strCurrency     = $_POST['currency'];          //--Currency Code
    $strStoreOrderNo = $_POST['storeorderno'];      //--Store Order Number
    $strPayAmt       = $_POST['payamt'];            //--Payment request amount

    $strPayerId      = $_POST['payerid'];           //--Payer ID
    $strPayerEmail   = $_POST['payeremail'];        //--Payer E-Mail
    $strServiceName  = $_POST['servicename'];       //--Product Information
    $strCustom       = $_POST['custom'];            //--Additional Information
    $strPayInfo      = $_POST['payinfo'];           //--Payment Information

    $strPgInfo       = $_POST['pginfo'];            //--PG Information paid
    $strTimeStamp    = $_POST['timestamp'];         //--Unix Time Stamp
    $strHash         = $_POST['hash'];              //--SHA256 DATA for comparative verification to prevent parameter forgery/modulation
    $strNotifyType   = $_POST['notifytype'];        //--Notification Type
    $strPayToken     = $_POST['paytoken'];          //--Payletter's unique payment number

    $strTranTime     = $_POST['trantime'];          //--Payment time (yyyy-mm-dd hh:mm:ss)
    $strPOQToken     = $_POST['poqtoken'];          //--Token value used for recurring payment of Payletter
    $strCardKind     = $_POST['cardkind'];          //--Card Kind
    $strCardNo       = $_POST['cardno'];            //--Card no In the case of (pginfo: PLCreditCard), mask processing excluding the last 4 digits of delivery
    $strRetCode      = $_POST['retcode'];           //--Result code (0 = payment success 0 <> payment failure)

    $strRetMsg       = $_POST['retmsg'];            //--Result message


    //-----------------------------------------------------------------------------------------------------------------
    //1. Check retcode
    //-----------------------------------------------------------------------------------------------------------------
    if($strRetCode != "0")
    {
        echo "<RESULT>FAIL</RESULT>";
        return;
    }


    //-----------------------------------------------------------------------------------------------------------------
    //2. hash value validation
    //   To prevent forgery/modulation of parameters, create SHA256 hash value and compare and verify the received hash value.
    //   hash = GetSHA256(storeid + currency + storeorderno + payamt + payerid + timestamp + hashkey)
    //-----------------------------------------------------------------------------------------------------------------
    $strVerifyHash = hash("sha256", $strStoreID. $strCurrency. $strStoreOrderNo. $strPayAmt. $strPayerId. $strTimeStamp. "PL_Merchant");

    if($strHash != $strVerifyHash)
    {
        echo "<RESULT>FAIL</RESULT>";
        return;
    }


    //-----------------------------------------------------------------------------------------------------------------
    //3. Logic implementation according to NotifyType
    //-----------------------------------------------------------------------------------------------------------------
    if ($strNotifyType == "1")
    {
        //--Success/Purchasing processing progress
    }
    elseif ($strNotifyType == "2")
    {
        //--Refund/Cancellation processing in progress
    }
    else
    {
        //--Refer to the guide document NotifyType
    }


    //--If successful, make sure that html and other code other than <RESULT>OK</RESULT> are not exposed on the page.
    //--If it is not <RESULT>OK</RESULT>, the notification is considered to have failed, and notifications are resent every 5 minutes, up to 10 times.
    echo "<RESULT>OK</RESULT>";
?>
