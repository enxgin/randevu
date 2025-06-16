const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const tailwindcss = require('tailwindcss');
const autoprefixer = require('autoprefixer');

async function buildCSS() {
  try {
    // Input CSS dosyasını oku
    const inputCSS = fs.readFileSync('./public/assets/css/input.css', 'utf8');
    
    // PostCSS ile işle
    const result = await postcss([
      tailwindcss('./tailwind.config.js'),
      autoprefixer
    ]).process(inputCSS, {
      from: './public/assets/css/input.css',
      to: './public/assets/css/output.css'
    });
    
    // Output CSS dosyasını yaz
    fs.writeFileSync('./public/assets/css/output.css', result.css);
    
    console.log('✅ CSS build tamamlandı!');
    console.log('📁 Çıktı: ./public/assets/css/output.css');
    
    if (result.map) {
      fs.writeFileSync('./public/assets/css/output.css.map', result.map.toString());
      console.log('🗺️  Source map oluşturuldu');
    }
    
  } catch (error) {
    console.error('❌ CSS build hatası:', error);
    process.exit(1);
  }
}

buildCSS();