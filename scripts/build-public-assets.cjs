// Build committed public assets without adding Node dependencies to the PHP runtime.
const fs = require('node:fs');
const path = require('node:path');
const os = require('node:os');
const crypto = require('node:crypto');
const toolRoot = process.env.PS_ASSET_TOOL_ROOT || path.join(os.tmpdir(), 'ps-performance-tools', 'node_modules');
const sharp = require(path.join(toolRoot, 'sharp'));
const esbuild = require(path.join(toolRoot, 'esbuild'));
const hash = value => crypto.createHash('sha256').update(value).digest('hex');
(async () => {
    fs.mkdirSync('public/optimized', { recursive: true });
    const files = {}, source_hashes = {};
    async function image(source, base, width, quality = 78) {
        const buffer = await sharp('public/' + source).resize({ width, withoutEnlargement: true }).webp({ quality, effort: 5 }).toBuffer();
        const output = `optimized/${base}.${hash(buffer).slice(0, 10)}.webp`;
        fs.writeFileSync('public/' + output, buffer);
        return output;
    }
    for (const name of fs.readdirSync('public/images')) {
        if (!/\.(png|jpe?g)$/i.test(name)) continue;
        files['images/' + name] = await image('images/' + name, path.parse(name).name, name.includes('hero') ? 1600 : 960);
    }
    files['logo-ps-seleraku-transparent.png'] = await image('logo-ps-seleraku-transparent.png', 'brand-logo-640', 640, 90);
    files['icons/brand-v2-192.png'] = await image('icons/brand-v2-192.png', 'brand-icon-96', 96, 88);
    files['hero.png'] = await image('hero.png', 'hero-1280', 1280, 80);
    const brand = { small: await image('logo-ps-seleraku-transparent.png', 'brand-logo-320', 320, 84) };
    const login = {
        desktop: await image('login-console.jpg', 'login-desktop-1280', 1280, 78),
        mobile: await image('login-console.jpg', 'login-mobile-640', 640, 76),
    };
    files['selo.webp'] = await image('selo.webp', 'selo-160', 160, 84);
    const scripts = ['selo.js', 'login.js', 'pwa.js', 'loading.js', 'rentals.js', 'seleraku-pages.js', 'reference.js', 'hero-carousel.js', 'booking-ui.js', 'rental-alert.js', 'demo-checkout.js'];
    const styles = ['site.css', 'selo.css', 'login.css', 'pwa.css', 'rentals.css', 'seleraku-pages.css', 'reference.css'];
    for (const name of [...scripts, ...styles]) {
        let source = fs.readFileSync('public/' + name, 'utf8').replace(/\r\n/g, '\n');
        source_hashes[name] = hash(source);
        if (name.endsWith('.css')) {
            for (const [original, output] of Object.entries(files)) source = source.replaceAll('/' + original, '/' + output);
            if (name === 'login.css') {
                let count = 0;
                source = source.replace(/url\(['"]?login-console\.jpg['"]?\)/g, () => `url('/${count++ === 0 ? login.desktop : login.mobile}')`);
            }
        }
        const { code } = await esbuild.transform(source, { loader: name.endsWith('.css') ? 'css' : 'js', minify: true, target: 'es2020', legalComments: 'none', target: name.endsWith('.css') ? ['chrome100', 'safari15'] : 'es2020' });
        const parsed = path.parse(name), output = `optimized/${parsed.name}.${hash(code).slice(0, 10)}${parsed.ext}`;
        files[name] = output;
        fs.writeFileSync('public/' + output, code);
    }
    const data = JSON.stringify({ files, source_hashes, login, brand }, null, 2);
    fs.writeFileSync('config/performance-assets.php', `<?php\n\nreturn json_decode(<<<'JSON'\n${data}\nJSON, true);\n`);
    console.log(`Built ${Object.keys(files).length} optimized public assets.`);
})().catch(error => { console.error(error.message); process.exitCode = 1; });
