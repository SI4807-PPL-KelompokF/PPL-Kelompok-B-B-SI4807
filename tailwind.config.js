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
        sans: ['"General Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'sans-serif'],
        mono: ['"General Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'sans-serif'],
      },
      letterSpacing: {
        tighter: '-0.02em',
        tight: '-0.015em',
      },
      transitionTimingFunction: {
        'tu': 'cubic-bezier(0.2, 0.7, 0.2, 1)',
        'tu-io': 'cubic-bezier(0.7, 0, 0.2, 1)',
      },
      boxShadow: {
        'tu-auth': '0 1px 3px 0 rgba(10, 10, 11, 0.06)',
        'tu-float': '0 1px 3px 0 rgba(10, 10, 11, 0.08)',
      },
    },
  },
  plugins: [],
}
