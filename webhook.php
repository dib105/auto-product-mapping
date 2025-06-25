<?php
include 'db.php';
include 'config.php';
include 'functions.php';

$storeName = $_GET['store_name'] ?? "";
$productJson = file_get_contents('php://input');
$productData = json_decode($productJson);

$timeStamp = time();

if(property_exists($productData, 'id')) {
    $activityLogStr = "Product ID: $productData->id | Product Name: $productData->title";
}
else {
    $activityLogStr = $productJson;
}
createActivityLog($activityLogStr, $timeStamp);

if($storeName == "") {
    createLog("Message: No store name specified", $timeStamp);
    return;
}

$storeLocalData = getLocalStoreData($storeName);

if($storeLocalData != null){
    $storeDataArr = array(
        'product_id' => $productData->id,
        'store_name' => $storeName,
        'unify_url' => $storeLocalData,
        'product_data_json' => $productJson
    );
    $insertUpdateResp = insertUpdateStoreData($productData->id, $storeName, $storeDataArr);
    unset($insertUpdateResp['payload']);
    
    if(str_contains(strtolower($productData->tags), 'auto_map')) {
        $unifyResponse = fireUnifyMapUrl($storeLocalData, $productJson);   
        
        $updateIsMap = array(
            'is_mapped' => '1',
            'unify_response_json' => $unifyResponse
        );
        insertUpdateStoreData($productData->id, $storeName, $updateIsMap);
        createLog("Message: Data updated and mapped successfully. | Response: ".json_encode($insertUpdateResp), $timeStamp);
    }
    else {
        createLog("Message: Data updated but not mapped. | Response: ".json_encode($insertUpdateResp), $timeStamp);
    }

}
else {
    createLog("Message: Store not configured in the config.", $timeStamp);
}

