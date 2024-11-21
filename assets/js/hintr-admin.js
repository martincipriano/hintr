document.addEventListener('DOMContentLoaded', function() {
  let selectInputs = document.querySelectorAll('.hintr-select')
  selectInputs.forEach(function(selectInput) {
    new SlimSelect({
      select: selectInput,
    })
  })
})
