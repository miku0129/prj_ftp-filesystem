const importWrap = require("postcss-import");

module.exports = {
  plugins: [importWrap({ path: ["node_modules"] })],
};
