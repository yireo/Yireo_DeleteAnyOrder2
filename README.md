# DeleteAnyOrder for Magento 2

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