/**
 * Parallax
 */
export default function() {

  $('.parallax').addClass('parallax-enabled');

  const $win     = $(window);
  let winHeight  = $win.height();
  const introElm = $('.parallax')[0];
  const scrollFn = function() {
    const st = $win.scrollTop();
    const y =  st * 0.5;

    if (st <= winHeight) {
      introElm.style.backgroundPosition = `50% -${Math.round(y)}px`;
    }
  };

  const frame = function() {
    window.requestAnimationFrame(scrollFn);
  };

  $win.on('scroll', frame).on('resize', function() {
    winHeight = $win.height();
  });
}
