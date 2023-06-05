import 'bootstrap/js/tab';
import 'magnific-popup';
import './jquery.toggleFieldClass';
import 'textarea-autosize';
// import formMasks      from './form-masks';
// import formValidators from './form-validators';

/**
 * Form
 */
export default function() {
  // formMasks();
  // formValidators();
  $('textarea').textareaAutoSize();
  $('.focus').each(function() {
    if ($(this).val() == '') {
      $(this).closest('label').addClass('clear-field');
    }
  });

  $('.focus').focusin(function() {
    $(this).closest('label').addClass('focus');
  }).focusout(function() {
    $(this).closest('label').removeClass('focus');
    if ($(this).val() == '') {
      $(this).closest('label').addClass('clear-field');
    } else {
      $(this).closest('label').removeClass('clear-field');
    }

  });
}
