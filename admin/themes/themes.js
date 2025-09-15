

$(".create-theme").click(function(){
    $(".build-new-theme").show();
});

document.querySelectorAll('.file-upload-input').forEach(inputElement => {
    inputElement.addEventListener('change', function() {
      const fileName = this.files[0] ? this.files[0].name : '';
      const label = this.nextElementSibling;
      label.querySelector('.file-upload-text').textContent = fileName ? fileName : 'Upload File';
    });
  });