import { defineConfig } from 'astro/config';

export default defineConfig({
  site: 'https://www.sedivakstavby.cz',
  build: { format: 'file', inlineStylesheets: 'always' },
  trailingSlash: 'never',
});
