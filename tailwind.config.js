/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./public/**/*.html",
    "./public/**/*.js"
  ],
  darkMode: 'class', // Enable dark mode with class strategy
  theme: {
    extend: {},
  },
  plugins: [],
  safelist: [
    // Dark mode classes'ları her zaman dahil et
    'dark',
    'dark:bg-gray-800',
    'dark:bg-gray-900',
    'dark:text-white',
    'dark:text-gray-300',
    'dark:text-gray-400',
    'dark:border-gray-600',
    'dark:border-gray-700',
    'dark:hover:bg-gray-700',
    'dark:hover:text-blue-300',
    'dark:hover:text-blue-400',
    // Spesifik dark mode class'ları
    'dark:bg-gray-50',
    'dark:bg-gray-100',
    'dark:bg-gray-200',
    'dark:text-gray-100',
    'dark:text-gray-200',
    'dark:text-gray-500',
    'dark:text-gray-600',
    'dark:text-gray-700',
    'dark:border-gray-100',
    'dark:border-gray-200',
    'dark:border-gray-300',
    'dark:border-gray-400',
    'dark:border-gray-500',
    'dark:hover:bg-gray-100',
    'dark:hover:bg-gray-600',
    'dark:hover:bg-gray-800',
    'dark:hover:text-gray-200',
    'dark:hover:text-gray-400'
  ]
}