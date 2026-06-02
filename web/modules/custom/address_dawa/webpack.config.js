const path = require('path');

module.exports = {
  mode: 'production',
  entry: './assets/js/apply-adressevaelger.js',
  output: {
    filename: 'widget.bundle.js',
    path: path.resolve(__dirname, 'js'),
  },
  // Keep the committed bundle readable in git diffs. Webpack's production
  // mode still applies tree-shaking, scope hoisting, and dead-code removal —
  // we just skip the final minification pass.
  optimization: {
    minimize: false,
  },
  performance: {
    hints: false,
  },
};
