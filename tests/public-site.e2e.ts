import { describe, test } from '@e2e-dev/web';
import { expect } from 'e2e';

const HERO_TITLE = 'गंगा सफाई अभियान में रिकॉर्ड स्तर पर कुदरत उपाय';

describe('public site', () => {
  test('the home page renders the masthead, nav and seeded hero story', async ({ app, screen, browser }) => {
    await app.open('/');
    await expect(browser).toHaveTitle('दैनिक विश्वामित्र | ताज़ा खबर, राजनीति, व्यापार, खेल');
    await expect(screen.getByRole('navigation').getByRole('link', 'होम')).toBeVisible();
    await expect(screen.getByRole('navigation').getByRole('link', 'ताज़ा खबरें')).toBeVisible();
    await expect(screen.getByRole('heading', HERO_TITLE)).toBeVisible();
  });

  test('a home hero story opens its article page with key points', async ({ app, screen, browser }) => {
    await app.open('/');
    await screen.getByRole('heading', HERO_TITLE).tap();
    await expect(browser).toHaveURL('/article/e2e-hero-ganga-cleanup');
    await expect(screen.getByRole('heading', { name: HERO_TITLE, level: 1 })).toBeVisible();
    await expect(screen.getByRole('heading', 'मुख्य बिंदु')).toBeVisible();
  });

  test('a category page shows its lead story', async ({ app, screen }) => {
    await app.open('/category/latest-news');
    // The lead is the h2; the feed below repeats every title as an h3.
    await expect(screen.getByRole('heading', { name: HERO_TITLE, level: 2 })).toBeVisible();
  });
});
