window.hintr = {}

window.hintr.init = function() {

  let inputs = document.querySelectorAll('[type="text"][data-hintr], [type="search"][data-hintr], [name=s]')

  inputs.forEach(input => {
    let element = document.createElement('ul')
    let rect = input.getBoundingClientRect()

    element.classList.add('hintr')
    element.style.top = rect.top + input.offsetHeight + 'px'
    element.style.left = rect.left + 'px'
    element.style.width = input.offsetWidth + 'px'

    input.parentNode.insertBefore(element, input.nextSibling)
  })
}

window.hintr.toggleSuggestions = function(e) {
}

window.hintr.eventListeners = function() {
}

document.addEventListener('DOMContentLoaded', function() {
  window.hintr.init()
  window.hintr.eventListeners()
})
