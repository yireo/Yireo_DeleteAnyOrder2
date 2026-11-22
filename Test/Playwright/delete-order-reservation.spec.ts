import {expect, test} from '@playwright/test';
import {ADMIN_USER, addToNewCart, configure, deleteOrderInAdmin, placeOrder} from './lib/delete-any-order';

test.describe('Delete order releases its MSI reservation', () => {
    test('a product with qty 1 becomes salable again after deleting its order', async ({page}) => {
        const sku = `deleteanyorder-reservation-${Date.now()}`;

        const result = await configure(page, {
            product: {sku, name: 'DeleteAnyOrder Reservation Product', price: 10, qty: 1, tax_class_id: 0},
            adminUser: ADMIN_USER,
            config: {
                'carriers/flatrate/active': 1,
                'carriers/flatrate/type': 'O',
                'carriers/flatrate/price': 5,
                'payment/checkmo/active': 1,
                'admin/security/use_form_key': 0,
            },
        });
        expect(result.product?.errors ?? [], 'Product could not be created').toEqual([]);

        const orderId = await placeOrder(page, sku);

        const beforeDelete = await addToNewCart(page, sku);
        expect(beforeDelete.response.ok(), 'The ordered product is still salable').toBeFalsy();

        await deleteOrderInAdmin(page, orderId);

        const afterDelete = await addToNewCart(page, sku);
        expect(afterDelete.response.ok(), `The product is not salable again: ${(await afterDelete.response.text()).slice(0, 500)}`).toBeTruthy();
    });
});
