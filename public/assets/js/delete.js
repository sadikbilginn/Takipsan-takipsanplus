/*

 Examples :
 <a href="posts/2" data-method="delete" data-token="{{csrf_token()}}">
 - Or, request confirmation in the process -
 <a href="posts/2" data-method="delete" data-token="{{csrf_token()}}" data-confirm="Are you sure?">
 */

(function(window, $, undefined) {

  var Laravel = {
    initialize: function() {
      this.methodLinks = $('a[data-method]');
      this.token = $('a[data-token]');
      this.registerEvents();
    },

    registerEvents: function() {
      this.methodLinks.on('click', this.handleMethod);
    },

    handleMethod: function(e) {
      e.preventDefault()

      var link = $(this)
      var httpMethod = link.data('method').toUpperCase()
      var form

      // If the data-method attribute is not PUT or DELETE,
      // then we don't know what to do. Just ignore.
      if ($.inArray(httpMethod, ['PUT', 'DELETE']) === -1) {
        return false
      }

      Laravel
          .verifyConfirm(link)
          .done(function () {
            form = Laravel.createForm(link)
            form.submit()
          })
    },

    verifyConfirm: function(link) {

      var confirm = new $.Deferred()

      // var userResponse = window.confirm(link.data('confirm'))
      //
      // if (userResponse) {
      //   confirm.resolve(link)
      // } else {
      //   confirm.reject(link);
      // }

        swal.fire({
            buttonsStyling: false,

            html: link.data('confirm'),
            type: "info",

            confirmButtonText: "Evet, sil!",
            confirmButtonClass: "btn btn-sm btn-bold btn-danger",

            showCancelButton: true,
            cancelButtonText: "Hayır, vazgeç",
            cancelButtonClass: "btn btn-sm btn-bold btn-default"
        }).then(function(result) {
            if (result.value) {
                confirm.resolve(link)
            } else if (result.dismiss === 'cancel') {
                confirm.reject(link)
            }
        });

      return confirm.promise()
    },

    createForm: function(link) {
      var form =
          $('<form>', {
            'method': 'POST',
            'action': link.attr('href')
          });

      var token =
          $('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': link.data('token')
          });

      var hiddenInput =
          $('<input>', {
            'name': '_method',
            'type': 'hidden',
            'value': link.data('method')
          });

      return form.append(token, hiddenInput)
          .appendTo('body');
    }
  };

  Laravel.initialize();

})(window, jQuery);