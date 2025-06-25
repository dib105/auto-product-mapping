<?php
function getStoreData($productId, $storeName) {
    global $dbConn;
    $dbConn->where('product_id', $productId);
    $dbConn->where('store_name', $storeName);
    $storeData = $dbConn->getOne('cust_auto_product_map_data');
    return $storeData;
}

function insertUpdateStoreData($productId, $storeName, $storeDataPayLoad) {
    global $dbConn;
    
    $storeData = getStoreData($productId, $storeName);

    if($storeData) { // Update data
        try {
            $dbConn->where('product_id', $productId);
            $dbConn->where('store_name', $storeName);
            $dbConn->update('cust_auto_product_map_data', $storeDataPayLoad);
            return array(
                'action' => 'update',
                'status' => 'success',
                'payload' => $storeDataPayLoad,
            );
        } catch (\Throwable $th) {
            // throw $th;
            return array(
                'action' => 'update',
                'status' => 'error',
                'message' => $th->getMessage(),
                'payload' => $storeDataPayLoad,
            );
        }
    }
    else { // Insert data
        try {
            $insertedId = $dbConn->insert('cust_auto_product_map_data', $storeDataPayLoad);
            return array(
                'action' => 'insert',
                'status' => 'success',
                'inserted_id' => $insertedId,
                'payload' => $storeDataPayLoad,
            );
        } catch (\Throwable $th) {
            // throw $th;
            return array(
                'action' => 'insert',
                'status' => 'error',
                'message' => $th->getMessage(),
                'payload' => $storeDataPayLoad,
            );
        }
    }

}

function getLocalStoreData($storeName) {
    global $storeDataArr;
    return $storeDataArr[$storeName] ?? null;
}

function fireUnifyMapUrl($url, $jsonData) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "$jsonData",
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            // 'X-Shopify-Access-Token: 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'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

function createLog($logText, $timeStamp)
{
    file_put_contents('./log-files/log.log', "$timeStamp | ".$logText." | ". date('m/d/Y h:i:s a', time()) . " GMT+\n\n", FILE_APPEND);
}

function createActivityLog($logText, $timeStamp) {
    file_put_contents('./log-files/activity-log.log', "$timeStamp | ".$logText." | ". date('m/d/Y h:i:s a', time()) . " GMT+\n\n", FILE_APPEND);
}