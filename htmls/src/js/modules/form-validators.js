/**
 * Validators
 */
export default function() {
  $.validator.addMethod('cpfValidator', function(value) {
    var $return = true;

    // this is mostly not needed
    var invalidos = [
      '111.111.111-11',
      '222.222.222-22',
      '333.333.333-33',
      '444.444.444-44',
      '555.555.555-55',
      '666.666.666-66',
      '777.777.777-77',
      '888.888.888-88',
      '999.999.999-99',
      '000.000.000-00'
    ];

    for (let i = 0; i < invalidos.length; i++) {
      if (invalidos[i] == value) {
        $return = false;
      }
    }

    value = value.replace('-', '');
    value = value.replace(/\./g, '');

    // validando primeiro digito
    var add1st = 0;
    for (let i = 0; i < 9; i++) {
      add1st += parseInt(value.charAt(i), 10) * (10 - i);
    }
    var rev = 11 - (add1st % 11);
    if (rev == 10 || rev == 11) {
      rev = 0;
    }
    if (rev != parseInt(value.charAt(9), 10)) {
      $return = false;
    }

    // validando segundo digito
    var add2nd = 0;
    for (let i = 0; i < 10; i++) {
      add2nd += parseInt(value.charAt(i), 10) * (11 - i);
    }
    rev = 11 - (add2nd % 11);
    if (rev == 10 || rev == 11) {
      rev = 0;
    }
    if (rev != parseInt(value.charAt(10), 10)) {
      $return = false;
    }

    return $return;
  });

  $.validator.addMethod('emailValidator', function(value) {
    var emailRE = /^[\w\.%\+\-]+@(?:[A-Z0-9\-]+\.)+(?:[A-Z]{2,6})$/i;
    var fazendaRE = /@fazenda.sp.gov.br$/i;
    var val = $.trim(value);

    return emailRE.test(val) && !fazendaRE.test(val);
  });

  $.validator.addMethod('statusValidator', function(value) {
    var toggleVal = $('[name="status-toggle"]:checked').val();

    return (toggleVal === 'Ativo' && value !== 'Aposentado');
  });

  $.validator.addMethod('equalToIgnoreCase', function(value, element, param) {
    return this.optional(element) || (value.toLowerCase() == $(param).val().toLowerCase());
  });

  $.validator.addMethod('telValidator', function(value) {
    return /\(\d{2}\)\s\d{4,5}\-\d{4}/.test(value);
  });

  $.validator.addMethod('date', function(value, element) {
    var check = false;
    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/;

    if ( re.test(value) ) {
      var adata = value.split('/');
      var gg = parseInt(adata[0], 10);
      var mm = parseInt(adata[1], 10);
      var aaaa = parseInt(adata[2], 10);
      var xdata = new Date(aaaa, mm - 1, gg);

      if ( ( xdata.getFullYear() == aaaa ) && ( xdata.getMonth() == mm - 1 ) && ( xdata.getDate() == gg ) ) {
        check = true;
      } else {
        check = false;
      }
    } else {
      check = false;
    }

    return this.optional(element) || check;
  }, 'Insira uma data válida');
}