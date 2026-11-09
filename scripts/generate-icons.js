import fs from 'node:fs';
import path from 'node:path';
import { Resvg } from '@resvg/resvg-js';

const logoSvgPath = path.resolve('public/images/logo.svg');
let originalSvg = fs.readFileSync(logoSvgPath, 'utf8');

// Extract the path elements from logo.svg
const pathMatches = originalSvg.match(/<path[^>]+>/g) || [];
// Replace fill with white for dark background
const whitePaths = pathMatches.map(p => p.replace(/fill="[^"]*"/g, 'fill="#ffffff"')).join('\n');

function buildSvg(size, paddingRatio = 0.15) {
    const iconSize = size * (1 - paddingRatio * 2);
    const offset = size * paddingRatio;
    const scale = iconSize / 500;

    return `
    <svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">
        <rect width="${size}" height="${size}" rx="${size * 0.2}" fill="#0f172a" />
        <g transform="translate(${offset}, ${offset}) scale(${scale})">
            ${whitePaths}
        </g>
    </svg>`;
}

function buildMaskableSvg(size) {
    // Maskable requires 10% - 20% safe zone padding and no rounded corners on the background rect
    const iconSize = size * 0.60;
    const offset = size * 0.20;
    const scale = iconSize / 500;

    return `
    <svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">
        <rect width="${size}" height="${size}" fill="#0f172a" />
        <g transform="translate(${offset}, ${offset}) scale(${scale})">
            ${whitePaths}
        </g>
    </svg>`;
}

const iconsDir = path.resolve('public/images/icons');
if (!fs.existsSync(iconsDir)) {
    fs.mkdirSync(iconsDir, { recursive: true });
}

const targets = [
    { filename: 'icon-192x192.png', size: 192, svg: buildSvg(192, 0.15) },
    { filename: 'icon-512x512.png', size: 512, svg: buildSvg(512, 0.15) },
    { filename: 'icon-maskable-512x512.png', size: 512, svg: buildMaskableSvg(512) },
    { filename: 'apple-touch-icon.png', size: 180, svg: buildSvg(180, 0.12) },
    { filename: 'favicon-32x32.png', size: 32, svg: buildSvg(32, 0.10) },
    { filename: 'favicon-16x16.png', size: 16, svg: buildSvg(16, 0.10) }
];

for (const target of targets) {
    const resvg = new Resvg(target.svg, {
        fitTo: { mode: 'width', value: target.size }
    });
    const pngData = resvg.render();
    const pngBuffer = pngData.asPng();
    const dest = path.join(iconsDir, target.filename);
    fs.writeFileSync(dest, pngBuffer);
    console.log(`Generated ${target.filename} (${target.size}x${target.size})`);
}

// Also write favicon.ico by copying 32x32 or placing in public/favicon.ico
fs.copyFileSync(path.join(iconsDir, 'favicon-32x32.png'), path.resolve('public/favicon.ico'));
console.log('Generated public/favicon.ico');

