window.hntr = {}

window.hntr.init = function() {

  // Get all inputs with data-hntr="true"
  let inputs = document.querySelectorAll('[type="text"][data-hntr="true"]')

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

  // Get all inputs with data-hntr="true"
  let inputs = document.querySelectorAll('[type="text"][data-hntr="true"]')

  // Add an event listener to each input element
  inputs.forEach(input => {

    // Listen to the keyup event
    input.addEventListener('keyup', function() {

      // Get the hntr list element
      let list = input.nextElementSibling

      // Check if there are 3 or more characters in the input
      if (input.value.length >= 3 && list.classList.contains('show') === false) {
      }
    })
  })
}

document.addEventListener('DOMContentLoaded', function() {
  window.hntr.init()
  window.hntr.eventListeners()
})
