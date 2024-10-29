window.hntr = {}

window.hntr.init = function() {

  // Get all inputs with data-hntr="true"
  let inputs = document.querySelectorAll('[data-hntr="true"]')

  // Add an element to the DOM for each input
  inputs.forEach(input => {

    // Create am unordered list
    let element = document.createElement('ul')

    // Add a class to the unordered list
    element.classList.add('hntr')

    // Add the unordered list after the input element
    input.parentNode.insertBefore(element, input.nextSibling)
  })
}

window.hntr.eventListeners = function() {
  
}

document.addEventListener('DOMContentLoaded', function() {
  window.hntr.init()
  window.hntr.eventListeners()
})