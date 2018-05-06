$('#formData').submit(function(){
   $.post('src/handler.php', $('#formData').serialize(), function(data){
      $('#formResult').html(data);
      $('#formData').find("input[type=text], select").val("");
   });
   return false;
});