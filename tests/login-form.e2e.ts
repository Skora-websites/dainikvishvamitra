import { test } from '@e2e-dev/web';
import { expect, credentials } from 'e2e';

test('the login form rejects a wrong password', async ({ app, screen }) => {
  await app.open('/admin/login');
  // The inputs are label siblings with no for/id and no placeholder, so they
  // carry no accessible name: identify them by order — the guest admin layout
  // renders exactly username then password inside <main>.
  await screen.getByRole('main').getByRole('textbox').nth(0).fill(credentials.user('admin').username);
  await screen.getByRole('main').getByRole('textbox').nth(1).fill('not-the-right-password');
  await screen.getByRole('button', 'Log in').tap();
  // The message renders twice: the admin layout's $errors summary (a listitem)
  // and the field's inline @error div. Assert the summary. A listitem takes no
  // accessible name from its contents, so match the role and filter by text.
  await expect(screen.getByRole('listitem').filter({ hasText: 'उपयोगकर्ता नाम या पासवर्ड गलत है।' })).toBeVisible();
});
