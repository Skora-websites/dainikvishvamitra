import type { E2EConfig } from 'e2e';
import { web } from '@e2e-dev/web';
import { gateway } from 'ai';

export default {
  tests: 'tests/**/*.e2e.ts',
  targets: [
    {
      name: 'web',
      engine: web(),
      app: {
        // `php artisan serve` is the whole stack: no @vite directives in the
        // Blade templates (Tailwind CDN + inline styles), so no build step.
        url: 'http://127.0.0.1:8199',
        command: {
          executable: 'php',
          args: ['artisan', 'serve', '--no-reload', '--port={port}'],
          env: {
            // Local workaround: php8.5-sqlite3 is not installed and the
            // extension .so is not on the system. `sudo apt install
            // php8.5-sqlite3` makes this env entry unnecessary.
            PHP_INI_SCAN_DIR: '/home/adam/.config/php-sqlite/scan.d',
          },
          startupTimeout: 30_000,
          shutdownTimeout: 10_000,
          log: '.e2e/logs/app.log',
        },
      },
    },
  ],
  // Model behind every agent.* step. Requires provider auth
  // (`npx e2e login <provider>` or an AI_GATEWAY_API_KEY); the current suite
  // is model-free and runs without it.
  agents: {
    default: {
      model: gateway('openai/gpt-6-luna-fast'),
      system: 'You are a thorough QA agent. Verify every outcome on screen.',
    },
  },
  // Seeded by `php artisan db:seed --class=E2ESeeder --force`.
  credentials: {
    admin: { username: 'e2e.admin', password: 'e2e-pass-1' },
  },
} satisfies E2EConfig;
