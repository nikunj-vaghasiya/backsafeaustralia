const {
  spacing
} = require('tailwindcss/defaultTheme');

const colors = require('tailwindcss/colors');

const hyvaModules = require('@hyva-themes/hyva-modules');

module.exports = hyvaModules.mergeTailwindConfig({
  theme: {
    extend: {
      screens: {
        'xs': '480px',
        // => @media (min-width: 480px) { ... }
        'sm': '640px',
        // => @media (min-width: 640px) { ... }
        'md': '768px',
        // => @media (min-width: 768px) { ... }
        'lg': '1024px',
        // => @media (min-width: 1024px) { ... }
        'xl': '1280px',
        // => @media (min-width: 1280px) { ... }
        '2xl': '1536px' // => @media (min-width: 1536px) { ... }
      },
      fontFamily: {
        sans: ["Segoe UI", "Helvetica Neue", "Arial", "sans-serif"]
      },
      colors: {
        primary: {
          lighter: colors.blue['300'],
          "DEFAULT": colors.blue['800'],
          darker: colors.blue['900']
        },
        secondary: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        background: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        green: colors.emerald,
        yellow: colors.amber,
        purple: colors.violet
      },
      textColor: {
        orange: colors.orange,
        red: { ...colors.red,
          "DEFAULT": colors.red['500']
        },
        primary: {
          lighter: colors.gray['700'],
          "DEFAULT": colors.gray['800'],
          darker: colors.gray['900']
        },
        secondary: {
          lighter: colors.gray['400'],
          "DEFAULT": colors.gray['600'],
          darker: colors.gray['800']
        }
      },
      backgroundColor: {
        primary: {
          lighter: colors.blue['600'],
          "DEFAULT": colors.blue['700'],
          darker: colors.blue['800']
        },
        secondary: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        container: {
          lighter: colors.white,
          "DEFAULT": colors.neutral['50'],
          darker: colors.neutral['100']
        }
      },
      borderColor: {
        primary: {
          lighter: colors.blue['600'],
          "DEFAULT": colors.blue['700'],
          darker: colors.blue['800']
        },
        secondary: {
          lighter: colors.blue['100'],
          "DEFAULT": colors.blue['200'],
          darker: colors.blue['300']
        },
        container: {
          lighter: colors.neutral['100'],
          "DEFAULT": '#e7e7e7',
          darker: '#b6b6b6'
        }
      },
      minHeight: {
        a11y: spacing["11"],
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh'
      },
      maxHeight: {
        'screen-25': '25vh',
        'screen-50': '50vh',
        'screen-75': '75vh'
      },
      container: {
        center: true,
        padding: spacing["6"]
      }
    }
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
  // Examples for excluding patterns from purge
  content: [
    // Child theme .phtml and layout files
    '../../**/*.phtml',
    '../../*/layout/*.xml',
    '../../*/page_layout/override/base/*.xml',

    // Parent Hyvä theme files in vendor
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/**/*.phtml',
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/*/layout/*.xml',
    '../../../../../../../vendor/hyva-themes/magento2-default-theme/*/page_layout/override/base/*.xml',

    // Custom modules (if you use Tailwind classes in .phtml files from modules)
    '../../../../../../../app/code/**/*.phtml',

    // Optional: Vendor modules if they include Hyvä-compatible templates
    '../../../../../../../vendor/**/*.phtml',
  ],
});
