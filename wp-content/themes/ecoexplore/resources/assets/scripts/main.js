// import external dependencies
import 'jquery';
import 'materialize-css';

// Import everything from autoload
import "./autoload/**/*"

// import local dependencies
import Router from './util/Router';
import common from './routes/common';
import home from './routes/home';
import aboutUs from './routes/about';

/**
 * Web Font Loader
 */
var WebFont = require('webfontloader');

WebFont.load({
 google: {
   families: ['Montserrat:400,400i,700', 'Zilla+Slab:400,700'],
 },
});

/** Populate Router instance with DOM routes */
const routes = new Router({
  // All pages
  common,
  // Home page
  home,
  // About Us page, note the change from about-us to aboutUs.
  aboutUs,
});

// Load Events
jQuery(document).ready(() => routes.loadEvents());
