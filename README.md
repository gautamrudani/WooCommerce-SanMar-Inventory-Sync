# WooCommerce SanMar Inventory Sync

## Description
This PHP script integrates WooCommerce with SanMar's Inventory Service using a SOAP client. It fetches all WooCommerce products and their variations, then updates the inventory for each variation by making SOAP requests to SanMar's Inventory Service.

## Features
- Fetches WooCommerce products and their variations.
- Retrieves current inventory levels from SanMar using SOAP requests.
- Updates the inventory levels in WooCommerce based on the data retrieved from SanMar.

## Requirements
- **WordPress with WooCommerce**: This script is intended to run in a WordPress environment with WooCommerce installed.
- **SOAP extension**: PHP must have the SOAP extension enabled.
- **SanMar Credentials**: Valid credentials (`id` and `password`) for accessing the SanMar Inventory Service.

## Installation
1. **Copy the Script**: Place the PHP script in the root of your WordPress installation.
2. **Dependencies**: Ensure that the SOAP PHP extension is enabled.
3. **Configuration**: Update the script with your SanMar credentials.

## Usage
1. **Run the Script**: The script can be executed manually or triggered via a cron job.
2. **Automation**: To keep your WooCommerce inventory in sync with SanMar, consider setting up a cron job to run this script periodically.

## Example
Set up a cron job to run this script every hour:
```sh   
0 * * * * php /path/to/your/wordpress/SanMarWooCommerceInventorySync.php


Contact:
For any issues or inquiries, please contact gautamrudani1@gmail.com
