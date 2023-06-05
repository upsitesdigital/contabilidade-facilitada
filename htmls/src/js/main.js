/*
 * Plugins
 */
import 'slick-carousel';
// import 'selectric';
import svg4everybody from 'svg4everybody';

/**
 * Modules
 */
import slider from './modules/slider';

/**
 * Pages
 */
import home from './pages/_home';


(($) => {
  'use strict';

  $(() => {
    svg4everybody();
    slider();
    home();
  });
})(jQuery);