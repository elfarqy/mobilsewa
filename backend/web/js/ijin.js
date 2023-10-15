$(document).ready(function () {
   var listOfValue = [];
   $('#permissiontransportform-approvals').select2({
      multiple: true,
      width: "100%",
      allowClear: true,
      ajax: {
         url: $('#permissiontransportform-approvals').attr('data-url'),
         data: function (params) {
            return {
               q: params.term, // search term
               page: params.page
            };
         },
      },
   });

   $( "#btnSubmit" ).click(function(e) {
      e.preventDefault();

      let select2Data = $('#permissiontransportform-approvals').select2('data');

      $('#permissiontransportform-tmpval').val(JSON.stringify(select2Data));
      $( "#w0" ).submit();
   });
});