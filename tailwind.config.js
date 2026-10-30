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
        'ink': '#0A0A0B',
        telyuji: {
          red: '#ED1E28',
          flame: '#F26A2D',
          jasmine: '#F7D488',
          cream: '#EAEFB1',
          mint: '#E9F7CA',
          ink: '#0A0A0B',
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Consolas', 'monospace'],
      },
      transitionTimingFunction: {
        'tu': 'cubic-bezier(0.2, 0.7, 0.2, 1)',
        'tu-io': 'cubic-bezier(0.7, 0, 0.2, 1)',
      },
      boxShadow: {
        'tu-auth': '0 30px 70px -40px rgba(10, 10, 11, 0.35)',
        'tu-float': '0 28px 60px -28px rgba(10, 10, 11, 0.45)',
      },
    },
  },
  plugins: [],
}
