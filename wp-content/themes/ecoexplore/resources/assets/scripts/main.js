// import external dependencies
import 'jquery';
import 'materialize-css';

// Import everything from autoload
// import "./autoload/**/*"

// import local dependencies
import Router from './util/Router';
import common from './routes/common';
import home from './routes/home';
import user from './routes/user';
import submitNewObservation from './routes/submit';
// import submitTest from './routes/submit-new';
import register from './routes/register';
import registerGroup from './routes/register';

/**
 * Web Font Loader
 */
var WebFont = require('webfontloader');

WebFont.load({
 google: {
   families: ['Montserrat:400,400i,600', 'Zilla+Slab:400,600', 'Material+Icons', 'Boogaloo'],
 },
});

/** Populate Router instance with DOM routes */
const routes = new Router({
  // All pages
  common,
  // Home page
  home,
  // Submit new observation page
  submitNewObservation,
  // submitTest,
  // User profile page
  user,
  // Registration page
  register,
  // Group registration page
  registerGroup,
});

// Load Events
jQuery(document).ready(() => routes.loadEvents());
