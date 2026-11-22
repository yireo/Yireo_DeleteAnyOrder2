# DeleteAnyOrder for Magento 2

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-deleteanyorder2)
<!-- badges.specs.end -->

### MSI reservations
When MSI (`Magento_InventoryReservations`) is installed, deleting an order also deletes its rows in the table `inventory_reservation`, so that the reserved quantity becomes salable again. Reservations of orders that were deleted earlier are listed by `bin/magento deleteanyorder:analyse` and removed by `bin/magento deleteanyorder:fix`. As an alternative, MSI can compensate them: `bin/magento inventory:reservation:list-inconsistencies -r | bin/magento inventory:reservation:create-compensations`.

### Unit tests
Install this module via composer in Magento 2 and then run:

    vendor/bin/phpunit vendor/yireo/magento2-deleteanyorder2/Test/Unit/

### Integration tests
Follow the procedure of Magento to setup integration tests. Then add a test suite that points to the `Test/Integration` folder of this extension.

### Todo
- Check existance of related downloadable links
- Fix grids automatically
- Reset increment IDs for orders, shipments and invoices
- Reset stock
## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DeleteAnyOrder2/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DeleteAnyOrder2/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DeleteAnyOrder2/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DeleteAnyOrder2/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_DeleteAnyOrder2/compile.yml?label=compile)
<!-- badges.test.end -->
