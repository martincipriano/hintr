window.hntr = {}

window.hntr.init = function() {

  // Get all inputs with data-hntr="true"
  let inputs = document.querySelectorAll('[data-hntr="true"]')

  // Add an element to the DOM for each input
  inputs.forEach(input => {
    let element = document.createElement('div')

    element.classList.add('hntr')

    input.parentNode.insertBefore(element, input.nextSibling)
  })
}

window.hntr.eventListeners = function() {
  
}

document.addEventListener('DOMContentLoaded', function() {
  window.hntr.init()
  window.hntr.eventListeners()
})