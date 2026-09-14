import { chromium } from '/tmp/school-playwright/node_modules/playwright/index.mjs';
import fs from 'node:fs';
import path from 'node:path';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1';
const email = process.env.PLAYWRIGHT_EMAIL || 'admin@example.com';
const password = process.env.PLAYWRIGHT_PASSWORD || 'password';
const artifactDir = process.env.PLAYWRIGHT_ARTIFACT_DIR || 'artifacts/playwright';
fs.mkdirSync(artifactDir, { recursive: true });

const routes = JSON.parse(fs.readFileSync('/tmp/school-routes.json', 'utf8'))
  .filter((r) => /GET\|HEAD|GET/.test(r.method) && !r.uri.startsWith('_ignition'));

function candidate(uri) {
  return uri.replace(/\{[^}]+\}/g, (part) => part.includes('token') ? 'invalid-token' : '1');
}

function slug(uri) {
  return (uri.replace(/^\//, '').replace(/[^a-zA-Z0-9]+/g, '_').replace(/^_|_$/g, '') || 'home').slice(0, 100);
}

const browser = await chromium.launch({ headless: true, executablePath: process.env.CHROME_PATH || '/usr/bin/google-chrome' });
const context = await browser.newContext({ viewport: { width: 1440, height: 1000 }, ignoreHTTPSErrors: true });
const page = await context.newPage();
const consoleErrors = [];
page.on('console', (msg) => { if (msg.type() === 'error') consoleErrors.push(msg.text()); });
page.on('pageerror', (err) => consoleErrors.push(`pageerror: ${err.message}`));

const login = await page.goto(`${baseURL}/login`, { waitUntil: 'domcontentloaded' });
if (!login || login.status() >= 400) throw new Error(`Login page unavailable: ${login?.status()}`);
if (page.url().includes('/login')) {
  await page.locator('#email').fill(email);
  await page.locator('#password').fill(password);
  await page.locator('button[type="submit"]').click();
  await page.waitForLoadState('domcontentloaded');
}
const authenticated = !page.url().includes('/login');
await page.screenshot({ path: path.join(artifactDir, '00-login-or-dashboard.png'), fullPage: true });

const seen = new Set();
const results = [];
for (const route of routes) {
  const uri = candidate('/' + route.uri.replace(/^\//, ''));
  if (seen.has(uri) || /\.(pdf|csv|xlsx|xml|json)$/i.test(uri)) continue;
  seen.add(uri);
  consoleErrors.length = 0;
  const started = Date.now();
  let response;
  let error = null;
  try {
    response = await page.goto(`${baseURL}${uri}`, { waitUntil: 'domcontentloaded', timeout: 10000 });
    await page.waitForTimeout(250);
    const title = await page.title();
    const bodyText = (await page.locator('body').innerText().catch(() => '')).slice(0, 500);
    const file = `${String(results.length + 1).padStart(3, '0')}-${slug(route.uri)}.png`;
    await page.screenshot({ path: path.join(artifactDir, file), fullPage: true });
    results.push({ uri, name: route.name, status: response?.status() ?? null, title, screenshot: file, duration_ms: Date.now() - started, redirected_to: page.url(), console_errors: [...consoleErrors], body_excerpt: bodyText });
  } catch (e) {
    error = e.message;
    results.push({ uri, name: route.name, status: response?.status() ?? null, error, duration_ms: Date.now() - started, redirected_to: page.url(), console_errors: [...consoleErrors] });
  }
}

const summary = { generated_at: new Date().toISOString(), baseURL, authenticated, route_candidates: results.length, results };
fs.writeFileSync(path.join(artifactDir, 'results.json'), JSON.stringify(summary, null, 2));
const counts = results.reduce((a, r) => { const key = r.error ? 'error' : r.status >= 500 ? 'server_error' : r.status >= 400 ? 'http_error' : r.redirected_to?.includes('/login') ? 'login_redirect' : 'ok'; a[key] = (a[key] || 0) + 1; return a; }, {});
fs.writeFileSync(path.join(artifactDir, 'summary.json'), JSON.stringify({ ...summary, counts }, null, 2));
await browser.close();
console.log(JSON.stringify({ authenticated, counts, route_candidates: results.length, artifactDir }, null, 2));
