<?php
// Required for WordPress functionality
require_once('wp-load.php');

// Initialize SOAP client for SanMar Inventory Service
$wsdlUrl = 'https://ws.sanmar.com:8080/promostandards/InventoryServiceBindingV2final?wsdl';
$client = new SoapClient($wsdlUrl, ['trace' => 1, 'exception' => 0]);

// Authentication details for SanMar
$authDetails = [
    'wsVersion' => '2.0.0',
    'id' => '', // Your SanMar credentials
    'password' => '!' // Your SanMar credentials
];

// Function to fetch all WooCommerce products with their SKUs and IDs
function fetchWooCommerceProducts()
{
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids'
    );

    $product_ids = get_posts($args);

    return $product_ids;
}

// Function to update WooCommerce product variation inventory
function updateWooCommerceProductInventory($variation_id, $quantity)
{
    $old_quantity = get_post_meta($variation_id, '_stock', true);
    update_post_meta($variation_id, '_stock', $quantity);
    return $old_quantity;
}

// Fetch product SKUs from WooCommerce
$product_ids = fetchWooCommerceProducts();

foreach ($product_ids as $product_id) {
    $product = wc_get_product($product_id);
    $product_sku = $product->get_sku();

    if ($product && $product->is_type('variable')) {
        foreach ($product->get_children() as $variation_id) {
            $variation = wc_get_product($variation_id);
            $variant_sku = $variation->get_sku();

            // Constructing the request parameters for SOAP call
            $requestParams = [
                'wsVersion' => $authDetails['wsVersion'],
                'id' => $authDetails['id'],
                'password' => $authDetails['password'],
                'productId' => $product_sku,
                'Filter' => [
                    'partIdArray' => [
                        'partId' => [$variant_sku],
                    ],
                ],
            ];

            try {
                $result = $client->getInventoryLevels($requestParams);
                if (!empty($result->Inventory->PartInventoryArray->PartInventory->InventoryLocationArray->InventoryLocation)) {
                    $sanmarInventory = $result->Inventory->PartInventoryArray->PartInventory->InventoryLocationArray->InventoryLocation[0]->inventoryLocationQuantity->Quantity->value;

                    // Update WooCommerce inventory for the variant
                    $old_quantity = updateWooCommerceProductInventory($variation_id, $sanmarInventory);
                    echo "Success: Inventory for SKU: $variant_sku (Variation ID: $variation_id) updated. Old stock quantity: $old_quantity, New stock quantity: $sanmarInventory<br>";
                } else {
                    echo "SKU not found in SanMar inventory: $variant_sku. Quantity not updated.<br>";
                }
            } catch (Exception $e) {
                echo "Error updating inventory for SKU: $variant_sku - " . $e->getMessage() . "<br>";
            }
        }
    } else if ($product) {
        echo "Product SKU/ID not found or is not a variable product: $product_sku. Quantity not updated.<br>";
    }
}
