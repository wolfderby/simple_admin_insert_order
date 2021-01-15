<?php

/*CURRENT GOAL: MANUALLY INSERT SIMPLEST 1 PRODUCT ORDER INTO THE DATABASE USING ORDER OBJECT

*/
require('includes/application_top.php');

include(DIR_WS_CLASSES . 'order.php');

//require('includes/classes/yoyo_gmail_action.js');
// ^ SEE BELOW
?>
<script type="text/javascript">
function updateSoldFor(der){
    var earn = der.previousSibling.previousSibling.value;
    var soldFor = earn - der.value;
    der.nextSibling.nextSibling.value = soldFor;
}
</script>
<?php 

echo '<h1><a href="manually_insert_order.php">Manually Insert an Order</a></h1>';

global $db;

if(!empty($_POST) && (!isset($_POST['delete']))) {

    $oID = '00012384';
    $order = new order((int)$oID);
    echo '$_POST form data: <pre>'; 
    print_r($_POST);
    echo '</pre>';
    //map order stuff
    
    //date
    $order->info['date_purchased'] = $_POST['nameDate'];
    
    //payment
    $order->info['payment_method'] = 'Amazon Payments';
    $order->info['payment_module_code'] = 'amazon';

    
    //ship method
    $order->info['shipping_method'] = 'amazon_ship';
    //ship cost
    $order->info['shipping_cost'] = $_POST['nameShip'];

    //map customer stuff
    $order->customer['id'] = '429';
    $order->customer['name'] = 'Amazon Gmail Order';
    
    //map product stuff
    //quan
    $order->products['qty'] = $_POST['nameQuan'];
    $order->products['id'] = $_POST['namePid'];
    $order->products['name'] = $_POST['nameName'];
    $order->products['model'] = $_POST['nameModel'];
    $order->products['taxprice'] = 0;
    $order->products['products_cost'] = $_POST['nameCogs'];
    $order->products['price'] = $_POST['nameSoldFor'];
    $order->products['final_price'] = $_POST['nameSoldFor'];
    $order->products['shipping_cost'] = $_POST['nameShip'];

    //amazon sku
    ////need to create field in db still
    
    //map order totals stuff
    $order->info['cogs'] = $_POST['nameCogs'];
    $order->info['subtotal'] = $_POST['nameSoldFor'];
    $order->info['total'] = $_POST['nameSoldFor'];
    $order->info['comments'] = $_POST['nameOrderData'];
    
    //ut oh this object has an object I don't understand yet 
    $order->totals['title'] = $_POST['nameOrderData'];
    $order->totals['text'] = $_POST['nameOrderData'];
    $order->totals['class'] = $_POST['nameOrderData'];
    $order->totals['value'] = $_POST['nameOrderData'];


    /**
       * require order_total class
       */
    //echo(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order_total.php');
    require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'order_total.php');
    $order_total_modules = new order_total();
    $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_BEFORE_ORDER_TOTALS_PROCESS');
    //$order_totals = $order_total_modules->process(); // PHP Warning: sizeof(): Parameter must be an array or an object that implements Countable in C:\xampp\htdocs\pebs_from_gitlab\pebs\includes\classes\order_total.php on line 66.
    $zco_notifier->notify('NOTIFY_CHECKOUT_PROCESS_AFTER_ORDER_TOTALS_PROCESS');

    echo '<pre>';
    print_r($order);
    echo '</pre>';

}

//data returned from gmail api
$date = '01/12/2021';
$order_id = '113-1177177-1957015';
$quantity = '1';

    echo '<br />';
    echo '<br />';
    echo zen_draw_form('addAmazonGmailOrder', 'manually_insert_order.php');
    echo zen_draw_hidden_field('nameOrderData',$order_data); //function zen_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    echo 'date:' . zen_draw_input_field('nameDate',$date, 'style=width:75px'); //function zen_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    echo 'oid:' . zen_draw_input_field('nameAmazonOrderId',$order_id, 'style=width:145px'); //function zen_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    echo 'quan:' . zen_draw_input_field('nameQuan',$quantity, 'style="width:25px"');
    //echo zen_draw_form('formName', 'formAction');
    
    $sku = 'MYPRODUCTMODEL-001';
    $products_name = $sku; //reset if actual name is pulled from zen-cart db in products_cost query
    $yourearnings = '72.09';
    $amazons_sku = 'YB-8T8E-MW38';
    $prodCostResult = $db->Execute("SELECT DISTINCT products_cost, products_id FROM " . TABLE_PRODUCTS . " p WHERE p.products_model='". $sku . "' LIMIT 1");

    if(isset($prodCostResult->fields['products_cost'])){
        $pID = $prodCostResult->fields['products_id'];
        $prodCost = $prodCostResult->fields['products_cost'];
        $prodNameResult = $db->Execute("SELECT DISTINCT products_name FROM " . TABLE_PRODUCTS_DESCRIPTION . " pd WHERE pd.products_id='". $pID . "' LIMIT 1");
        if(isset($prodNameResult->fields['products_name'])){
        $products_name = $prodNameResult->fields['products_name'];
        }
    }else{
        echo '<br />This Product Not Found in Database<br />';
        echo ("SELECT DISTINCT products_cost FROM " . TABLE_PRODUCTS . " p WHERE p.products_model='". $sku . "' LIMIT 1");
        echo '<br />';
    }
    echo 'amazons_sku:' . zen_draw_input_field('nameSkus',$amazons_sku, 'style=width:125px');
    echo 'name:' . zen_draw_input_field('nameName',$products_name, 'style=width:125px');
    echo 'model:' . zen_draw_input_field('nameModel',$sku, 'style=width:125px');
    $earn_attributes = 'style=width:45px';
    echo 'earn:' . zen_draw_input_field('nameEarn',$yourearnings, $earn_attributes);
    echo 'shippingWePaid:' . zen_draw_input_field('nameShip','valueShipWePaid', 'style=width:100px onKeyUp="updateSoldFor(this);"');
    echo 'soldFor:' . zen_draw_input_field('nameSoldFor','valueSkusSoldFor', 'style=width:180px');

    if(!empty($prodCost)){
        $nameSkus = $nameName;
        echo zen_draw_input_field('nameCogs',$prodCost, 'style=width:75px');
        echo zen_draw_input_field('namePid',$pID, 'style=width:75px');
    }else{
        echo zen_draw_input_field('nameCogs','valueCogs', 'style=width:75px');
        echo zen_draw_input_field('namePid','777', 'style=width:75px');
    }
    echo zen_image_submit('button_insert.gif', IMAGE_INSERT);
    echo '</form>';