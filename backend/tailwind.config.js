import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Super AMOLED True Black Backgrounds
                "amoled-black": "#000000",
                "surface-dark": "#090E08",

                // Material 3 Green Palette
                primary: {
                    DEFAULT: "#146C2E", // Vibrant M3 Green (Light Mode)
                    dark: "#3BC05B", // Lighter Green (Dark OLED Mode)
                    50: "#f0fdf4",
                    100: "#dcfce7",
                    200: "#bbf7d0",
                    300: "#86efac",
                    400: "#4ade80",
                    500: "#22c55e",
                    600: "#146C2E",
                    700: "#15803d",
                    800: "#166534",
                    900: "#14532d",
                    950: "#052e16",
                },
                "on-primary": {
                    DEFAULT: "#FFFFFF",
                    dark: "#003911",
                },
                "primary-container": {
                    DEFAULT: "#A6FBAA",
                    dark: "#00531E",
                },
                "on-primary-container": {
                    DEFAULT: "#002106",
                    dark: "#A6FBAA",
                },
                surface: {
                    DEFAULT: "#FAF9F6",
                    variant: "#DFE4D7",
                },
                "on-surface": {
                    DEFAULT: "#1A1C19",
                    variant: "#43483E",
                    dark: "#E2E3DD",
                },
                outline: {
                    DEFAULT: "#73796E",
                    dark: "#8D9387",
                },
            },
            borderRadius: {
                m3: "2rem",
            },
            animation: {
                blob: "blob 7s infinite",
            },
            keyframes: {
                blob: {
                    "0%": {
                        transform: "translate(0px, 0px) scale(1)",
                    },
                    "33%": {
                        transform: "translate(30px, -50px) scale(1.08)",
                    },
                    "66%": {
                        transform: "translate(-20px, 20px) scale(0.95)",
                    },
                    "100%": {
                        transform: "translate(0px, 0px) scale(1)",
                    },
                },
            },
        },
    },
    plugins: [],
};
