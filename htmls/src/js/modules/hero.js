/**
 * Hero
 */
export default function() {
  const $hero = $('.hero');

  if ($hero.find('.hero-item').length > 1) {
    $hero.find('.hero-slides').slick({
      dots: true,
      appendArrows: $hero.find('.hero-nav'),
      appendDots: $hero.find('.hero-nav').find('.dots'),
    });
  }
}
