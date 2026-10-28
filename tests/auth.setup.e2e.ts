import { test } from '@e2e-dev/web';
import { expect, credentials } from 'e2e';

test.setup('authenticate as e2e.admin', { sessions: ['admin'] }, async ({ app, screen, session, browser }) => {
  const admin = credentials.user('admin');
  await app.open('/admin/login');
  // No for/id on the labels and no placeholders, so the inputs carry no
  // accessible name: identify them by order inside <main>.
  await screen.getByRole('main').getByRole('textbox').nth(0).fill(admin.username);
  await screen.getByRole('main').getByRole('textbox').nth(1).fill(admin.password);
  await screen.getByRole('button', 'Log in').tap();
  await expect(browser).toHaveURL('/admin'); // prove the sign-in worked before saving
  await session.save('admin');
});
