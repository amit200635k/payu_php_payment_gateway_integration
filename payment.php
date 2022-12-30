<?php
// Merchant key here as provided by Payu
$MERCHANT_KEY = "";
// Merchant Salt as provided by Payu
$SALT = "";
// Change to https://secure.payu.in for LIVE mode https://test.payu.in
$PAYU_BASE_URL = "https://test.payu.in";
$action = '';
$hash_string = '';
$hash = '';
// Hash Sequence
  $hashSequence = "key|txnid|amount|productinfo|firstname|email||||||||||";
$posted = array();

if (!empty($_POST)) {
    $posted['key'] = $MERCHANT_KEY;
    $posted['surl']='https://google.com' ;
    $posted['furl']='https://facebook.com' ;
    $posted['service_provider']= 'payu_paisa';
    foreach ($_POST as $key => $value) {
        if($key=='fullname'){
            $posted['firstname'] = $value;
        }
        else if($key=='amount'){
            $posted['amount'] = $value ? $value:'0';
        }
        else if($key=='contactno'){
            $posted['phone'] =  $value;
           
        } 
        else if($key=='email'){
            $posted['email'] = $value;
           
        }
        else if($key=='dob'){
            $posted['productinfo'] = $key.'-'.$value;
        }
        else if($key=='payingfor'){
            $posted['productinfo'] = $posted['productinfo'] ? $posted['productinfo'].','. $key.'-'.$value : $key.'-'. $value;
           
        }  
    }
    $_POST = array();
}
$formError = 0;
if (empty($posted['txnid'])) {
    // Generate random transaction id
    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    $posted['txnid']=$txnid ;
} else {
    $txnid = $posted['txnid'];
}

if (empty($posted['hash']) && sizeof($posted) > 0) {
    if (
        empty($posted['key'])
        || empty($posted['txnid'])
        || empty($posted['amount'])
        || empty($posted['firstname'])
        || empty($posted['email'])
        || empty($posted['phone'])
        || empty($posted['productinfo'])
        || empty($posted['surl'])
        || empty($posted['furl'])
        || empty($posted['service_provider'])
    ) {
        $formError = 1;
    } else {
         $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach ($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }
        $hash_string .= $SALT;
        $hash = hash('sha512', $hash_string);
        $action = $PAYU_BASE_URL . '/_payment';
    }
} elseif (!empty($posted['hash'])) {
    $hash = $posted['hash'];
    $action = $PAYU_BASE_URL . '/_payment';
}
 
?>

<html>

<head>
    <script>
        var hash = '<?php echo $hash ?>';

        function submitForm() {
            if (hash == '') {
                return;
            }
            //console.log(hash);
            var payuForm = document.forms.payuForm;
            payuForm.submit();
        }
    </script>
</head>
<?php echo hash('sha512',$hash_string);?>
<body onLoad="submitForm()"> 
    <h2>Payment Processing .....</h2>
    <br />
    <?php if ($formError) { ?>
        <span style="color:red">Please fill all mandatory fields.</span>
        <br />
        <br />
    <?php } ?>
    <form action="<?php echo $action; ?>" method="post" name="payuForm" enctype="multipart/form-data" >
        <input type="text" name="key" value="<?php echo $MERCHANT_KEY ?>" />
        <input type="text" name="hash" value="<?php echo $hash ?>" />  
        <input type="text" name="txnid" value="<?php echo $txnid ?>" />
         <table>
            <tr>
                <td><b>Mandatory Parameters</b></td>
            </tr>
            <tr>
                <td>Amount: </td>
                <td><input name="amount" value="<?php echo (empty($posted['amount'])) ? '' : $posted['amount'] ?>" /></td>
                <td>First Name: </td>
                <td><input name="firstname" id="firstname" value="<?php echo (empty($posted['firstname'])) ? '' : $posted['firstname']; ?>" /></td>
            </tr>
            <tr>
                <td>Email: </td>
                <td><input name="email" id="email" value="<?php echo (empty($posted['email'])) ? '' : $posted['email']; ?>" /></td>
                <td>Phone: </td>
                <td><input name="phone" value="<?php echo (empty($posted['phone'])) ? '' : $posted['phone']; ?>" /></td>
            </tr>
            <tr>
                <td>Product Info: </td>
                <td colspan="3"><textarea name="productinfo"><?php echo (empty($posted['productinfo'])) ? '' : $posted['productinfo'] ?></textarea></td>
            </tr>
            <tr>
                <td>Success URI: </td>
                <td colspan="3"><input name="surl" value="<?php echo (empty($posted['surl'])) ? 'https://google.com' : $posted['surl'] ?>" size="64" /></td>
            </tr>
            <tr>
                <td>Failure URI: </td>
                <td colspan="3"><input name="furl" value="<?php echo (empty($posted['furl'])) ? 'https://facebook.com' : $posted['furl'] ?>" size="64" /></td>
            </tr>
            <tr>
                <td colspan="3"><input type="hidden" name="service_provider" value="payu_paisa" size="64" /></td>
            </tr>
            <tr>
                <?php  if (!$hash) { ?>
                    <td colspan="4"><input type="submit" value="Submit" /></td>
                <?php  } ?>
            </tr>
        </table>
    </form>
</body>

</html>