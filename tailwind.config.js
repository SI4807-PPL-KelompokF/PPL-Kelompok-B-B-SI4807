/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'racing-red': '#ED1E28',
        'tiger-flame': '#F26A2D',
        'jasmine': '#F7D488',
        'cream': '#EAEFB1',
        'frosted-mint': '#E9F7CA',
        telyuji: {
          red: '#ED1E28',
          flame: '#F26A2D',
          jasmine: '#F7D488',
          cream: '#EAEFB1',
          mint: '#E9F7CA',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
