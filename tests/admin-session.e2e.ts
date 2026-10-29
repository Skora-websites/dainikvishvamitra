import { expect, test } from 'e2e';

const HERO_TITLE = 'गंगा सफाई अभियान में रिकॉर्ड स्तर पर कुदरत उपाय';

test('the dashboard opens for a signed-in admin', { session: 'admin' }, async ({ app, screen, browser }) => {
  await app.open('/admin');
  await expect(browser).toHaveURL('/admin');
  await expect(screen.getByText('E2E Admin')).toBeVisible();
  await expect(screen.getByRole('heading', 'Latest articles')).toBeVisible();
});

test('the admin article list shows seeded articles', { session: 'admin' }, async ({ app, screen }) => {
  await app.open('/admin/articles');
  await expect(screen.getByRole('columnheader', 'Title')).toBeVisible();
  await expect(screen.getByText(HERO_TITLE)).toBeVisible();
});
