const fs = require('fs');
const path = require('path');

const jsContent = fs.readFileSync('assets/js/script.js', 'utf8');
const cssContent = fs.readFileSync('assets/css/styles.css', 'utf8');

// Read all PHP files
const phpFiles = ['index.php'];
const partsDir = 'parts';
if (fs.existsSync(partsDir)) {
    fs.readdirSync(partsDir).forEach(f => {
        if (f.endsWith('.php') || f.endsWith('.html')) {
            phpFiles.push(path.join(partsDir, f));
        }
    });
}

let htmlContent = '';
phpFiles.forEach(file => {
    if (fs.existsSync(file)) {
        htmlContent += '\n' + fs.readFileSync(file, 'utf8');
    }
});

// Extract IDs from HTML
const htmlIds = new Set();
const idRegex = /id=["']([^"']+)["']/g;
let match;
while ((match = idRegex.exec(htmlContent)) !== null) {
    htmlIds.add(match[1]);
}

// Extract classes from HTML
const htmlClasses = new Set();
const classRegex = /class=["']([^"']+)["']/g;
while ((match = classRegex.exec(htmlContent)) !== null) {
    match[1].split(/\s+/).forEach(c => {
        if (c.trim()) htmlClasses.add(c.trim());
    });
}

// Extract IDs queried by JS: getElementById('...')
const jsQueriedIds = new Set();
const jsIdRegex = /getElementById\(['"]([^'"]+)['"]\)/g;
while ((match = jsIdRegex.exec(jsContent)) !== null) {
    jsQueriedIds.add(match[1]);
}

// Extract selectors queried by querySelector / querySelectorAll
const jsQuerySelectors = new Set();
const qsRegex = /querySelector(?:All)?\(['"]([^'"]+)['"]\)/g;
while ((match = qsRegex.exec(jsContent)) !== null) {
    jsQuerySelectors.add(match[1]);
}

// Extract classes toggled/added/removed by JS
const jsManipulatedClasses = new Set();
const classListRegex = /classList\.(?:add|remove|toggle|contains)\(['"]([^'"]+)['"]\)/g;
while ((match = classListRegex.exec(jsContent)) !== null) {
    jsManipulatedClasses.add(match[1]);
}

console.log('=== JS QUERIED IDs CHECK ===');
const missingIds = [];
jsQueriedIds.forEach(id => {
    if (!htmlIds.has(id)) {
        missingIds.push(id);
    }
});
console.log('Total IDs queried by JS:', jsQueriedIds.size);
console.log('IDs in HTML:', htmlIds.size);
console.log('IDs queried by JS not found in static HTML:', missingIds);

console.log('\n=== JS MANIPULATED CLASSES IN CSS CHECK ===');
const classesNotInCss = [];
jsManipulatedClasses.forEach(cls => {
    // Check if class exists in CSS or HTML
    const regexInCss = new RegExp(`\\.${cls}[^a-zA-Z0-9_-]`);
    const inCss = regexInCss.test(cssContent);
    const inHtml = htmlClasses.has(cls);
    if (!inCss && !inCss) {
        classesNotInCss.push({ cls, inCss, inHtml });
    }
});
console.log('Total classes manipulated by JS:', jsManipulatedClasses.size);
console.log('Manipulated classes details:');
jsManipulatedClasses.forEach(cls => {
    const inCss = (new RegExp(`\\.${cls}[^a-zA-Z0-9_-]`)).test(cssContent);
    console.log(`  .${cls} -> in CSS: ${inCss}, in HTML: ${htmlClasses.has(cls)}`);
});

console.log('\n=== JS QUERY SELECTORS CHECK ===');
jsQuerySelectors.forEach(sel => {
    console.log('Query selector:', sel);
});
