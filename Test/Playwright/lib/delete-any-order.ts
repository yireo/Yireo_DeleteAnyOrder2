import {APIResponse, expect, Page} from '@playwright/test';

export const ADMIN_USER = {
    username: 'deleteanyorder_admin',
    password: 'P@ssw0rd1234x',
    email: 'deleteanyorder_admin@example.com',
    firstname: 'Delete',
    lastname: 'AnyOrder',
    resources: [
        'Magento_Backend::admin',
        'Magento_Sales::sales',
        'Magento_Sales::sales_operation',
        'Magento_Sales::sales_order',
        'Magento_Sales::actions',
        'Magento_Sales::actions_view',
        'Magento_Backend::system',
        'Magento_Backend::tools',
        'Yireo_DeleteAnyOrder2::index',
    ],
};

const address = {
    firstname: 'John',
    lastname: 'Doe',
    street: ['Main Street 1'],
    city: 'Amsterdam',
    postcode: '1000AA',
    country_id: 'NL',
    telephone: '0123456789',
    email: 'john.doe@example.com',
};

export const adminPath = (): string => (process.env.ADMIN_PATH ?? '').trim().replace(/^\/+|\/+$/g, '') || 'admin';

export async function configure(page: Page, payload: Record<string, any>) {
    const response = await page.request.post('/loki_functional_tests/index/configure?token=' + process.env.TEST_TOKEN, {
        form: {config: JSON.stringify(payload)},
    });

    const body = await response.text();
    let data: any;
    try {
        data = JSON.parse(body);
    } catch (error) {
        throw new Error(`Configure endpoint returned no JSON (status ${response.status()}): ${body.slice(0, 500)}`);
    }

    expect(data.error, 'Configure error').toBeFalsy();
    return data.result ?? {};
}

async function rest(page: Page, path: string, data?: Record<string, any>): Promise<APIResponse> {
    return page.request.post('/rest/default/V1/' + path, {data: data ?? {}});
}

async function restJson(page: Page, path: string, data?: Record<string, any>) {
    const response = await rest(page, path, data);
    const body = await response.text();
    expect(response.ok(), `REST ${path} failed: ${body.slice(0, 500)}`).toBeTruthy();
    return JSON.parse(body);
}

/**
 * Add the product to a new guest cart; this fails when the product is not salable anymore
 */
export async function addToNewCart(page: Page, sku: string): Promise<{cartId: string, response: APIResponse}> {
    const cartId = await restJson(page, 'guest-carts');
    const response = await rest(page, `guest-carts/${cartId}/items`, {
        cartItem: {sku, qty: 1, quote_id: cartId},
    });

    return {cartId, response};
}

/**
 * Place an order through the guest REST API (so with any checkout) and return its entity ID
 */
export async function placeOrder(page: Page, sku: string): Promise<string> {
    const {cartId, response} = await addToNewCart(page, sku);
    expect(response.ok(), `Adding ${sku} to the cart failed: ${(await response.text()).slice(0, 500)}`).toBeTruthy();

    await restJson(page, `guest-carts/${cartId}/shipping-information`, {
        addressInformation: {
            shipping_address: address,
            billing_address: address,
            shipping_carrier_code: 'flatrate',
            shipping_method_code: 'flatrate',
        },
    });

    const orderId = await restJson(page, `guest-carts/${cartId}/payment-information`, {
        email: address.email,
        paymentMethod: {method: 'checkmo'},
        billingAddress: address,
    });

    return String(orderId);
}

export async function loginAsAdmin(page: Page) {
    await page.goto('/' + adminPath() + '/');

    const usernameField = page.locator('#username');
    if (await usernameField.count() === 0) {
        return;
    }

    await usernameField.fill(ADMIN_USER.username);
    await page.locator('#login').fill(ADMIN_USER.password);
    await page.locator('.action-login').click();
    await expect(page.locator('#username'), 'Admin login failed').toHaveCount(0);
}

export async function deleteOrderInAdmin(page: Page, orderId: string) {
    await loginAsAdmin(page);
    await page.goto(`/${adminPath()}/sales/order/view/order_id/${orderId}/`);

    await page.getByRole('button', {name: 'Delete', exact: true}).click();
    const dialog = page.locator('.modal-popup.confirm._show');
    await expect(dialog).toContainText('Are you sure you want to delete this order permanently?');
    await dialog.getByRole('button', {name: 'OK'}).click();

    await expect(page.getByText('Removed order', {exact: true})).toBeVisible();
}
