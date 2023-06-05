/**
 * Form masks
 */
export default function() {
  $('#cpf, #cpfnaoassoc').mask('000.000.000-00');
  $('#associado').mask('000000000');
  $('#datanasc').mask('00/00/0000');
  $('#rg').mask('000000000000000');

  var celMaskBehavior = function(val) {
    return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
  };

  $('#cel, #telefone').mask(celMaskBehavior, {
    onKeyPress: function(val, e, field, options) {
      field.mask(celMaskBehavior.apply({}, arguments), options);
    }
  });
}