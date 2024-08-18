/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: "tw-",
  corePlugins: {
    preflight: false,
  },
  content: [
    // "./**/*.{php,js}",
    "./includes/publics/**/*.{php,js}",
    "./assets/js/**/*.{php,js}",
  ],
  theme: {
    container: {
      center: true,
    },
    extend: {
      colors: {
        'green-1':'#588f8f',
      },

      fontFamily: {
        sans: ["sanserif", "arial", "sans-serif"],
      },
      fontSize: {},
      boxShadow: {
        input:'0 3px 6px rgba(0,0,0,.2)',
      },
      dropShadow: {},
      borderRadius: {},
      backgroundImage: {},
      gridTemplateRows: {},
    },
  },

  plugins: [
    function ({ addVariant }) {
      addVariant("child", "& > *");
      addVariant("child-hover", "& > *:hover");
    },
    require("tailwindcss-labeled-groups")(["1", "2", "3", "4", "5", "6"]),
    ({ addUtilities }) => {
      addUtilities({
        ".flex-center": {
          display: "flex",
          "align-items": "center",
          "justify-content": "center",
        },
      });
    },
  ],
};
