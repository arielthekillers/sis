/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors');

module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./Modules/**/*.php",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        slate: colors.zinc,
      }
    },
  },
  plugins: [],
}
