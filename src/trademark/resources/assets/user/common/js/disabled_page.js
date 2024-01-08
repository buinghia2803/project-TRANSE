const form = $('form');
form.find('a, input, button, textarea, select').addClass('disabled')
form.find('a, input, button, textarea, select').prop('disabled', true)
form.find('a').attr('href', '#')
form.find('a').attr('target', '')
$('[type=submit]').remove();
$('#cart').prop('disabled', false);
