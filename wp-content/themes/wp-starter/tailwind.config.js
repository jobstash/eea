const themeConfig = require('./theme.json');

module.exports = {
  content: [
    // https://tailwindcss.com/docs/content-configuration
    './*.php',
    './*.twig',
    './inc/**/*.php',
    './template-parts/**/*.php',
    './template-parts/**/*.twig',
    './safelist.txt',
    //'./**/*.php', // recursive search for *.php (be aware on every file change it will go even through /node_modules which can be slow, read doc)
  ],
  safelist: [
    //{
    //  pattern: /text-(white|black)-(200|500|800)/
    //}
  ],
  theme: {
    extend: {
      container: {
        center: true,
      },
      colors: colorMapper(theme('settings.color.palette', themeConfig)),
      fontFamily: {
        sans: ['"Inter"', 'sans-serif'],
      },
      fontSize: fontSizeMapper(
        theme('settings.typography.fontSizes', themeConfig)
      )
    },
  },
  corePlugins: {
    aspectRatio: false,
    container: false,
  },
  plugins: [
    require('@tailwindcss/aspect-ratio'),
  ],
};

function theme(path, theme) {
  return path.split('.').reduce(function(previous, current) {
    return previous ? previous[current] : null;
  }, theme || self);
}

function colorMapper(colors) {
  let result = {};

  colors.forEach(function(color) {
    result['' + color.slug + ''] = color.color;
  });

  return result;
}

function fontSizeMapper(fontSizes) {
  let result = {};

  fontSizes.forEach(function(fontSize) {
    result['' + fontSize.slug + ''] = fontSize.size;
  });

  return result;
}
