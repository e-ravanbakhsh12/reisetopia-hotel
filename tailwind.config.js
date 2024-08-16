/** @type {import('tailwindcss').Config} */
module.exports = {
  prefix: "tw-",
  corePlugins: {
    preflight: false,
  },
  content: [
    // "./**/*.{php,js}",
    "./includes/publics/**/*.{php,js}",
  ],
  theme: {
    container: {
      center: true,
    },
    extend: {
      colors: {},

      fontFamily: {
        sans: ["sanserif", "arial", "sans-serif"],
      },
      fontSize: {},
      boxShadow: {},
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
