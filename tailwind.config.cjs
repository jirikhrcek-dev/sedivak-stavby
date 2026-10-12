/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{astro,js,ts}"],
  theme: {
    extend: {
      fontFamily: {
        display: ["'Barlow Condensed'", "sans-serif"],
        body: ["'DM Sans'", "sans-serif"],
      },
      colors: {
        stone: {
          950: "#0c0a09",
        },
        // Znackova zlata. Odstiny jsou vybrane podle kontrastu, ne od oka:
        //   400 - zlata z polepu auta, na tmavem podkladu 9,5:1
        //   500 - vyplne tlacitek, jen s TMAVYM textem (5,7:1)
        //   700 - text a odkazy na svetlem podkladu, 6,5:1
        // Bily text na zlate nikdy - vyjde 3,1:1 a neprojde.
        brand: {
          300: "#E2D3A0",
          400: "#C9B365",
          500: "#A8913F",
          600: "#8A7530",
          700: "#6E5C25",
          800: "#57491C",
        },
      },
    },
  },
  plugins: [],
};
